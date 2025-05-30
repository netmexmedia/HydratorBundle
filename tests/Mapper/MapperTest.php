<?php

namespace Netmex\HydratorBundle\Tests\Mapper;

use Netmex\HydratorBundle\Mapper\FieldCollection;
use Netmex\HydratorBundle\Mapper\FieldDefinition;
use Netmex\HydratorBundle\Mapper\MapperDefinition;
use PHPUnit\Framework\TestCase;
use Netmex\HydratorBundle\Mapper\Mapper;
use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Options\OptionsResolver;
use Netmex\HydratorBundle\Factory\MapperFactory;
use Netmex\HydratorBundle\Mapper\MapperResolver;
use Netmex\HydratorBundle\Contracts\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Netmex\HydratorBundle\Exception\MappingValidationException;

class MapperTest extends TestCase
{
    private const FIELD_NAME = 'fieldName';

    private BuilderInterface $builder;
    private OptionsResolver $resolver;
    private MapperFactory $factory;
    private MapperResolver $mapperResolver;
    private ValidatorInterface $validator;
    private DenormalizerInterface $serializer;
    private Mapper $mapper;

    protected function setUp(): void
    {
        $this->builder = $this->createMock(BuilderInterface::class);
        $this->resolver = $this->createMock(OptionsResolver::class);
        $this->factory = $this->createMock(MapperFactory::class);
        $this->mapperResolver = $this->createMock(MapperResolver::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->serializer = $this->createMock(DenormalizerInterface::class);

        $this->mapper = new Mapper(
            $this->builder,
            $this->resolver,
            $this->factory,
            $this->mapperResolver,
            $this->validator,
            $this->serializer
        );
    }

    private function createFieldDefinitionMock(): FieldDefinition
    {
        $fieldMock = $this->createMock(FieldDefinition::class);
        $fieldMock->method('getValue')->willReturn('some value');
        $fieldMock->method('getName')->willReturn(self::FIELD_NAME);

        $fieldMock->name = self::FIELD_NAME; // Add this line to fix typed property error

        $fieldMock->expects($this->once())->method('transform');

        return $fieldMock;
    }

    private function createFieldCollectionWith(FieldDefinition $fieldDefinition): FieldCollection
    {
        return new FieldCollection([$fieldDefinition]);
    }

    private function createMapperDefinitionMockWithFields(FieldCollection $fieldCollection): MapperDefinition
    {
        $mapperDefinitionMock = $this->createMock(MapperDefinition::class);
        $mapperDefinitionMock->method('getFields')->willReturn($fieldCollection);
        $mapperDefinitionMock->method('getModel')->willReturn(\stdClass::class);

        return $mapperDefinitionMock;
    }

    public function testBuildCallsMapperResolver(): void
    {
        $mapperInstance = $this->createMock(DummyMapperClass::class);
        $this->mapperResolver->expects($this->once())
            ->method('resolve')
            ->with(DummyMapperClass::class)
            ->willReturn($mapperInstance);

        $this->factory->method('create')->willReturn($this->createMock(MapperDefinition::class));
        $this->validator->method('hasErrors')->willReturn(false);
        $this->serializer->method('denormalize')->willReturn(new \stdClass());

        $this->mapper->build(DummyMapperClass::class, []);
    }

    public function testBuildValidatesFields(): void
    {
        $fieldMock = $this->createFieldDefinitionMock();
        $fieldCollection = $this->createFieldCollectionWith($fieldMock);
        $mapperDefinitionMock = $this->createMapperDefinitionMockWithFields($fieldCollection);

        $this->factory->method('create')->willReturn($mapperDefinitionMock);
        $this->mapperResolver->method('resolve')->willReturn($this->createMock(DummyMapperClass::class));

        $this->validator->expects($this->exactly(2))
            ->method('validate')
            ->with($fieldMock, $this->isType('string'));

        $this->validator->method('hasErrors')->willReturn(false);
        $this->serializer->method('denormalize')->willReturn(new \stdClass());

        $this->mapper->build(DummyMapperClass::class, [self::FIELD_NAME => 'value']);
    }

    public function testBuildReturnsHydratedObject(): void
    {
        $fieldDefinitionMock = $this->createFieldDefinitionMock();

        $fieldCollectionMock = $this->createMock(FieldCollection::class);
        $fieldCollectionMock->method('count')->willReturn(1);
        $fieldCollectionMock->method('get')->willReturn($fieldDefinitionMock);
        $fieldCollectionMock->method('getIterator')->willReturn(new \ArrayIterator([$fieldDefinitionMock]));

        $mapperDefinitionMock = $this->createMapperDefinitionMockWithFields($fieldCollectionMock);

        $this->factory->method('create')->willReturn($mapperDefinitionMock);
        $this->mapperResolver->method('resolve')->willReturn($this->createMock(DummyMapperClass::class));
        $this->validator->method('hasErrors')->willReturn(false);

        $expectedObject = new \stdClass();
        $this->serializer->method('denormalize')->willReturn($expectedObject);

        $result = $this->mapper->build(DummyMapperClass::class, []);
        $this->assertSame($expectedObject, $result);
    }

    public function testBuildThrowsExceptionOnValidationError(): void
    {
        $fieldDefinitionMock = $this->createFieldDefinitionMock();

        $fieldCollectionMock = $this->createMock(FieldCollection::class);
        $fieldCollectionMock->method('count')->willReturn(1);
        $fieldCollectionMock->method('get')->willReturn($fieldDefinitionMock);
        $fieldCollectionMock->method('getIterator')->willReturn(new \ArrayIterator([$fieldDefinitionMock]));

        $mapperDefinitionMock = $this->createMapperDefinitionMockWithFields($fieldCollectionMock);

        $this->factory->method('create')->willReturn($mapperDefinitionMock);
        $this->mapperResolver->method('resolve')->willReturn($this->createMock(DummyMapperClass::class));

        $this->validator->method('hasErrors')->willReturn(true);
        $this->validator->method('getErrors')->willReturn([
            self::FIELD_NAME => ['error'],
        ]);

        $this->expectException(MappingValidationException::class);

        $this->mapper->build(DummyMapperClass::class, []);
    }
}
