# Local Class

The Local Class is your connector's entry point. It implements `LocalClassInterface` and provides configuration, initialization, and metadata for your connector.

**Important**: Splash always looks for the class `Splash\Local\Local` to initialize your connector. This namespace and class name are mandatory.

## Minimal Implementation

```php
<?php

namespace Splash\Local;

use ArrayObject;
use Splash\Core\Interfaces\Local\LocalClassInterface;

class Local implements LocalClassInterface
{
    /**
     * Return connection parameters
     */
    public function parameters(): array
    {
        return array(
            "WsIdentifier"    => "your-server-id",
            "WsEncryptionKey" => "your-encryption-key",
        );
    }

    /**
     * Include required files
     */
    public function includes(): bool
    {
        // Load your application bootstrap if needed
        return true;
    }

    /**
     * Run self-test validations
     */
    public function selfTest(): bool
    {
        // Validate your configuration
        // Use Splash::log() to report errors
        return true;
    }

    /**
     * Provide server information
     */
    public function informations(ArrayObject $informations): ArrayObject
    {
        //====================================================================//
        // Company
        $informations->company  = "Your Company";
        $informations->address  = "123 Main Street";
        $informations->zip      = "12345";
        $informations->town     = "Paris";
        $informations->country  = "France";
        $informations->www      = "https://your-website.com";
        $informations->email    = "contact@your-website.com";
        $informations->phone    = "+33 1 23 45 67 89";

        //====================================================================//
        // Server
        $informations->servertype    = "Your Application Name";
        $informations->serverurl     = "https://your-app.com";
        $informations->moduleversion = "1.0.0";

        return $informations;
    }
}
```

## The includes() method: boot your app!

This method must fully boot your application when Splash receives an API call. Splash has no knowledge of your system, so `includes()` is responsible for initializing everything: autoloader, database connection, services, etc.

```php
use Splash\Core\Client\Splash;

public function includes(): bool
{
    //====================================================================//
    // Boot your application
    require_once '/path/to/app/bootstrap.php';

    return true;
}
```

You can differentiate between client and server mode using `Splash::isServerMode()`:

```php
use Splash\Core\Client\Splash;

public function includes(): bool
{
    //====================================================================//
    // When Library is called in SERVER mode
    // i.e. API call from Splash Cloud (incoming sync)
    //====================================================================//
    if (Splash::isServerMode()) {
        // Full initialization: autoloader, database, services, etc.
        require_once '/path/to/app/bootstrap.php';
    }

    //====================================================================//
    // When Library is called in CLIENT mode
    // i.e. API call from your application (outgoing sync)
    //====================================================================//
    if (!Splash::isServerMode()) {
        // Your app is already booted
        // Minimal initialization if needed
    }

    //====================================================================//
    // When Library is called in BOTH client & server mode
    //====================================================================//
    // Load additional Splash-specific resources here

    return true;
}
```

## The parameters() method: configure your connector

Returns connection credentials and optional settings. Mandatory values are provided by Splash when you create a server in your dashboard.

### Mandatory Parameters

| Parameter          | Description                              |
|--------------------|------------------------------------------|
| `WsIdentifier`     | Your server unique identifier            |
| `WsEncryptionKey`  | Encryption key for secure communication  |

**Note**: In production, these values are provided by Splash when you create a server in your dashboard. You don't define them yourself.

```php
public function parameters(): array
{
    return array(
        "WsIdentifier"    => "your-server-id",
        "WsEncryptionKey" => "your-encryption-key",
    );
}
```

You can also use environment variables. Splash automatically detects `SPLASH_CONNEXION`:

```bash
SPLASH_CONNEXION="https://your-server-id:your-encryption-key@proxy.splashsync.com/ws/soap"
```

With this env variable set, your `parameters()` can return an empty array:

```php
public function parameters(): array
{
    return array();
}
```

### User Interface Parameters

| Parameter          | Default   | Description                                          |
|--------------------|-----------|------------------------------------------------------|
| `DefaultLanguage`  | `en_US`   | Language code for translations (e.g., `fr_FR`)       |
| `SmartNotify`      | `false`   | Only show warnings & errors on commit notifications  |

### Technical Parameters

