# CRUD Operations

This document explains how to implement the Create, Read, Update, Delete operations for your Objects using `IntelParserTrait`.

## Overview

With `IntelParserTrait`, you implement these methods:

| Method | Purpose |
|--------|---------|
| `load()` | Load an existing object from database |
| `create()` | Create a new object |
| `update()` | Save changes to database |
| `delete()` | Delete an object |
| `getObjectIdentifier()` | Return current object ID |
| `objectsList()` | List objects with pagination |

The trait handles `get()` and `set()` automatically by calling your getter/setter methods.

## Complete Example

```php
<?php

namespace Splash\Local\Objects;

use Splash\Core\Models\AbstractObject;
use Splash\Core\Models\Objects\IntelParserTrait;
use App\Entity\ThirdParty as ThirdPartyEntity;
use App\Repository\ThirdPartyRepository;

class ThirdParty extends AbstractObject
{
    use IntelParserTrait;
    use ThirdParty\CrudTrait;      // CRUD Ops: create, load, update, delete...
    use ThirdParty\CoreTrait;      // Convention: defines all REQUIRED fields
    use ThirdParty\AddressTrait;

    //====================================================================//
    // Object Definition
    //====================================================================//

    protected static string $name = "Third Party";
    protected static string $description = "Customer or Supplier";
    protected static string $ico = "fa fa-user";

    //====================================================================//
    // Work Object
    //====================================================================//

    protected ThirdPartyEntity $object;
}
```

## The load() Method

Load an object from your database. Return `null` if not found.

```php
<?php

namespace Splash\Local\Objects\ThirdParty;

use App\Entity\ThirdParty;

trait CrudTrait
{
    /**
     * Load object from database
     */
    public function load(string $objectId): ?ThirdParty
    {
        //====================================================================//
        // Load from repository
        $thirdParty = $this->repository->find((int) $objectId);

        //====================================================================//
        // Object not found
        if (!$thirdParty) {
            Splash::log()->errTrace("Unable to load ThirdParty (" . $objectId . ")");

            return null;
        }

        return $thirdParty;
    }
}
```

## The create() Method

Create a new object instance. Don't save to database yet - that happens in `update()`.

```php
/**
 * Create a new object
 */
public function create(): ?ThirdParty
{
    //====================================================================//
    // Create new entity
    $thirdParty = new ThirdParty();

    //====================================================================//
    // Set default values
    $thirdParty->setCreatedAt(new \DateTime());
    $thirdParty->setStatus("active");

    return $thirdParty;
}
```

## The update() Method

Save changes to database. The `$needed` parameter indicates if any field was modified.

```php
/**
 * Save object to database
 *
 * @param bool $needed True if fields were modified
 */
public function update(bool $needed): ?string
{
    //====================================================================//
    // No changes? Skip database write for better performance
    if (!$needed) {
        return $this->getObjectIdentifier();
    }

    //====================================================================//
    // Save to database
    try {
        $this->entityManager->persist($this->object);
        $this->entityManager->flush();
    } catch (\Exception $e) {
        Splash::log()->err("Unable to save: " . $e->getMessage());
        return null;
    }

    return $this->getObjectIdentifier();
}
```

**Important**: Always check `$needed` before writing to database. The `IntelParserTrait` tracks which fields changed and passes `false` if nothing was modified.

## The delete() Method

Delete an object. Return `true` on success, `false` only if deletion failed.

```php
/**
 * Delete object from database
 */
public function delete(?string $objectId = null): bool
{
    //====================================================================//
    // Load object if not already loaded
    if ($objectId && !isset($this->object)) {
        $this->object = $this->load($objectId);
    }

    //====================================================================//
    // Object not found? Consider it deleted
    if (!$this->object) {
        return true;
    }

    //====================================================================//
    // Delete from database
    try {
        $this->entityManager->remove($this->object);
        $this->entityManager->flush();
    } catch (\Exception $e) {
        Splash::log()->err("Unable to delete: " . $e->getMessage());
        return false;
    }

    return true;
}
```

## The getObjectIdentifier() Method

Return the current object's ID as a string.

```php
/**
 * Return current object identifier
 */
public function getObjectIdentifier(): ?string
{
    if (!isset($this->object) || !$this->object->getId()) {
        return null;
    }

    return (string) $this->object->getId();
}
```

## The objectsList() Method

Return a paginated list of objects.

