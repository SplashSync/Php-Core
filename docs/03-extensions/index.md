# Extensions & Filters

Extend existing Objects or filter which objects are synchronized.

## Object Extensions

Extensions add fields to existing Objects **without modifying the native connector code**. This is essential for:

- **Preserving updates**: Your customizations persist after connector updates
- **Project-specific data**: Add fields unique to your integration needs
- **Non-intrusive customization**: Extend any connector without forking it
- **Modularity**: Add as many extensions as needed, each focused on specific functionality

You can create multiple extensions for the same object type. Each extension manages its own fields independently.

### Use Cases

- Adding custom fields from other modules
- Extending third-party connectors
- Multi-tenant customizations

### Creating an Extension

```php
<?php

namespace Splash\Local\Objects\Extensions;

use Splash\Core\Components\FieldsFactory;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Interfaces\Extensions\ObjectExtensionInterface;

class ThirdPartyExtension implements ObjectExtensionInterface
{
    /**
     * Which object types this extension applies to
     */
    public function getExtendedTypes(): array
    {
        return array("ThirdParty");
    }

    /**
     * Add fields to the object
     */
    public function buildExtendedFields(string $objectType, FieldsFactory $factory): void
    {
        $factory->create(SplFields::VARCHAR)
            ->identifier("custom_field")
            ->name("Custom Field")
            ->group("Custom Data")
        ;
    }

    /**
     * Read field values
     *
     * @return null|bool null = not managed, false = error, true = success
     */
    public function getExtendedFields(object $object, string $fieldId, &$fieldData): ?bool
    {
        switch ($fieldId) {
            case "custom_field":
                $fieldData = $object->getCustomField();

                return true;
        }

        return null;  // Field not managed by this extension
    }

    /**
     * Write field values
     *
     * @return null|bool null = not managed, false = unchanged, true = changed
     */
    public function setExtendedFields(object $object, string $fieldId, $fieldData): ?bool
    {
        switch ($fieldId) {
            case "custom_field":
                if ($object->getCustomField() !== $fieldData) {
                    $object->setCustomField($fieldData);

                    return true;  // Changed
                }

                return false;  // Unchanged
        }

        return null;  // Field not managed by this extension
    }
}
```

### Extension Interface

| Method | Purpose |
|--------|---------|
| `getExtendedTypes()` | Return array of object types to extend |
| `buildExtendedFields()` | Add field definitions using FieldsFactory |
| `getExtendedFields()` | Read field values |
| `setExtendedFields()` | Write field values |

### Return Values

For `getExtendedFields()`:
- `null` - Field not managed by this extension
- `false` - Field managed but reading failed
- `true` - Field read successfully

For `setExtendedFields()`:
- `null` - Field not managed by this extension
- `false` - Field managed but unchanged
- `true` - Field changed

## Object Filters

Filters prevent specific objects from being synchronized. Use cases:

- Exclude test/draft objects
- Filter by status or type
- Implement access control

### Creating a Filter

```php
<?php

namespace Splash\Local\Objects\Filters;

use Splash\Core\Interfaces\Extensions\ObjectFilterInterface;

class DraftOrderFilter implements ObjectFilterInterface
{
    /**
     * Which object types this filter applies to
     */
    public function getFilteredTypes(): array
    {
        return array("Order");
    }

    /**
     * Check if object should be filtered (excluded)
     *
     * @return bool true = filtered (excluded), false = allowed
     */
    public function isFiltered(string $objectType, string $objectId, ?object $object): bool
    {
        //====================================================================//
        // Load object if not provided
        if (!$object) {
            $object = $this->orderRepository->find($objectId);
        }

        //====================================================================//
        // Exclude draft orders
        if ($object && "draft" === $object->getStatus()) {
            return true;
        }

        return false;
    }
}
```

### Filter Interface

| Method | Purpose |
|--------|---------|
| `getFilteredTypes()` | Return array of object types to filter |
| `isFiltered()` | Return `true` to exclude object, `false` to allow |

### Filter Behavior

When an object is filtered:
- It won't appear in `objectsList()` results
- Changes won't be committed to Splash
- Read/write operations are blocked

## Namespace Convention

Place extensions and filters in dedicated namespaces:

```
Splash/Local/Objects/
├── Extensions/
│   ├── ThirdPartyExtension.php
│   └── ProductExtension.php
└── Filters/
    ├── DraftOrderFilter.php
    └── TestDataFilter.php
```

---

[Back to Documentation Index](../index.md)
