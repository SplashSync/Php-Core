# Installation

## Requirements

- **PHP** 7.4 or higher (8.x recommended)
- **Extensions**: xml, soap, simplexml, xmlwriter, libxml

For development and testing, you'll also need: curl, apcu.

## Install via Composer

```bash
composer require splash/phpcore
```

## Project Structure

After installation, create the following structure in your project:

```
your-project/
├── local/
│   ├── Local.php           # Your Local Class (entry point)
│   ├── Objects/            # Your Objects
│   │   └── ThirdParty.php
│   └── Widgets/            # Your Widgets (optional)
│       └── Stats.php
└── vendor/
    └── splash/phpcore/
```

## Autoloading

Splash always looks for the class `Splash\Local\Local` to initialize your connector. This namespace is mandatory.

Add it to your `composer.json`:

```json
{
    "autoload": {
        "psr-4": {
            "Splash\\Local\\": "local/"
        }
    }
}
```

Then run:

```bash
composer dump-autoload
```

## Next Step

Configure your [Local Class](local-class.md) to connect your application to Splash.

---

[Back to Documentation Index](../index.md)
