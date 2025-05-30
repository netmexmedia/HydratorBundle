<?php

namespace Netmex\HydratorBundle\Validation;

use Netmex\HydratorBundle\Contracts\ValidatorInterface;
use Netmex\HydratorBundle\Mapper\FieldDefinition;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidator;

class FieldValidator implements ValidatorInterface
{
    private SymfonyValidator $validator;

    private array $violations = [];

    public function __construct(SymfonyValidator $validator)
    {
        $this->validator = $validator;
    }

    public function validate(FieldDefinition $field, string $context): void
    {
        $constraints = [];

        foreach ($field->getConstraints() as $ruleName => $value) {
            $rule = ValidationRule::tryFrom($ruleName);
            if ($rule === null) continue;

            $constraint = $rule->getConstraints($value, $field->getName());
            if ($constraint !== null) {
                $constraints[] = $constraint;
            }
        }

        $violations = $this->validator->validate($field->getValue(), $constraints);

        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        if (!empty($errors)) {
            $this->addViolations($field->getName(), $context, $errors);
        }
    }

    public function addViolations(string $property, string $context, array $messages): void
    {
        foreach ($this->violations as &$violation) {
            if ($violation['property'] === $property && $violation['context'] === $context) {
                $violation['message'] = array_unique(array_merge($violation['message'], $messages));
                return;
            }
        }
        unset($violation);

        $this->violations[] = [
            'property' => $property,
            'context' => $context,
            'message' => $messages,
        ];
    }

    public function hasErrors(): bool
    {
        return !empty($this->violations);
    }

    public function getErrors(): array
    {
        return $this->violations;
    }

    public function resetErrors(): void
    {
        $this->violations = [];
    }
}
