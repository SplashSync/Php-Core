[![N|Solid](https://github.com/SplashSync/Php-Core/raw/master/img/github.jpg)](https://www.splashsync.com)

# Splash Php-Core

Core library for building Splash Sync connectors in PHP.

## What is Splash Sync?

Splash Sync is a universal data synchronization framework. Connectors built with this library can exchange data (Products, Orders, Customers, etc.) between any application and the Splash ecosystem.

## Features

- Base classes for Objects and Widgets
- Fluent API for field definitions
- Helpers for complex fields (prices, images, files)
- Auto-discovery of fields and getters/setters with `IntelParserTrait`
- Object extensions and filters

## Installation

```bash
composer require splash/phpcore
```

## Requirements

- PHP 7.4+ (8.x recommended)
- Extensions: xml, soap, simplexml, xmlwriter, libxml

## Documentation

See the [full documentation](docs/index.md) for:

- [Getting Started](docs/01-getting-started/installation.md)
- [Building Objects](docs/02-objects/overview.md)
- [Extensions & Filters](docs/03-extensions/index.md)
- [Widgets](docs/04-widgets/overview.md)
- [Helpers Reference](docs/05-helpers/index.md)
- [Testing](docs/06-testing/index.md)

## Quick Example

```php
<?php

namespace Splash\Local\Objects;

use Splash\Core\Models\AbstractObject;
use Splash\Core\Models\Objects\IntelParserTrait;

class ThirdParty extends AbstractObject
{
    use IntelParserTrait;
    use ThirdParty\CrudTrait;
    use ThirdParty\CoreTrait;

    protected static string $name = "Third Party";
    protected static string $description = "Customer or Supplier";
    protected static string $ico = "fa fa-user";
}
```

## Ecosystem

| Package | Description |
|---------|-------------|
| [Toolkit](https://gitlab.com/SplashTools/Toolkit) | Development environment (CLI/Docker) |
| [Php-Bundle](https://github.com/SplashSync/Php-Bundle) | Symfony integration |
| [OpenAPI](https://gitlab.com/SplashTools/OpenApi) | REST API connectors |
| [Metadata](https://gitlab.com/SplashTools/Metadata) | PHP 8 attributes support |

## License

This module is part of [SplashSync](https://splashsync.com) project.
