<?php

namespace Netmex\HydratorBundle\Resolver;

use Netmex\HydratorBundle\Contracts\OptionsInterface;
use Netmex\HydratorBundle\Contracts\OptionsResolverInterface;

final class OptionsResolver implements OptionsResolverInterface
{
    private array $defaults = [];

    public function resolve(array $options, string $optionsClass): OptionsInterface
    {
        try {
            $reflection = new \ReflectionClass($optionsClass);
            $instance = $reflection->newInstance();
        } catch (\ReflectionException $exception) {
            throw new \InvalidArgumentException(
                sprintf('Invalid options class "%s".', $optionsClass),
                previous: $exception,
            );
        }

        /** @var OptionsInterface $instance */
        $resolved = $this->resolveDefaults($reflection, $instance);

        $this->validateRequiredOptions($options, $instance);
        $this->validateOptions($options, $instance, $optionsClass);

        $resolved = array_replace($resolved, $options);

        $this->applyOptions($instance, $resolved);

        return $instance;
    }

    private function resolveDefaults(\ReflectionClass $reflection, OptionsInterface $instance): array
    {
        $defaults = [];

        foreach ($reflection->getProperties() as $property) {
            $setter = $this->getSetterName($property->getName());

            if ($property->hasDefaultValue() && method_exists($instance, $setter)) {
                $defaults[$property->getName()] = $property->getDefaultValue();
            }
        }

        return $defaults;
    }

    private function validateOptions(array $options, OptionsInterface $instance, string $optionsClass): void
    {
        foreach ($options as $name => $value) {

            $setter = $this->getSetterName($name);

            if (!method_exists($instance, $setter)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Unknown option "%s" for %s.',
                        $name,
                        $optionsClass,
                    ),
                );
            }
        }
    }

    private function validateRequiredOptions(array $options, OptionsInterface $instance): void
    {
        foreach ($instance->getRequiredOptions() as $name) {
            if (!array_key_exists($name, $options)) {
                throw new \InvalidArgumentException(
                    sprintf('Required option "%s" is missing.', $name),
                );
            }
        }
    }

    private function applyOptions(object $instance, array $options): void
    {
        foreach ($options as $name => $value) {
            $setter = $this->getSetterName($name);
            $instance->$setter($value);
        }
    }

    private function getSetterName(string $name): string
    {
        return 'set' . ucfirst($name);
    }

    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * @param array{
     *     model?: class-string,
     *     multiple?: bool,
     * } $defaults
     */
    public function setDefaults(array $defaults): static
    {
        foreach ($defaults as $option => $value) {
            $this->setValue($option, $value);
        }

        return $this;
    }

    private function setValue(string $option, mixed $value): void
    {
        $this->defaults[$option] = $value;
    }
}