| Parameter          | Default                        | Description                                      |
|--------------------|--------------------------------|--------------------------------------------------|
| `WsHost`           | `proxy.splashsync.com/ws/soap` | Splash API server URL                            |
| `WsTimeout`        | `30`                           | Request timeout in seconds                       |
| `WsPostCommit`     | `true`                         | Intelligent Commit: send commits at end of request (set `false` to disable) |
| `ExtensionsPath`   | `[]`                           | List of paths to search for extensions (e.g., `["my-app/splash/extensions"]`) |
| `Configurator`     | -                              | Custom configurator class (must implement `ConfiguratorInterface`) |
| `ConfiguratorPath` | `local/configuration.json`     | Path to JSON config file (alternative to `Configurator` class) |
| `localname`        | -                              | Force client name displayed in logs              |

## The selfTest() method: validate your configuration

Validates your connector configuration. Called during server connection tests and results are displayed in your Splash dashboard.

Use `Splash::log()` to report errors and warnings:

```php
use Splash\Core\Client\Splash;

public function selfTest(): bool
{
    //====================================================================//
    // Check database connection
    if (!$this->checkDatabase()) {
        Splash::log()->err("Database connection failed");
        return false;
    }

    //====================================================================//
    // Check required configuration
    if (empty($this->getApiKey())) {
        Splash::log()->err("API key not configured");
        return false;
    }

    //====================================================================//
    // Non-blocking warning
    if (!$this->isCacheEnabled()) {
        Splash::log()->war("Cache is disabled, performance may be affected");
    }

    return true;
}
```

## The informations() method: describe your server

Provides metadata displayed in your Splash dashboard server profile.

Since `includes()` was called before, your application is fully booted. You can fetch information directly from your app:

```php
public function informations(ArrayObject $informations): ArrayObject
{
    //====================================================================//
    // Fetch company info from your application
    $company = MyApp::getCompany();

    //====================================================================//
    // Company
    $informations->company  = $company->getName();
    $informations->address  = $company->getAddress();
    $informations->zip      = $company->getZipCode();
    $informations->town     = $company->getCity();
    $informations->country  = $company->getCountry();
    $informations->www      = $company->getWebsite();
    $informations->email    = $company->getEmail();
    $informations->phone    = $company->getPhone();

    //====================================================================//
    // Server
    $informations->servertype    = "My Application";
    $informations->serverurl     = MyApp::getUrl();
    $informations->moduleversion = "1.0.0";

    return $informations;
}
```

### Company Fields

| Field     | Description           |
|-----------|-----------------------|
| `company` | Company name          |
| `address` | Street address        |
| `zip`     | Postal code           |
| `town`    | City                  |
| `country` | Country               |
| `www`     | Website URL           |
| `email`   | Contact email         |
| `phone`   | Phone number          |

### Server Fields

| Field           | Description                    |
|-----------------|--------------------------------|
| `servertype`    | Your application name          |
| `serverurl`     | Application URL                |
| `moduleversion` | Your connector version         |

### Logo Fields (optional)

| Field     | Description                          |
|-----------|--------------------------------------|
| `icoraw`  | Icon as base64 encoded string        |
| `logourl` | URL to your application logo         |

## The testSequences() method: define test scenarios (optional)

Define different test configurations for your connector. Called during test sequences initialization.

- When `$name` is `null` or `"List"`: return an array of available sequence names
- When `$name` is a sequence name: set up that sequence and return an empty array

```php
public function testSequences(?string $name = null): array
{
    switch ($name) {
        //====================================================================//
        // Setup "Basic" test sequence
        case "Basic":
            MyApp::setupBasicTestConfig();
            return array();
        //====================================================================//
        // Setup "Advanced" test sequence
        case "Advanced":
            MyApp::setupAdvancedTestConfig();
            return array();
        //====================================================================//
        // List available sequences
        default:
        case "List":
            return array("Basic", "Advanced");
    }
}
```

## The testParameters() method: override test settings (optional)

Override general test settings to adjust to your local system.

```php
public function testParameters(): array
{
    return array(
        //====================================================================//
        // Override default test settings
        "NumberOfItems" => 5,
        "ReadDelay"     => 100,
    );
}
```

## Next Step

Create your first [Object](../02-objects/overview.md).

---

[Back to Documentation Index](../index.md)
