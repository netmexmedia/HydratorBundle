<?php

namespace Netmex\HydratorBundle\Tests\Factory;

use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Factory\MapperFactory;
use Netmex\HydratorBundle\Mapper\MapperDefinition;
use Netmex\HydratorBundle\Options\OptionsResolver;
use Netmex\HydratorBundle\Transformer\CapitalizationTransformer;
use PHPUnit\Framework\TestCase;

class MapperFactoryTest extends TestCase
{
    private $mockBuilder;
    private $mockResolver;
    private $factory;
    private $mapper;

    protected function setUp(): void
    {
        $this->mockBuilder = $this->createMock(BuilderInterface::class);
        $this->mockResolver = $this->createMock(OptionsResolver::class);
        $this->factory = new MapperFactory($this->mockBuilder, $this->mockResolver);

        $this->mapper = $this->getMockBuilder(MapperDefinitionInterface::class)
            ->onlyMethods(['process', 'options'])
            ->getMock();
    }

    public function testProcessIsCalledOnMapper(): void
    {
        $this->mapper->expects($this->once())->method('process')->with($this->mockBuilder);
        $this->mapper->expects($this->once())->method('options')->with($this->mockResolver);

        // Setup needed for factory create method to run without errors
        $this->mockBuilder->method('getFields')->willReturn([]);
        $this->mockResolver->method('resolve')->willReturn(['model' => 'App\Model\User', 'fields' => []]);

        $this->factory->create($this->mapper, []);
    }

    public function testCreateReturnsMapperDefinitionInstance(): void
    {
        $this->mockBuilder->method('getFields')->willReturn([]);
        $this->mockResolver->method('resolve')->willReturn(['model' => 'App\Model\User', 'fields' => []]);

        $result = $this->factory->create($this->mapper, []);

        $this->assertInstanceOf(MapperDefinition::class, $result);
    }

    public function testCreatedMapperHasCorrectFields(): void
    {
        $fieldsDefinition = [
            'name' => [
                'Transformer' => CapitalizationTransformer::class,
                'constraints' => ['required']
            ]
        ];

        $this->mockBuilder->method('getFields')->willReturn($fieldsDefinition);
        $this->mockResolver->method('resolve')->willReturn([
            'model' => 'App\Model\User',
            'fields' => $fieldsDefinition
        ]);

        $result = $this->factory->create($this->mapper, ['name' => 'John']);

        $fieldCollection = $result->getFields();
        $this->assertCount(1, $fieldCollection);

        $field = $fieldCollection->get('name');
        $this->assertEquals('name', $field->getName());
        $this->assertInstanceOf(CapitalizationTransformer::class, $field->getTransformer());
        $this->assertEquals('John', $field->getValue());
        $this->assertEquals(['required'], $field->getConstraints());
    }

    public function testCreateThrowsExceptionForInvalidTransformer(): void
    {
        $fieldsDefinition = [
            'name' => [
                'Transformer' => 'NonExistentTransformerClass',
                'constraints' => ['required']
            ]
        ];

        $this->mockBuilder->method('getFields')->willReturn($fieldsDefinition);
        $this->mockResolver->method('resolve')->willReturn([
            'model' => 'App\Model\User',
            'fields' => $fieldsDefinition
        ]);

        $this->expectException(\Error::class);
        $this->factory->create($this->mapper, ['name' => 'John']);
    }

}