```php
/**
 * List objects with pagination and filtering
 */
public function objectsList(?string $filter = null, array $params = array()): array
{
    //====================================================================//
    // Prepare query
    $queryBuilder = $this->repository->createQueryBuilder('t');

    //====================================================================//
    // Apply filter
    if ($filter) {
        $queryBuilder
            ->where('t.name LIKE :filter')
            ->orWhere('t.email LIKE :filter')
            ->setParameter('filter', '%' . $filter . '%')
        ;
    }

    //====================================================================//
    // Get total count
    $total = (clone $queryBuilder)->select('COUNT(t.id)')->getQuery()->getSingleScalarResult();

    //====================================================================//
    // Apply pagination
    if (isset($params["max"])) {
        $queryBuilder->setMaxResults((int) $params["max"]);
    }
    if (isset($params["offset"])) {
        $queryBuilder->setFirstResult((int) $params["offset"]);
    }

    //====================================================================//
    // Apply sorting
    if (isset($params["sortfield"])) {
        $order = $params["sortorder"] ?? "ASC";
        $queryBuilder->orderBy('t.' . $params["sortfield"], $order);
    }

    //====================================================================//
    // Execute query
    $results = $queryBuilder->getQuery()->getResult();

    //====================================================================//
    // Build response
    $list = array();
    foreach ($results as $thirdParty) {
        $list[] = array(
            "id"    => $thirdParty->getId(),
            "name"  => $thirdParty->getName(),
            "email" => $thirdParty->getEmail(),
        );
    }

    //====================================================================//
    // Add metadata (required)
    $list["meta"] = array(
        "total"   => (int) $total,
        "current" => count($results),
    );

    return $list;
}
```

### objectsList Parameters

| Parameter | Description |
|-----------|-------------|
| `$filter` | Search string to filter results |
| `$params["max"]` | Maximum number of results |
| `$params["offset"]` | Pagination offset |
| `$params["sortfield"]` | Field name to sort by |
| `$params["sortorder"]` | Sort direction (`ASC` or `DESC`) |

### objectsList Response

The response must include:
- An array of objects with at least `id` field
- Fields marked with `isListed()` should be included
- A `meta` key with `total` and `current` counts

## Reading Fields (Getter Methods)

With `IntelParserTrait`, create methods named `getXxx()` where `Xxx` matches your field identifiers.

```php
<?php

namespace Splash\Local\Objects\ThirdParty;

trait CoreTrait
{
    /**
     * Build core fields
     */
    protected function buildCoreFields(): void
    {
        $this->fieldsFactory()->create(SplFields::VARCHAR)
            ->identifier("name")
            ->name("Company Name")
            ->isRequired()
        ;

        $this->fieldsFactory()->create(SplFields::EMAIL)
            ->identifier("email")
            ->name("Email")
        ;
    }

    /**
     * Read core fields
     */
    protected function getCoreFields(int $key, string $fieldName): void
    {
        switch ($fieldName) {
            case "name":
                $this->out[$fieldName] = $this->object->getName();
                unset($this->in[$key]);
                break;
            case "email":
                $this->out[$fieldName] = $this->object->getEmail();
                unset($this->in[$key]);
                break;
        }
    }
}
```

**Important**: Always `unset($this->in[$key])` after reading a field to mark it as processed.

## Writing Fields (Setter Methods)

Create methods named `setXxx()` to write fields.

```php
/**
 * Write core fields
 */
protected function setCoreFields(string $fieldName, $fieldData): void
{
    switch ($fieldName) {
        case "name":
            if ($this->object->getName() !== $fieldData) {
                $this->object->setName($fieldData);
                $this->needUpdate();
            }
            unset($this->in[$fieldName]);
            break;
        case "email":
            if ($this->object->getEmail() !== $fieldData) {
                $this->object->setEmail($fieldData);
                $this->needUpdate();
            }
            unset($this->in[$fieldName]);
            break;
    }
}
```

**Important**:
- Compare values before setting to avoid unnecessary updates
- Call `$this->needUpdate()` when a field is modified
- Always `unset($this->in[$fieldName])` after processing

## Using SimpleFieldsTrait

For simple properties, use `SimpleFieldsTrait` helpers:

```php
use Splash\Core\Models\Objects\SimpleFieldsTrait;

trait CoreTrait
{
    use SimpleFieldsTrait;

    protected function getCoreFields(int $key, string $fieldName): void
    {
        switch ($fieldName) {
            case "name":
            case "email":
            case "phone":
                $this->getSimple($fieldName);
                unset($this->in[$key]);
                break;
        }
    }

    protected function setCoreFields(string $fieldName, $fieldData): void
    {
        switch ($fieldName) {
            case "name":
            case "email":
            case "phone":
                $this->setSimple($fieldName, $fieldData);
                unset($this->in[$fieldName]);
                break;
        }
    }
}
```

### SimpleFieldsTrait Methods

| Method | Description |
|--------|-------------|
| `getSimple($field)` | Read a string property |
| `getSimpleBool($field)` | Read a boolean property |
| `getSimpleDouble($field)` | Read a float property |
| `setSimple($field, $data)` | Write a string property |
| `setSimpleBool($field, $data)` | Write a boolean property |
| `setSimpleDouble($field, $data)` | Write a float property |

## Next Steps

- [List Fields](lists.md) - Handle collections (order lines, etc.)
- [Advanced Patterns](advanced.md) - Extensions, optimization

---

[Back to Documentation Index](../index.md)
