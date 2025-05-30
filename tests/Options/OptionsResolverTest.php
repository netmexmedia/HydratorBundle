<?php

namespace Netmex\HydratorBundle\Tests\Options;

use Netmex\HydratorBundle\Options\OptionsResolver;
use PHPUnit\Framework\TestCase;

class OptionsResolverTest extends TestCase
{
    public function testSetAndGetDefaults()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefault('model', 'User');
        $resolver->setDefault('fields', ['name' => ['type' => 'string']]);

        $resolved = $resolver->resolve();

        $this->assertEquals('User', $resolved['model']);
        $this->assertArrayHasKey('name', $resolved['fields']);
        $this->assertEquals(['type' => 'string'], $resolved['fields']['name']);
    }

    public function testResolveMergesOptionsIntoFields()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'model' => 'User',
            'fields' => ['email' => ['type' => 'string']],
        ]);

        $options = [
            'name' => ['type' => 'string'],
            'email' => ['type' => 'email'],
        ];

        $resolved = $resolver->resolve($options);

        $this->assertEquals('User', $resolved['model']);
        $this->assertCount(2, $resolved['fields']);
        $this->assertEquals(['type' => 'string'], $resolved['fields']['name']);
        $this->assertEquals(['type' => 'email'], $resolved['fields']['email']);
    }

    public function testHasModel()
    {
        $resolver = new OptionsResolver();
        $this->assertFalse($resolver->hasModel());

        $resolver->setDefault('model', 'User');
        $this->assertTrue($resolver->hasModel());
    }
}
