# Defining Fields

Fields describe what data can be synchronized for an Object. Each field has a type, identifier, and various properties that control how it's handled.

## Field Types

### Simple Types

| Type       | Constant            | Description                        |
|------------|---------------------|------------------------------------|
| `bool`     | `SplFields::BOOL`   | Boolean (stored as 0 or 1)         |
| `int`      | `SplFields::INT`    | Signed integer                     |
| `double`   | `SplFields::DOUBLE` | Float/decimal values               |
| `varchar`  | `SplFields::VARCHAR`| Short text (max 256 chars)         |
| `text`     | `SplFields::TEXT`   | Long text                          |
| `email`    | `SplFields::EMAIL`  | Email address                      |
| `phone`    | `SplFields::PHONE`  | Phone number                       |
| `url`      | `SplFields::URL`    | URL                                |
| `date`     | `SplFields::DATE`   | Date (Y-m-d)                       |
| `datetime` | `SplFields::DATETIME`| DateTime (Y-m-d H:i:s)            |

### ISO Code Types

| Type       | Constant              | Description                      |
|------------|-----------------------|----------------------------------|
| `lang`     | `SplFields::LANG`     | Language code (e.g., `en_US`)    |
| `country`  | `SplFields::COUNTRY`  | Country code (e.g., `FR`)        |
| `state`    | `SplFields::STATE`    | State code (e.g., `CA`)          |
| `currency` | `SplFields::CURRENCY` | Currency code (e.g., `EUR`)      |

### Complex Types

See [Complex Fields](fields-complex.md) for detailed documentation:

| Type       | Constant            | Description                           |
|------------|---------------------|---------------------------------------|
| `price`    | `SplFields::PRICE`  | Price with VAT, currency              |
| `image`    | `SplFields::IMG`    | Image with metadata                   |
| `file`     | `SplFields::FILE`   | File with metadata                    |
| `objectid` | `SplFields::ID`     | Link to another Object                |
| `inline`   | `SplFields::INLINE` | Array stored as JSON                  |

## Defining Fields with a Trait

The recommended pattern is to define fields in a dedicated trait:

```php
<?php

namespace Splash\Local\Objects\ThirdParty;

use Splash\Core\Dictionary\SplFields;

trait FieldsTrait
{
    /**
     * Build Object Fields Definition
     */
    protected function buildThirdPartyFields(): void
    {
        //====================================================================//
        // Name
        $this->fieldsFactory()->create(SplFields::VARCHAR)
            ->identifier("name")
            ->name("Company Name")
            ->isRequired()
            ->isPrimary()
            ->isListed()
        ;

        //====================================================================//
        // Email
        $this->fieldsFactory()->create(SplFields::EMAIL)
            ->identifier("email")
            ->name("Email Address")
            ->isListed()
        ;

        //====================================================================//
        // Status with Choices
        $this->fieldsFactory()->create(SplFields::VARCHAR)
            ->identifier("status")
            ->name("Status")
            ->addChoices(array(
                "active"   => "Active",
                "inactive" => "Inactive",
                "prospect" => "Prospect",
            ))
        ;

        //====================================================================//
        // Address Fields (grouped)
        $this->fieldsFactory()->create(SplFields::VARCHAR)
            ->identifier("address")
            ->name("Street")
            ->group("Address")
        ;

        $this->fieldsFactory()->create(SplFields::VARCHAR)
            ->identifier("city")
            ->name("City")
            ->group("Address")
        ;

        $this->fieldsFactory()->create(SplFields::COUNTRY)
            ->identifier("country")
            ->name("Country")
            ->group("Address")
        ;
    }
}
```

Then use the trait in your Object:

```php
<?php

namespace Splash\Local\Objects;

use Splash\Core\Models\AbstractObject;
use Splash\Core\Models\Objects\IntelParserTrait;

class ThirdParty extends AbstractObject
{
    use IntelParserTrait;
    use ThirdParty\FieldsTrait;

    // ... rest of your object
}
```

## Field Properties

### Core Properties

| Method           | Description                                  |
|------------------|----------------------------------------------|
| `identifier()`   | Unique field ID (used in get/set)            |
| `name()`         | Display name                                 |
| `description()`  | Detailed description                         |
| `group()`        | Group fields together (creates tabs in UI)   |

### Field Flags

