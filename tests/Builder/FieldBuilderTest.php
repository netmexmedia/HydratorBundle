<?php

namespace Netmex\HydratorBundle\Test\Builder;

use Netmex\HydratorBundle\Builder\FieldBuilder;
use Netmex\HydratorBundle\Contracts\TransformerInterface;
use PHPUnit\Framework\TestCase;

class FieldBuilderTest extends TestCase
{
    private FieldBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new FieldBuilder();
    }

    public function testAddFieldStoresCorrectData(): void
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

    public function testAddAcceptsStringTransformer(): void
    {
        $this->builder->add('status', 'string_transformer', null);

        $expected = [
            'status' => [
                'Transformer' => 'string_transformer',
                'constraints' => null,
            ],
        ];

        $this->assertEquals($expected, $this->builder->getFields());
    }
}
