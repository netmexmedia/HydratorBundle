<?php

namespace Netmex\HydratorBundle\Tests\Builder;

use Netmex\HydratorBundle\Builder\FieldBuilder;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Contracts\TransformerInterface;
use PHPUnit\Framework\TestCase;

class FieldBuilderTest extends TestCase
{
    private FieldBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new FieldBuilder();
    }

    public function testAddFieldWithTransformer(): void
    {
        $mockTransformer = $this->createMock(TransformerInterface::class);
        $constraints = ['not_blank', 'max_length' => 255];

        $this->builder->add('title', $mockTransformer, $constraints);

        $expected = [
            'title' => [
                'Transformer' => $mockTransformer,
                'constraints' => $constraints,
            ],
        ];

        $this->assertEquals($expected, $this->builder->getFields());
    }

    public function testAddFieldWithMapperDefinition(): void
    {
        $mockMapperDefinition = $this->createMock(MapperDefinitionInterface::class);
        $constraints = ['not_blank', 'max_length' => 255];

        $this->builder->add('title', $mockMapperDefinition, $constraints);

        $expected = [
            'title' => [
                'MapperDefinition' => $mockMapperDefinition,
                'constraints' => $constraints,
            ],
        ];

        $this->assertEquals($expected, $this->builder->getFields());
    }

    public function testAddAcceptsStringTransformer(): void
    {
        // When passing a string transformer and omitting constraints, constraints should default to an empty array
        $this->builder->add('status', 'string_transformer');

        $expected = [
            'status' => [
                'Transformer' => 'string_transformer',
                'constraints' => [],
            ],
        ];

        $this->assertEquals($expected, $this->builder->getFields());
    }

    // TODO this isn't quite right without transformer then the whole transformer key should not exist
    public function testAddWithoutTransformerDefaultsConstraintsEmpty(): void
    {
        // Transformer is optional and constraints default to empty array
        $this->builder->add('name');

        $fields = $this->builder->getFields();

        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('Transformer', $fields['name']);
        $this->assertNull($fields['name']['Transformer']);
        $this->assertSame([], $fields['name']['constraints']);
    }
}
