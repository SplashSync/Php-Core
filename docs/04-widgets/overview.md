# Widgets Overview

Widgets display data on the Splash Dashboard. They can show statistics, charts, notifications, or any information from your application.

## What is a Widget?

A Widget is a PHP class that:

- Extends `AbstractWidget`
- Returns data for display on the dashboard
- Lives in the `Splash\Local\Widgets` namespace

## Minimal Widget Structure

```php
<?php

namespace Splash\Local\Widgets;

use Splash\Core\Models\AbstractWidget;

class SalesStats extends AbstractWidget
{
    //====================================================================//
    // Widget Definition
    //====================================================================//

    protected static string $name = "Sales Statistics";
    protected static string $description = "Daily sales overview";
    protected static string $ico = "fa fa-line-chart";

    //====================================================================//
    // Widget Options
    //====================================================================//

    public static array $options = array(
        "Width"         => self::SIZE_DEFAULT,
        "UseCache"      => true,
        "CacheLifeTime" => 60,  // Cache for 60 minutes
    );

    //====================================================================//
    // Main Method
    //====================================================================//

    public function get(array $parameters = array()): ?array
    {
        //====================================================================//
        // Setup Widget Header
        $this->setTitle($this->getName());
        $this->setIcon($this->getIcon());

        //====================================================================//
        // Build Content Blocks
        $this->buildStatsBlock();

        //====================================================================//
        // Set Blocks & Render
        $blocks = $this->blocksFactory()->render();
        if ($blocks) {
            $this->setBlocks($blocks);
        }

        return $this->render();
    }

    private function buildStatsBlock(): void
    {
        $this->blocksFactory()->addSparkInfoBlock(array(
            "title"    => "Today's Sales",
            "fa_icon"  => "fa fa-shopping-cart",
            "value"    => "$1,234",
        ));
    }
}
```

## Widget Sizes

Use size constants for responsive layouts:

| Constant | Description | Grid Classes |
|----------|-------------|--------------|
| `SIZE_XS` | Extra small | `col-sm-6 col-md-4 col-lg-3` |
| `SIZE_SM` | Small | `col-sm-6 col-md-6 col-lg-4` |
| `SIZE_DEFAULT` / `SIZE_M` | Medium (default) | `col-sm-12 col-md-6 col-lg-6` |
| `SIZE_L` | Large | `col-sm-12 col-md-6 col-lg-8` |
| `SIZE_XL` | Full width | `col-sm-12 col-md-12 col-lg-12` |

## Widget Options

```php
public static array $options = array(
    "Width"         => self::SIZE_DEFAULT,  // Widget size
    "UseCache"      => true,                // Enable caching
    "CacheLifeTime" => 60,                  // Cache duration (minutes)
);
```

## Block Types

Use `$this->blocksFactory()` to create content blocks:

### Text Block

Simple text content:

```php
$this->blocksFactory()->addTextBlock("Your text content here");
```

### Notifications Block

Status messages with icons:

```php
$this->blocksFactory()->addNotificationsBlock(array(
    "success" => "Operation completed!",
));

$this->blocksFactory()->addNotificationsBlock(array(
    "error"   => "Error message",
    "warning" => "Warning message",
    "info"    => "Info message",
    "success" => "Success message",
));
```

### Spark Info Block

Key metric display:

```php
$this->blocksFactory()->addSparkInfoBlock(array(
    "title"    => "Total Orders",
    "fa_icon"  => "fa fa-shopping-cart",  // FontAwesome icon
    "value"    => "1,234",
    "chart"    => array(10, 20, 30, 25),  // Optional sparkline data
));
```

### Table Block

Data in rows:

```php
$this->blocksFactory()->addTableBlock(array(
    array("Product A", "10", "$100"),
    array("Product B", "5", "$50"),
    array("Product C", "20", "$200"),
));
```

### Charts (Bar, Line, Area, Donut)

These methods use the legacy "Morris" naming but charts are now rendered with **Chart.js**. The API and data format remain unchanged for backward compatibility.

Available chart types: Bar, Line, Area, and Donut.

```php
// Bar Chart
$this->blocksFactory()->addMorrisGraphBlock(
    array(
        array("period" => "2024-01", "sales" => 100),
        array("period" => "2024-02", "sales" => 150),
        array("period" => "2024-03", "sales" => 120),
    ),
    "Bar",  // "Bar", "Line", or "Area"
    array(
        "title"  => "Monthly Sales",
        "xkey"   => "period",
        "ykeys"  => array("sales"),
        "labels" => array("Sales"),
    )
);

// Donut Chart
$this->blocksFactory()->addMorrisDonutBlock(
    array(
        array("label" => "Product A", "value" => 30),
        array("label" => "Product B", "value" => 50),
        array("label" => "Product C", "value" => 20),
    ),
    array("title" => "Sales by Product")
);
```

## Custom Parameters

Allow users to configure widget behavior:

```php
public function getParameters(): array
{
    //====================================================================//
    // Use FieldsFactory to define parameters
    $this->fieldsFactory()->create(SPL_T_VARCHAR)
        ->identifier("period")
        ->name("Time Period")
        ->addChoices(array(
            "day"   => "Today",
            "week"  => "This Week",
            "month" => "This Month",
        ))
    ;

    return $this->fieldsFactory()->publish();
}

public function get(array $parameters = array()): ?array
{
    //====================================================================//
    // Read parameter value
    $period = $parameters["period"] ?? "day";

    // Use $period to filter data...
}
```

## Date Range Widget

For widgets with date filtering, use `DatesManagerTrait`:

```php
use Splash\Core\Models\AbstractWidget;

class SalesReport extends AbstractWidget
{
    public function get(array $parameters = array()): ?array
    {
        //====================================================================//
        // Get date range from parameters
        $dateStart = $this->getDateStart($parameters);
        $dateEnd   = $this->getDateEnd($parameters);

        // Query your data with date range...
    }
}
```

## Disabling a Widget

```php
protected static bool $disabled = true;
```

## Complete Example

```php
<?php

namespace Splash\Local\Widgets;

use Splash\Core\Models\AbstractWidget;

class OrdersSummary extends AbstractWidget
{
    protected static string $name = "Orders Summary";
    protected static string $description = "Overview of recent orders";
    protected static string $ico = "fa fa-shopping-bag";

    public static array $options = array(
        "Width"         => self::SIZE_M,
        "UseCache"      => true,
        "CacheLifeTime" => 5,
    );

    public function get(array $parameters = array()): ?array
    {
        $this->setTitle($this->getName());
        $this->setIcon($this->getIcon());

        //====================================================================//
        // Fetch data from your application
        $stats = $this->orderRepository->getTodayStats();

        //====================================================================//
        // Build blocks
        $this->blocksFactory()->addSparkInfoBlock(array(
            "title"   => "Orders Today",
            "fa_icon" => "fa fa-shopping-cart",
            "value"   => $stats["count"],
        ));

        $this->blocksFactory()->addSparkInfoBlock(array(
            "title"   => "Revenue",
            "fa_icon" => "fa fa-money",
            "value"   => "$" . number_format($stats["revenue"], 2),
        ));

        //====================================================================//
        // Render
        $blocks = $this->blocksFactory()->render();
        if ($blocks) {
            $this->setBlocks($blocks);
        }

        return $this->render();
    }
}
```

---

[Back to Documentation Index](../index.md)
