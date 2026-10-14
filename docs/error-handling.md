# Error Handling

Mapping input can fail for different reasons. Handling those failures separately helps applications return meaningful responses and makes debugging easier.

## Invalid JSON

When receiving JSON over HTTP, parse it before passing it to the mapper.

```php
try {
    $data = json_decode(
        $request->getContent(),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );
} catch (\JsonException $exception) {
    return new JsonResponse(
        ['error' => 'Invalid JSON.'],
        400,
    );
}
```

Also verify that the decoded value has the top-level structure expected by your mapper.

## Invalid mapping input

The bundle validates the shape of mapping input, including collection and nested mapping input.

For example, collection hydration expects a list of arrays. Passing a scalar or an incorrectly structured collection may result in an input exception.

The bundle provides mapping input exceptions for these situations. Catch the specific exception class exposed by your bundle when you want to distinguish malformed input from validation failures.

## Validation failures

Field constraints can reject input values. Handle the bundle's mapping validation exception and convert the failure into an appropriate application response.

```php
try {
    $result = $mapper->build(
        UserHydrator::class,
        $inputData,
    );
} catch (MappingValidationException $exception) {
    // Return a validation error response.
}
```

Import the exception class used by your bundle version:

```php
use Netmex\HydratorBundle\Exception\MappingValidationException;
```

If the exception exposes validation violations, consider returning a structured response containing the affected fields and their error messages rather than returning the exception message alone.

## Choose appropriate HTTP responses

An API may distinguish between:

- `400 Bad Request` for malformed request data.
- `422 Unprocessable Content` for syntactically valid input that fails application validation.
- `404 Not Found` when a referenced resource does not exist.
- `500 Internal Server Error` for unexpected failures.

These are application-level choices. The bundle does not determine which HTTP status your API must use.

## Avoid swallowing unexpected exceptions

Catch known input and validation exceptions where you can handle them meaningfully. Avoid catching every exception and returning the same client error, because programming errors and unexpected service failures should remain distinguishable.
