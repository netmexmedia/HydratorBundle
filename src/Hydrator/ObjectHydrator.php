<?php

namespace Netmex\HydratorBundle\Hydrator;

use Netmex\HydratorBundle\Contracts\ValidatorInterface;
use Netmex\HydratorBundle\Exception\MappingValidationException;
use Netmex\HydratorBundle\Iterator\FieldIterator;
use Netmex\HydratorBundle\Mapper\MapperDefinition;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final readonly class ObjectHydrator
{
    public function __construct(
        private FieldIterator         $fieldIterator,
        private ValidatorInterface    $validator,
        private DenormalizerInterface $serializer,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws MappingValidationException
     */
    public function hydrate(MapperDefinition $definition): object
    {
        $data = iterator_to_array(
            $this->fieldIterator->iterate($definition),
        );

        if ($this->validator->hasErrors()) {
            throw new MappingValidationException(
                $this->validator->getErrors(),
            );
        }

        return $this->serializer->denormalize(
            $data,
            $definition->getOptions()->getModel(),
            null,
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $definition->getTarget(),
            ],
        );
    }
}
