<?php

namespace Netmex\HydratorBundle\Exception;

use Exception;

class MappingValidationException extends Exception
{
    private array $violations;

    public function __construct(array $violations, string $message = "VALIDATION FAILED", int $code = 0, ?\Throwable $previous = null)
    {
        $this->violations = $violations;
        parent::__construct($message . $this->formatViolations(), $code, $previous);
    }

    public function getViolations(): array
    {
        return $this->violations;
    }

    private function formatViolations(): string
    {
        $grouped = $this->groupViolationsByContextAndField($this->violations);

        $output = "\n";
        foreach ($grouped as $context => $fields) {
            $output .= sprintf("%s errors:\n", $context);
            $output .= $this->formatFieldErrors($fields);
        }

        return $output;
    }

    private function groupViolationsByContextAndField(array $violations): array
    {
        $grouped = [];

        foreach ($violations as $violation) {
            $context = strtoupper($violation['context']);
            $property = $violation['property'];
            $messages = is_array($violation['message']) ? $violation['message'] : [$violation['message']];

            foreach ($messages as $message) {
                $grouped[$context][$property][] = $message;
            }
        }

        return $grouped;
    }

    private function formatFieldErrors(array $fields): string
    {
        $output = "";

        foreach ($fields as $property => $messages) {
            $output .= "- $property:\n";
            foreach ($messages as $message) {
                $output .= "\u{2003}\u{2003}• $message\n";
            }
            $output .= "\n";
        }

        return $output;
    }
}
