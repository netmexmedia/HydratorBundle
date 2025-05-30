<?php
namespace Netmex\HydratorBundle\Validation;

use Symfony\Component\Validator\Constraints as Assert;

enum ValidationRule: string
{
    // Core
    case NotBlank = 'NotBlank';
    case Blank = 'Blank';
    case NotNull = 'NotNull';
    case IsNull = 'IsNull';
    case Type = 'Type';
    case IsTrue = 'IsTrue';
    case IsFalse = 'IsFalse';

    // String / Format
    case Email = 'Email';
    case Url = 'Url';
    case Regex = 'Regex';
    case Length = 'Length';
    case MinLength = 'MinLength';
    case MaxLength = 'MaxLength';
    case Ip = 'Ip';
    case Uuid = 'Uuid';
    case Hostname = 'Hostname';

    // Numbers
    case Numeric = 'Numeric';
    case Integer = 'Integer';
    case Positive = 'Positive';
    case Negative = 'Negative';
    case PositiveOrZero = 'PositiveOrZero';
    case NegativeOrZero = 'NegativeOrZero';

    // Date / Time
    case Date = 'Date';
    case DateTime = 'DateTime';
    case Time = 'Time';

    // Comparison
    case EqualTo = 'EqualTo';
    case NotEqualTo = 'NotEqualTo';
    case GreaterThan = 'GreaterThan';
    case GreaterThanOrEqual = 'GreaterThanOrEqual';
    case LessThan = 'LessThan';
    case LessThanOrEqual = 'LessThanOrEqual';

    // Collections
    case Count = 'Count';
    case Unique = 'Unique';
    case Choice = 'Choice';
    case Collection = 'Collection';

    // Locale
    case Country = 'Country';
    case Currency = 'Currency';
    case Language = 'Language';
    case Locale = 'Locale';
    case Timezone = 'Timezone';

    // File
    case File = 'File';
    case Image = 'Image';

    // Finance / Security
    case Iban = 'Iban';
    case Bic = 'Bic';
    case Luhn = 'Luhn';
    case PasswordStrength = 'PasswordStrength';

    public function getConstraints(mixed $options, string $fieldName = '{{ property }}'): ?object
    {
        $constraint = match ($this) {
            self::NotBlank => new Assert\NotBlank(),
            self::Blank => new Assert\Blank($options),
            self::NotNull => new Assert\NotNull($options),
            self::IsNull => new Assert\IsNull($options),
            self::Type => new Assert\Type($options),
            self::IsTrue => new Assert\IsTrue($options),
            self::IsFalse => new Assert\IsFalse($options),

            self::Email => new Assert\Email(),
            self::Url => new Assert\Url(),
            self::Regex => new Assert\Regex($options),
            self::Length => new Assert\Length(array_merge(
                (array) $options,
                [
                    'maxMessage' => '{{ value }} is too long. It should have {{ limit }} characters or less.',
                    'minMessage' => '{{ value }} is too short. It should have {{ limit }} characters or more.',
                    'exactMessage' => '{{ value }} should have exactly {{ limit }} characters.',
                ]
            )),            self::MinLength => new Assert\Length([
                'min' => $options,
                'minMessage' => '{{ value }} is too short. It should have at least {{ limit }} characters.',
            ]),

            self::MaxLength => new Assert\Length([
                'max' => $options,
                'maxMessage' => '{{ value }} is too long. It should have at most {{ limit }} characters.',
            ]),
            self::Ip => new Assert\Ip($options),
            self::Uuid => new Assert\Uuid(),
            self::Hostname => new Assert\Hostname(),

            self::Numeric => new Assert\Type('numeric'),
            self::Integer => new Assert\Type('integer'),
            self::Positive => new Assert\Positive(),
            self::Negative => new Assert\Negative(),
            self::PositiveOrZero => new Assert\PositiveOrZero(),
            self::NegativeOrZero => new Assert\NegativeOrZero(),

            self::Date => new Assert\Date(),
            self::DateTime => new Assert\DateTime(),
            self::Time => new Assert\Time(),

            self::EqualTo => new Assert\EqualTo($options),
            self::NotEqualTo => new Assert\NotEqualTo($options),
            self::GreaterThan => new Assert\GreaterThan($options),
            self::GreaterThanOrEqual => new Assert\GreaterThanOrEqual($options),
            self::LessThan => new Assert\LessThan($options),
            self::LessThanOrEqual => new Assert\LessThanOrEqual($options),

            self::Count => new Assert\Count($options),
            self::Unique => new Assert\Unique(),
            self::Choice => new Assert\Choice($options),
            self::Collection => new Assert\Collection($options),

            self::Country => new Assert\Country(),
            self::Currency => new Assert\Currency(),
            self::Language => new Assert\Language(),
            self::Locale => new Assert\Locale(),
            self::Timezone => new Assert\Timezone(),

            self::File => new Assert\File($options),
            self::Image => new Assert\Image($options),

            self::Iban => new Assert\Iban(),
            self::Bic => new Assert\Bic(),
            self::Luhn => new Assert\Luhn(),
            self::PasswordStrength => new Assert\PasswordStrength($options),

            default => null,
        };

        if ($constraint !== null) {
            // Inject the field name into the default message where possible
            $constraint = self::addMessageOption($constraint, $fieldName);
        }

        return $constraint;
    }


    private static function addMessageOption(object $constraint, string $fieldName): object
    {
        // If the constraint has a 'message' property, override it with a property-based message.
        // This uses reflection to safely inject the message.

        $reflection = new \ReflectionObject($constraint);

        if ($reflection->hasProperty('message')) {
            $prop = $reflection->getProperty('message');
            $prop->setAccessible(true);
            // Replace default message with one that contains the field name:
            $customMessage = str_replace('This value', $fieldName, $constraint->message);
            $prop->setValue($constraint, $customMessage);
        }

        return $constraint;
    }

}
