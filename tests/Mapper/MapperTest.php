<?php

namespace Netmex\HydratorBundle\Tests\Mapper;

use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Factory\MapperFactory;
use Netmex\HydratorBundle\Mapper\Mapper;
use Netmex\HydratorBundle\Exception\MappingValidationException;
use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\ValidatorInterface;
use Netmex\HydratorBundle\Options\OptionsResolver;
use Netmex\HydratorBundle\Mapper\MapperDefinition;
use Netmex\HydratorBundle\Mapper\FieldDefinition;
use Netmex\HydratorBundle\Mapper\MapperResolver;
use Netmex\HydratorBundle\Mapper\FieldCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Netmex\HydratorBundle\Contracts\TransformerInterface;

class MapperTest extends TestCase
{
    private BuilderInterface $builder;
    private OptionsResolver $optionsResolver;
    private MapperFactory $mapperFactory;
    private MapperResolver $mapperResolver;
    private ValidatorInterface $validator;
    private DenormalizerInterface $serializer;
    private MapperDefinitionInterface $mapperDefinition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = $this->createMock(BuilderInterface::class);
        $this->optionsResolver = $this->createMock(OptionsResolver::class);
        $this->mapperFactory = $this->createMock(MapperFactory::class);
        $this->mapperResolver = $this->createMock(MapperResolver::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->serializer = $this->createMock(DenormalizerInterface::class);
        $this->mapperDefinition = $this->createMock(MapperDefinitionInterface::class);
    }

    public function testBuildTransformsAndDenormalizesSuccessfully(): void
    {
        $target = new \stdClass();
        $modelClass = \stdClass::class;
        $expectedOutput = new \stdClass();

        // Create transformers that will return transformed values
        $transformer1 = $this->createMock(TransformerInterface::class);
        $transformer1->method('transform')->willReturn('trans1');

        $transformer2 = $this->createMock(TransformerInterface::class);
        $transformer2->method('transform')->willReturn('trans2');

        // Create FieldDefinition instances with initial raw values
        $field1 = new FieldDefinition('field1', $transformer1, 'raw1', []);
        $field2 = new FieldDefinition('field2', $transformer2, 'raw2', []);

        $mapperDefinition = new MapperDefinition($modelClass, new FieldCollection([$field1, $field2]), $target);

        // mapperResolver returns some mapper object (not used directly by Mapper except passed to factory)
        $this->mapperResolver->method('resolve')->with($this->isType('string'), $this->builder, $this->optionsResolver)->willReturn($this->mapperDefinition);

        // Factory should receive the mapper instance and input data and return our prepared definition
        $this->mapperFactory->method('create')->with($this->mapperDefinition, ['field1' => 'raw1', 'field2' => 'raw2'], $target)->willReturn($mapperDefinition);

        // Validator expectations
        $this->validator->expects($this->once())->method('resetErrors');
        // validate called 4 times: input+output for each field
        $this->validator->expects($this->exactly(4))->method('validate');
        $this->validator->method('hasErrors')->willReturn(false);

        // Serializer should be called with transformed data
        $this->serializer->expects($this->once())
            ->method('denormalize')
            ->with(
                ['field1' => 'trans1', 'field2' => 'trans2'],
                $modelClass,
                null,
                [AbstractNormalizer::OBJECT_TO_POPULATE => $target]
            )
            ->willReturn($expectedOutput);

        $mapper = new Mapper($this->builder, $this->optionsResolver, $this->mapperFactory, $this->mapperResolver, $this->validator, $this->serializer);

        $result = $mapper->build('Some\\MapperClass', ['field1' => 'raw1', 'field2' => 'raw2'], $target);

        $this->assertSame($expectedOutput, $result);
    }

    public function testBuildThrowsMappingValidationExceptionWhenValidatorHasErrors(): void
    {
        $transformer = $this->createMock(TransformerInterface::class);
        $transformer->method('transform')->willReturn('trans');

        $field = new FieldDefinition('name', $transformer, 'value', []);

        $mapperDefinition = new MapperDefinition(\stdClass::class, new FieldCollection([$field]), null);

        $this->mapperResolver->method('resolve')->willReturn($this->mapperDefinition);
        $this->mapperFactory->method('create')->willReturn($mapperDefinition);

        $this->validator->expects($this->once())->method('resetErrors');
        $this->validator->expects($this->exactly(2))->method('validate'); // input + output
        $this->validator->method('hasErrors')->willReturn(true);
        $this->validator->method('getErrors')->willReturn([
            [
                'context' => 'input',
                'property' => 'name',
                'message' => 'This field is invalid.',
            ],
        ]);

        $mapper = new Mapper($this->builder, $this->optionsResolver, $this->mapperFactory, $this->mapperResolver, $this->validator, $this->serializer);

        $this->expectException(MappingValidationException::class);

        $mapper->build('Some\\MapperClass', ['name' => 'value']);
    }

    public function testBuildMapsNestedMapperDefinition(): void
    {
        $target = new \stdClass();
        $nestedTarget = new \stdClass();

        $parentModel = \stdClass::class;
        $nestedModel = \stdClass::class;

        $nestedObject = new \stdClass();
        $nestedObject->city = 'Amsterdam';

        // The nested mapper's field.
        $nestedField = new FieldDefinition(
            'city',
            null,
            'Amsterdam',
            []
        );

        $nestedRuntimeDefinition = new MapperDefinition(
            $nestedModel,
            new FieldCollection([$nestedField]),
            $nestedTarget
        );

        // The parent field uses a nested mapper definition.
        $nestedMapperDefinition = $this->createMock(
            MapperDefinitionInterface::class
        );

        $parentField = new FieldDefinition(
            'address',
            $nestedMapperDefinition,
            ['city' => 'Amsterdam'],
            []
        );

        $parentRuntimeDefinition = new MapperDefinition(
            $parentModel,
            new FieldCollection([$parentField]),
            $target
        );

        $this->mapperResolver->expects($this->once())->method('resolve')->willReturn($this->mapperDefinition);

        $this->mapperFactory->expects($this->exactly(2))->method('create')
            ->willReturnOnConsecutiveCalls(
                $parentRuntimeDefinition,
                $nestedRuntimeDefinition
            );

        $this->validator->expects($this->once())->method('resetErrors');

        $this->validator->expects($this->exactly(4))->method('validate');

        $this->validator->method('hasErrors')->willReturn(false);

        // The first denormalization creates the nested object;
        // the second creates the parent object.
        $this->serializer->expects($this->exactly(2))->method('denormalize')
            ->willReturnOnConsecutiveCalls(
                $nestedObject,
                $target
            );

        $mapper = new Mapper($this->builder, $this->optionsResolver, $this->mapperFactory, $this->mapperResolver, $this->validator, $this->serializer);

        $result = $mapper->build(
            'Some\\MapperClass',
            [
                'address' => [
                    'city' => 'Amsterdam',
                ],
            ],
            $target
        );

        $this->assertSame($target, $result);
    }
}
