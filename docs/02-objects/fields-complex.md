# Complex Fields

Complex fields handle structured data like prices, images, files, and links between objects.

## Price Fields

Price fields store complete price information: amounts with/without tax, VAT rate, and currency.

### Definition

```php
use Splash\Core\Dictionary\SplFields;

$this->fieldsFactory()->create(SplFields::PRICE)
    ->identifier("price")
    ->name("Unit Price")
;
```

### Structure

```php
$price = array(
    "base"   => false,      // false = HT reference, true = TTC reference
    "ht"     => 100.00,     // Price without tax
    "ttc"    => 120.00,     // Price with tax
    "vat"    => 20.0,       // VAT rate in percent
    "tax"    => 20.00,      // Tax amount
    "code"   => "EUR",      // Currency code (ISO)
    "symbol" => "€",        // Currency symbol
    "name"   => "Euro",     // Currency name
);
```

### Using PricesHelper

Always use `PricesHelper` to encode prices:

```php
use Splash\Core\Helpers\PricesHelper;

// From price without tax (HT)
$price = PricesHelper::encode(
    100.00,     // HT price
    20.0,       // VAT rate
    null,       // TTC (will be calculated)
    "EUR",      // Currency code
    "€",        // Symbol (optional)
    "Euro"      // Name (optional)
);

// From price with tax (TTC)
$price = PricesHelper::encode(
    null,       // HT (will be calculated)
    20.0,       // VAT rate
    120.00,     // TTC price
    "EUR"
);
```

### Reading Price Values

```php
use Splash\Core\Helpers\PricesHelper;

// In your set method
$priceData = $receivedData["price"];

$ht     = PricesHelper::taxExcluded($priceData);  // Price without tax
$ttc    = PricesHelper::taxIncluded($priceData);  // Price with tax
$vat    = PricesHelper::taxPercent($priceData);   // VAT rate (e.g., 20.0)
$ratio  = PricesHelper::taxRatio($priceData);     // VAT ratio (e.g., 0.2)
$tax    = PricesHelper::taxAmount($priceData);    // Tax amount

// Validate a price array
if (PricesHelper::isValid($priceData)) {
    // Price is valid
}

// Compare two prices
if (PricesHelper::compare($price1, $price2)) {
    // Prices are equal
}
```

## Object ID Fields (Links)

Object ID fields create links between Splash Objects (e.g., Order → Customer).

### Definition

Use `ObjectsHelper::encode()` to specify which object type this field links to:

```php
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Helpers\ObjectsHelper;

// Link to ThirdParty objects
$this->fieldsFactory()->create((string) ObjectsHelper::encode("ThirdParty", SplFields::ID))
    ->identifier("customer_id")
    ->name("Customer")
    ->microData("http://schema.org/Organization", "ID")
;
```

### Format

Object IDs use the format: `ObjectId::ObjectType`

```
123::ThirdParty
456::Product
789::Address
```

### Using ObjectsHelper

```php
use Splash\Core\Helpers\ObjectsHelper;

//====================================================================//
// Encode: Create an Object ID string
$encoded = ObjectsHelper::encode("ThirdParty", "123");
// Result: "123::ThirdParty"

//====================================================================//
// Decode: Extract ID and Type
$objectId   = ObjectsHelper::id($encoded);    // "123"
$objectType = ObjectsHelper::type($encoded);  // "ThirdParty"

//====================================================================//
// Check if a value is an Object ID
if (ObjectsHelper::isIdField($value)) {
    // It's an object reference
}
```

### Example: Reading/Writing a Customer Link

```php
// GET: Return customer as Object ID
protected function getCustomer(): void
{
    $this->out["customer_id"] = ObjectsHelper::encode(
        "ThirdParty",
        (string) $this->object->getCustomerId()
    );
}

// SET: Extract customer ID from Object ID
protected function setCustomer(array $data): void
{
    if (!isset($data["customer_id"])) {
        return;
    }

    $customerId = ObjectsHelper::id($data["customer_id"]);
    if ($customerId && $customerId != $this->object->getCustomerId()) {
        $this->object->setCustomerId((int) $customerId);
        $this->needUpdate();
    }
}
```

## File Fields

File fields handle generic files with metadata.

### Definition

```php
use Splash\Core\Dictionary\SplFields;

$this->fieldsFactory()->create(SplFields::FILE)
    ->identifier("document")
    ->name("Attached Document")
;
```

### Structure

```php
$file = array(
    "name"     => "Invoice",                        // File name/description
    "filename" => "invoice.pdf",                    // Filename with extension
    "path"     => "/var/www/uploads/invoice.pdf",   // Full local path or file identifier
    "url"      => "https://example.com/invoice.pdf",// Public URL
    "md5"      => "abc123def456...",                // MD5 checksum
    "size"     => 54321,                            // File size in bytes
);
```

