# Testing

Splash provides a comprehensive test framework to validate your connector. Testing is primarily done through the **Toolkit**.

## Toolkit Testing

The [Splash Toolkit](https://gitlab.com/SplashTools/Toolkit) includes a complete test suite:

```bash
# Install Toolkit
composer require splash/toolkit --dev

# Run all tests
vendor/bin/phpunit
```

## Test Sequences

Define test sequences in your Local Class to control test execution:

```php
public function testSequences(string $name = null): array
{
    switch ($name) {
        case "List":
            return array(
                "ThirdParty"  => array("filter" => "test"),
                "Product"     => array(),
            );
        case "Main":
            return array(
                "ThirdParty"  => array("id" => "123"),
            );
    }

    return array();
}
```

## Test Parameters

Provide default test values for fields:

```php
public function testParameters(): array
{
    return array(
        "ThirdParty" => array(
            "name"  => "Test Company",
            "email" => "test@example.com",
        ),
    );
}
```

## Field Test Flags

Control which fields are tested:

```php
// Exclude field from automated tests
$this->fieldsFactory()->create(SplFields::VARCHAR)
    ->identifier("internal_code")
    ->name("Internal Code")
    ->isNotTested()  // Skip in tests
;
```

## Self-Test

Implement thorough self-testing in your Local Class:

```php
public function selfTest(): bool
{
    //====================================================================//
    // Check database connection
    if (!$this->database->isConnected()) {
        return Splash::log()->err("Database not connected");
    }

    //====================================================================//
    // Check required configuration
    if (empty($this->config->getApiKey())) {
        return Splash::log()->err("API key not configured");
    }

    return true;
}
```

## Running Tests

With Toolkit installed:

```bash
# Run all tests
vendor/bin/phpunit
```

Use filtering constants (see below) to target specific objects, fields, or sequences.

## PHPUnit Configuration

Configure test behavior in `phpunit.xml.dist`:

```xml
<phpunit>
    <php>
        <!-- Always present -->
        <const  name="SPLASH_DEBUG"     value="true" />
        <server name="SPLASH_CI"        value="true" />
        <server name="SERVER_NAME"      value="http://localhost"/>

        <!-- Only Test Specified Types -->
        <!--<const  name="SPLASH_TYPES"     value="ThirdParty" />-->
        <!--<const  name="SPLASH_TYPES"     value="Product" />-->

        <!-- Only Test Specified Fields -->
        <!--<const  name="SPLASH_FIELDS"    value="name,email" />-->

        <!-- Only Test Specified Sequence -->
        <!--<const  name="SPLASH_SEQUENCE"  value="Basic" />-->
    </php>
</phpunit>
```

### Filtering Constants

| Constant | Description |
|----------|-------------|
| `SPLASH_TYPES` | Limit tests to specific object types. Comma-separated list (e.g., `ThirdParty,Product`) |
| `SPLASH_FIELDS` | Filter on specific field IDs (e.g., `name,email,address`) |
| `SPLASH_SEQUENCE` | Filter on a specific test sequence. Useful for splitting tests in CI or focusing on a single server when using Php-Bundle (e.g., `Basic`, `Advanced`, `Variants`) |

Uncomment and set these values to speed up development by testing only what you need.

These can be defined as PHPUnit constants in `phpunit.xml` or exported as environment variables:

```bash
export SPLASH_TYPES=Product
export SPLASH_FIELDS=name,price
vendor/bin/phpunit
```

`SPLASH_DEBUG` is always present and enables verbose output during tests.

`SPLASH_CI` enables CI/CD mode for automated testing pipelines (`SPLASH_TRAVIS` is deprecated).

## Test Coverage

The standard test suite validates:

- **Connection**: Server connectivity and authentication
- **Objects**: CRUD operations for all objects
- **Fields**: Read/write for all field types
- **Lists**: Pagination and filtering
- **Widgets**: Widget rendering
- **Self-Test**: Your custom validation

## More Information

See the [Toolkit documentation](https://gitlab.com/SplashTools/Toolkit) for advanced testing options.

---

[Back to Documentation Index](../index.md)
