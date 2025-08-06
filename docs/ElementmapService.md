Here is the **Markdown documentation** for the provided Craft CMS service class `ElementmapService`, which forms a core part of the **Element Map** plugin.

---


# 📘 Documentation: `ElementmapService` – Element Map Plugin for Craft CMS

## Overview

The `ElementmapService` class bootstraps the **Element Map plugin** into the Craft CMS control panel UI. It hooks into the Craft CP via event listeners and template hooks to render:

- Element relationship buttons in the sidebar
- Element map data in index views (tables)
- Custom URL routes to access relationship data

This service integrates **deeply with the Craft CP interface**, enhancing the editing experience for entries, assets, users, categories, products, and even elements from third-party plugins like **Commerce** and **Campaign**.

---

## 🚀 Public Method: `initElementmap()`

Initializes all plugin behavior by registering:
- Custom CP URL routes
- Sidebar buttons for supported element types
- Column display handlers for element indexes

---

### 📌 Route Registration

```php
$event->rules['elementmap-getrelations/<siteId:.*>/<elementId:[\d]+>'] = '_elementmap/elementmap/get-relations';
````

Adds a custom control panel route used to load the relationship map for a given element via `ElementmapController`.

---

### 🧩 Sidebar Button Hooks

Uses `DefineHtmlEvent` to inject the **element map sidebar button** into element editors:

Supported types:

* `Entry`
* `Category`
* `Asset`
* `User`
* `CampaignElement` (if Campaign plugin is enabled)
* `Product` (if Commerce plugin is enabled)

```php
Craft::$app->getView()->hook('cp.commerce.product.edit.details', [$this, 'renderProductElementMap']);
```

This allows editors to access relationship visualizations directly within the edit UI.

---

### 📊 Table Column Enhancements

Registers new columns for **Assets** and **Entries**:

```php
$event->tableAttributes['elementmap_incomingReferenceCount']
$event->tableAttributes['elementmap_outgoingReferenceCount']
$event->tableAttributes['elementmap_incomingReferences']
$event->tableAttributes['elementmap_outgoingReferences']
```

Handled via:

* `registerElementmapTableAttributes()`
* `getElementmapTableAttributeHtml()`

These enhance index views with relationship data.

---

## 🧠 Method: `registerElementmapTableAttributes(RegisterElementTableAttributesEvent $event)`

Adds custom columns to Craft's element index table for:

* **Reference counts** (incoming/outgoing)
* **Reference details** (incoming/outgoing)

Useful for sorting or scanning related content in list view.

---

## 🧠 Method: `getElementmapTableAttributeHtml(DefineAttributeHtmlEvent $event)`

Generates the actual **HTML output** for each custom table column using the `ElementmapRenderer`.

It:

* Computes relationship data (incoming/outgoing)
* Optionally limits based on plugin settings
* Renders with `_elementmap/_elementmap_indexcolumn.twig`

Handles errors gracefully with fallbacks.

---

## 🧠 Method: `renderSidebarButton(ElementInterface $element, string $class): string`

Renders the actual **sidebar button** UI for a specific element type using:

```php
'_elementmap/_elementmap_sidebarbutton.twig'
```

This is the button users click to open the element relationship map.

---

## 🎨 Editor Hook Renderers

These methods are triggered via Twig hooks in the edit screen for different element types:

```php
renderAssetElementMap(array &$context)
renderProductElementMap(array &$context)
```

Internally, they all delegate to `renderSidebarButton()`.

> Note: Some `render*ElementMap` methods are commented out (e.g. for users, globals). You can enable them to extend functionality further.

---

## 🔗 Integration with Plugins

Conditionally hooks in extra support if the following plugins are enabled:

* ✅ **Craft Commerce**: Product relationship buttons and maps
* ✅ **Campaign**: Campaign element maps

Uses `Craft::$app->plugins->isPluginEnabled()` for safe plugin detection.

---

## 🔧 Twig Templates Used

* `_elementmap/_elementmap_sidebarbutton.twig`: Sidebar button
* `_elementmap/_elementmap_indexcolumn.twig`: Table cell for relationship data

---

## ✅ Summary

The `ElementmapService` class:

* Wires the plugin into Craft CP via event listeners
* Adds dynamic sidebar buttons to many element types
* Enhances element indexes with relationship data
* Provides a seamless UI integration layer between the Element Map logic and Craft’s backend UI

It is the **core UI glue** that connects Craft CMS elements to the element map display engine (`ElementmapRenderer`).

---

## 🧩 Related Classes

* [`ElementmapRenderer`](./ElementmapRenderer_Documentation.md): Responsible for generating actual relationship data
* `ElementmapController`: Handles AJAX/HTTP routes for fetching relationship data

---

## 🛠️ Developer Notes

To extend support for more element types:

1. Register additional `EVENT_DEFINE_SIDEBAR_HTML` listeners
2. Register table attributes using `EVENT_REGISTER_TABLE_ATTRIBUTES`
3. Render sidebar buttons or map data as needed

To enhance the UI:

* Customize the included Twig templates
* Extend the service class to support matrix blocks, super table blocks, or CKEditor elements

```

---

Let me know if you'd like to combine all these pieces into a single **developer manual** or generate it as a downloadable Markdown or PDF file once file saving is working again.
```
