# Helpers Reference

Splash provides helper classes for common data transformations and field operations.

| Helper | Purpose |
|--------|---------|
| [PricesHelper](#priceshelper) | Price encoding/decoding with tax calculations |
| [ObjectsHelper](#objectshelper) | Object ID links (e.g., `123::ThirdParty`) |
| [DatesHelper](#dateshelper) | Date/DateTime formatting |
| [FilesHelper](#fileshelper) | File field metadata |
| [ImagesHelper](#imageshelper) | Image field metadata with dimensions |
| [ListsHelper](#listshelper) | Collection/list field operations |
| [InlineHelper](#inlinehelper) | Array to JSON conversion |

## PricesHelper

Handle price fields with tax calculations.

**Note**: Prices are automatically converted between **Tax Excluded** and **Tax Included** using 6 decimal places internally, ensuring 3-digit precision in final values.

```php
use Splash\Core\Helpers\PricesHelper;
```

### Encoding Prices

```php
// From price without tax
$price = PricesHelper::encode(
    100.00,     // Tax Excluded price
    20.0,       // VAT rate (%)
    null,       // Tax Included (calculated)
    "EUR",      // Currency code
    "€",        // Symbol (optional)
    "Euro"      // Name (optional)
);

// From price with tax
$price = PricesHelper::encode(
    null,       // Tax Excluded (calculated)
    20.0,       // VAT rate (%)
    120.00,     // Tax Included price
    "EUR"
);
```

### Reading Prices

```php
$priceData = $receivedData["price"];

$taxExcluded = PricesHelper::taxExcluded($priceData);  // Price without tax
$taxIncluded = PricesHelper::taxIncluded($priceData);  // Price with tax
$vat    = PricesHelper::taxPercent($priceData);   // VAT rate (e.g., 20.0)
$ratio  = PricesHelper::taxRatio($priceData);     // VAT ratio (e.g., 0.2)
$tax    = PricesHelper::taxAmount($priceData);    // Tax amount

// Validate
if (PricesHelper::isValid($priceData)) {
    // Price is valid
}

// Compare two prices
if (PricesHelper::compare($price1, $price2)) {
    // Prices are equal
}
```

## ObjectsHelper

Encode/decode links between Splash Objects.

```php
use Splash\Core\Helpers\ObjectsHelper;
```

### Usage

```php
// Encode: Create Object ID string
$encoded = ObjectsHelper::encode("ThirdParty", "123");
// Result: "123::ThirdParty"

// Decode: Extract ID and Type
$objectId   = ObjectsHelper::id($encoded);    // "123"
$objectType = ObjectsHelper::type($encoded);  // "ThirdParty"

// Check if value is an Object ID
if (ObjectsHelper::isIdField($value)) {
    // It's an object reference
}
```

## DatesHelper

Format dates for Splash.

```php
use Splash\Core\Helpers\DatesHelper;
```

### Formats

| Constant | Format | Example |
|----------|--------|---------|
| `DATE_CAST` | `Y-m-d` | `2024-01-15` |
| `DATETIME_CAST` | `Y-m-d H:i:s` | `2024-01-15 14:30:00` |

### Usage

```php
// Convert DateTime to date string
$dateStr = DatesHelper::toDateStr($dateTime);
// Result: "2024-01-15"

// Convert DateTime to datetime string
$datetimeStr = DatesHelper::toDateTimeStr($dateTime);
// Result: "2024-01-15 14:30:00"

// Custom format
$custom = DatesHelper::toDateStr($dateTime, "d/m/Y");
// Result: "15/01/2024"
```

## FilesHelper

Handle file field data.

```php
use Splash\Core\Helpers\FilesHelper;
```

### Encoding Files

Returns file **description** only (path, MD5, size), not the content:

```php
$file = FilesHelper::encode(
    "Invoice Q1 2024",              // Name
    "invoice-q1-2024.pdf",          // Filename
    "/var/www/uploads/invoice.pdf", // Full path
    "https://example.com/invoice.pdf" // Public URL (optional)
);
```

## ImagesHelper

Handle image field data. Extends file functionality with dimensions.

```php
use Splash\Core\Helpers\ImagesHelper;
```

### Encoding Images

```php
// From local file
$image = ImagesHelper::encode(
    "Product Image",                // Name
    "product.jpg",                  // Filename
    "/var/www/uploads/product.jpg", // Full path
    "https://example.com/product.jpg" // Public URL (optional)
);

// From URL (downloads and analyzes)
$image = ImagesHelper::encodeFromUrl(
    "Product Image",                    // Name
    "https://cdn.example.com/image.jpg",// Absolute URL
    "https://cdn.example.com/image.jpg" // Public URL
);
```

## ListsHelper

Handle list/collection field data.

```php
use Splash\Core\Helpers\ListsHelper;
```

### Field Identifier Format

List fields use format: `fieldName@listName`

```php
// Check if field is a list field
if (ListsHelper::isList($fieldType)) {
    // It's a list field
}

// Extract list name
$listName = ListsHelper::listName("quantity@lines");  // "lines"

// Extract field name
$fieldName = ListsHelper::fieldName("quantity@lines"); // "quantity"

// Create list field identifier
$fieldId = ListsHelper::encode("lines", "quantity");  // "quantity@lines"
```

### Building List Output

```php
// Initialize list for output
$fieldName = ListsHelper::initOutput($this->out, "lines", $requestedField);
if (!$fieldName) {
    return;  // Field doesn't belong to this list
}

// Insert values
foreach ($items as $index => $item) {
    ListsHelper::insert($this->out, "lines", $requestedField, $index, $value);
}
```

## InlineHelper

Convert arrays to/from JSON strings for inline fields.

```php
use Splash\Core\Helpers\InlineHelper;
```

### Usage

```php
// Encode: Array to JSON string
$inline = InlineHelper::fromArray(["tag1", "tag2", "tag3"]);
// Result: '["tag1","tag2","tag3"]'

// Decode: JSON string to array
$tags = InlineHelper::toArray($inline);
// Result: ["tag1", "tag2", "tag3"]
```

## Summary Table

| Helper | Purpose |
|--------|---------|
| `PricesHelper` | Price encoding/decoding with tax calculations |
| `ObjectsHelper` | Object ID links (e.g., `123::ThirdParty`) |
| `DatesHelper` | Date/DateTime formatting |
| `FilesHelper` | File field metadata |
| `ImagesHelper` | Image field metadata with dimensions |
| `ListsHelper` | Collection/list field operations |
| `InlineHelper` | Array ↔ JSON conversion |

---

[Back to Documentation Index](../index.md)