### Using FilesHelper

**Important**: `FilesHelper::encode()` returns only the file **description** (path, MD5, size), not the file content. Splash will request the file content separately when needed.

```php
use Splash\Core\Helpers\FilesHelper;

//====================================================================//
// Encode from local file
$file = FilesHelper::encode(
    "Invoice Q1 2024",                  // Name
    "invoice-q1-2024.pdf",              // Filename
    "/var/www/uploads/invoice.pdf",     // Full path
    "https://example.com/invoice.pdf"   // Public URL (optional)
);
```

### FileProviderInterface (for files not on disk)

If your files are not directly accessible on the filesystem (e.g., stored in database, cloud storage, or generated dynamically), implement `FileProviderInterface` in your Local Class:

```php
<?php

namespace Splash\Local;

use Splash\Core\Interfaces\FileProviderInterface;
use Splash\Core\Interfaces\Local\LocalClassInterface;

class Local implements LocalClassInterface, FileProviderInterface
{
    /**
     * Check if file is available
     */
    public function hasFile(string $file, string $md5): bool
    {
        // Check if file exists in your storage
        return $this->storage->exists($file);
    }

    /**
     * Read file content
     */
    public function readFile(string $file, string $md5): ?array
    {
        // Return file content from your storage
        $content = $this->storage->get($file);

        return array(
            "name"     => $file,
            "filename" => basename($file),
            "md5"      => $md5,
            "size"     => strlen($content),
            "file"     => base64_encode($content),  // File content as base64
        );
    }

    // ... other LocalClassInterface methods
}
```

When using `FileProviderInterface`, the `path` field becomes a file identifier that your provider uses to locate the file.

## Image Fields

Image fields extend File fields with additional metadata (dimensions). All File field concepts apply to Images.

### Definition

```php
use Splash\Core\Dictionary\SplFields;

$this->fieldsFactory()->create(SplFields::IMG)
    ->identifier("logo")
    ->name("Company Logo")
;
```

### Structure

```php
$image = array(
    "name"     => "Logo",                           // Image name/description
    "filename" => "logo.png",                       // Filename with extension
    "path"     => "/var/www/uploads/logo.png",      // Full local path
    "url"      => "https://example.com/logo.png",   // Public URL
    "width"    => 200,                              // Width in pixels
    "height"   => 100,                              // Height in pixels
    "md5"      => "abc123def456...",                // MD5 checksum
    "size"     => 12345,                            // File size in bytes
);
```

### Using ImagesHelper

**Note**: Like `FilesHelper`, `ImagesHelper::encode()` returns only the description, not the content.

```php
use Splash\Core\Helpers\ImagesHelper;

//====================================================================//
// Encode from local file
$image = ImagesHelper::encode(
    "Product Image",                    // Name
    "product.jpg",                      // Filename
    "/var/www/uploads/product.jpg",     // Full path
    "https://example.com/product.jpg"   // Public URL (optional)
);

//====================================================================//
// Encode from URL (downloads and analyzes the image)
$image = ImagesHelper::encodeFromUrl(
    "Product Image",                        // Name
    "https://cdn.example.com/image.jpg",    // Absolute URL
    "https://cdn.example.com/image.jpg"     // Public URL
);
```

## Inline Fields

Inline fields store arrays as JSON strings. Useful for tags, categories, or simple lists.

### Definition

```php
use Splash\Core\Dictionary\SplFields;

$this->fieldsFactory()->create(SplFields::INLINE)
    ->identifier("tags")
    ->name("Tags")
;
```

### Usage

```php
use Splash\Core\Helpers\InlineHelper;

//====================================================================//
// Encode: Array to JSON string
$inline = InlineHelper::fromArray(["tag1", "tag2", "tag3"]);
// Result: '["tag1","tag2","tag3"]'

//====================================================================//
// Decode: JSON string to array
$tags = InlineHelper::toArray($inline);
// Result: ["tag1", "tag2", "tag3"]
```

### Example

```php
// GET
protected function getTags(): void
{
    $this->out["tags"] = InlineHelper::fromArray(
        $this->object->getTags()
    );
}

// SET
protected function setTags(array $data): void
{
    if (!isset($data["tags"])) {
        return;
    }

    $tags = InlineHelper::toArray($data["tags"]);
    $this->object->setTags($tags);
    $this->needUpdate();
}
```

## Next Steps

- [CRUD Operations](crud.md) - Read and write field values
- [List Fields](lists.md) - Handle collections (order lines, etc.)

---

[Back to Documentation Index](../index.md)