| Method           | Description                                  |
|------------------|----------------------------------------------|
| `isRequired()`   | Field required for object creation           |
| `isPrimary()`    | Primary key (used for object identification) |
| `isIndexed()`    | Field is indexed for search                  |
| `isReadOnly()`   | Field can only be read, not written          |
| `isWriteOnly()`  | Field can only be written, not read          |
| `isListed()`     | Include in `objectsList()` results           |
| `isListHidden()` | In list but hidden (for analytics)           |
| `isLogged()`     | Log changes to this field                    |
| `isNotTested()`  | Exclude from automated tests                 |

## Field Groups

Groups organize related fields together. In the Toolkit UI, each group becomes a **tab**.

```php
//====================================================================//
// Billing Address Tab
$this->fieldsFactory()->create(SplFields::VARCHAR)
    ->identifier("billing_address")
    ->name("Street")
    ->group("Billing Address")
;

$this->fieldsFactory()->create(SplFields::VARCHAR)
    ->identifier("billing_city")
    ->name("City")
    ->group("Billing Address")
;

//====================================================================//
// Shipping Address Tab
$this->fieldsFactory()->create(SplFields::VARCHAR)
    ->identifier("shipping_address")
    ->name("Street")
    ->group("Shipping Address")
;

$this->fieldsFactory()->create(SplFields::VARCHAR)
    ->identifier("shipping_city")
    ->name("City")
    ->group("Shipping Address")
;
```

## Choices

Choices define allowed values for a field. They display as **select dropdowns** in the Toolkit and are used to **generate test values** during automated testing.

```php
$this->fieldsFactory()->create(SplFields::VARCHAR)
    ->identifier("status")
    ->name("Order Status")
    ->addChoices(array(
        "draft"     => "Draft",
        "pending"   => "Pending",
        "confirmed" => "Confirmed",
        "shipped"   => "Shipped",
        "delivered" => "Delivered",
        "cancelled" => "Cancelled",
    ))
;
```

You can also add choices one by one:

```php
$this->fieldsFactory()->create(SplFields::VARCHAR)
    ->identifier("priority")
    ->name("Priority")
    ->addChoice("low", "Low Priority")
    ->addChoice("normal", "Normal Priority")
    ->addChoice("high", "High Priority")
    ->addChoice("urgent", "Urgent")
;
```

## Multi-Language Fields

For multilingual content, create one field per language and use `setMultiLang()`:

```php
// Set the default language first
$this->fieldsFactory()->setDefaultLanguage("en_US");

// English (default)
$this->fieldsFactory()->create(SplFields::VARCHAR)
    ->identifier("name_en")
    ->name("Name (English)")
    ->setMultiLang("en_US")
;

// French
$this->fieldsFactory()->create(SplFields::VARCHAR)
    ->identifier("name_fr")
    ->name("Name (French)")
    ->setMultiLang("fr_FR")
;

// German
$this->fieldsFactory()->create(SplFields::VARCHAR)
    ->identifier("name_de")
    ->name("Name (German)")
    ->setMultiLang("de_DE")
;
```

The default language field will be used when the target system doesn't support multilingual content.

## Field Templates

Templates provide pre-configured field definitions for common use cases. Many templates are available in the [splash/scopes](https://packagist.org/packages/splash/scopes) package.

Use `createFromTemplate()` or `template()`:

```php
use Splash\Local\Fields\MyCustomTemplate;

// Create field from template
$this->fieldsFactory()->createFromTemplate("my_field", MyCustomTemplate::class, "My Field Name");

// Or apply template to existing field
$this->fieldsFactory()->create(SplFields::VARCHAR)
    ->identifier("my_field")
    ->template(MyCustomTemplate::class)
;
```

Create your own template by implementing `FieldTemplateInterface`:

```php
<?php

namespace Splash\Local\Fields;

use Splash\Core\Dictionary\SplFields;
use Splash\Core\Interfaces\Fields\FieldTemplateInterface;

class StatusTemplate implements FieldTemplateInterface
{
    public static function getCode(): string
    {
        return "status";
    }

    public function getConfiguration(?string $isoLang = null): array
    {
        return array(
            "type"     => SplFields::VARCHAR,
            "name"     => "Status",
            "required" => false,
            "choices"  => array(
                array("key" => "active", "value" => "Active"),
                array("key" => "inactive", "value" => "Inactive"),
            ),
        );
    }
}
```

## Next Steps

- [Complex Fields](fields-complex.md) - Price, Image, File, ObjectId
- [CRUD Operations](crud.md) - Read and write field values
- [List Fields](lists.md) - Handle collections (order lines, etc.)

---

[Back to Documentation Index](../index.md)
