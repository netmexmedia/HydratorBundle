<?php

namespace Netmex\HydratorBundle\Tests\Mapper;

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
    public function testBuildTransformsAndDenormalizesSuccessfully(): void
    {
        $builder = $this->createMock(BuilderInterface::class);
        $optionsResolver = $this->createMock(OptionsResolver::class);
        $mapperFactory = $this->createMock(MapperFactory::class);
        $mapperResolver = $this->createMock(MapperResolver::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $serializer = $this->createMock(DenormalizerInterface::class);

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
        $mapperInstance = new \stdClass();
        $mapperResolver->method('resolve')->with($this->isType('string'), $builder, $optionsResolver)->willReturn($mapperInstance);

        // Factory should receive the mapper instance and input data and return our prepared definition
        $mapperFactory->method('create')->with($mapperInstance, ['field1' => 'raw1', 'field2' => 'raw2'], $target)->willReturn($mapperDefinition);

        // Validator expectations
        $validator->expects($this->once())->method('resetErrors');
        // validate called 4 times: input+output for each field
        $validator->expects($this->exactly(4))->method('validate');
        $validator->method('hasErrors')->willReturn(false);

        // Serializer should be called with transformed data
        $serializer->expects($this->once())
            ->method('denormalize')
            ->with(
                ['field1' => 'trans1', 'field2' => 'trans2'],
                $modelClass,
                null,
                [AbstractNormalizer::OBJECT_TO_POPULATE => $target]
            )
            ->willReturn($expectedOutput);

        $mapper = new Mapper($builder, $optionsResolver, $mapperFactory, $mapperResolver, $validator, $serializer);

        $result = $mapper->build('Some\\MapperClass', ['field1' => 'raw1', 'field2' => 'raw2'], $target);

        $this->assertSame($expectedOutput, $result);
    }

    public function testBuildThrowsMappingValidationExceptionWhenValidatorHasErrors(): void
    {
        $builder = $this->createMock(BuilderInterface::class);
        $optionsResolver = $this->createMock(OptionsResolver::class);
        $mapperFactory = $this->createMock(MapperFactory::class);
        $mapperResolver = $this->createMock(MapperResolver::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $serializer = $this->createMock(DenormalizerInterface::class);

        $transformer = $this->createMock(TransformerInterface::class);
        $transformer->method('transform')->willReturn('trans');

        $field = new FieldDefinition('name', $transformer, 'value', []);

        $mapperDefinition = new MapperDefinition(\stdClass::class, new FieldCollection([$field]), null);

        $mapperResolver->method('resolve')->willReturn(new \stdClass());
        $mapperFactory->method('create')->willReturn($mapperDefinition);

        $validator->expects($this->once())->method('resetErrors');
        $validator->expects($this->exactly(2))->method('validate'); // input + output
        $validator->method('hasErrors')->willReturn(true);
        $validator->method('getErrors')->willReturn(['some_error']);

        $mapper = new Mapper($builder, $optionsResolver, $mapperFactory, $mapperResolver, $validator, $serializer);

        $this->expectException(MappingValidationException::class);

        $mapper->build('Some\\MapperClass', ['name' => 'value']);
    }
}
