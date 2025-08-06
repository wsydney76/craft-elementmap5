Here is a clear and structured **developer documentation** for the provided `ElementmapRenderer` class in your **Craft CMS plugin (Element Map)**. This doc explains the **purpose**, **main features**, and **how it works**, useful for onboarding new developers or maintaining the plugin.

---

# 📘 Documentation: `ElementmapRenderer` – Element Map Plugin for Craft CMS

## Overview

The `ElementmapRenderer` service powers the **Element Map plugin** for Craft CMS. Its main function is to generate an **interactive map of relationships** between elements — including entries, assets, users, addresses, Neo blocks, Commerce products, and more.

This renderer identifies:

* Elements **referencing** the target element (**incoming** relationships)
* Elements that the target **references** (**outgoing** relationships)

This helps content editors and developers **visualize complex relationships** between content types in Craft.

---

## Key Responsibilities

### 🔁 Relationship Mapping

* **Incoming Elements**: Other elements that reference the current one.
* **Outgoing Elements**: Elements that the current one references.

### ⚙️ Configurable Element Types

* A private config (`$elementTypeConfig`) determines:

    * Which element types are mapped.
    * Which method handles each.
    * Display sorting order.

### 📦 Supported Element Types

Includes but is not limited to:

* `Entry`, `ContentBlock`, `GlobalSet`, `Category`, `Asset`, `User`, `Address`
* Commerce: `Product`, `Variant`
* Neo: `Block`
* Campaign plugin: `CampaignElement`

---

## 🔧 Main Methods

### `getElementMap(Element $element, int $siteId): ?array`

Generates the map:

* `incoming`: elements referencing `$element`
* `outgoing`: elements referenced by `$element`
* `elementsNotShown`: counter for trimmed elements (if limited by settings)

---

### `getIncomingElements(Element $element, int $siteId): array`

Builds a list of elements **referencing** the provided element.

Steps:

1. Collect variant IDs if the element is a product.
2. Get all relationships where `$element` is the target.
3. Fetch associated elements via `getElementMapData`.

---

### `getOutgoingElements(Element $element, int $siteId): array`

Builds a list of elements the given element **references**.

Steps:

1. Starts with the element and its variants.
2. Recursively fetches nested entries, addresses, Neo blocks, content blocks.
3. Gets outgoing relationships.
4. Returns usable related elements.

---

## 🔁 Relationship Handling

### `getRelationships(array $elementIds, int $siteId, bool $getSources): array`

Queries the `relations` table to get related element IDs by:

* Source → Target (outgoing)
* Target → Source (incoming)

Groups by type and returns an array like:

```php
[
  ['id' => 123, 'type' => 'craft\elements\Entry'],
  ...
]
```

---

## 🔍 Element Data Retrieval

### `getElementMapData(array $elements, int $siteId): array`

Converts element IDs into full map records with metadata:

* Icon
* Title
* Color
* Edit URL
* View permission
* Sort key

Delegates by type (e.g., `getEntryElements`, `getAssetElements`).

---

## 📑 Data Fetching for Types

Each supported type has a handler, for example:

* `getEntryElements()`
* `getAssetElements()`
* `getUserElements()`
* `getProductElements()`

These methods:

1. Use Craft element queries (e.g., `Entry::find()`)
2. Create display metadata for each element
3. Include thumbnails, icons, or root owner info as needed

---

## 🔂 Utilities and Internals

### `getNestedEntryIds($elementId): array`

Recursively fetches IDs of nested elements (Entries, Addresses, Neo Blocks, Content Blocks). Used in outgoing relationship mapping.

---

### `getVariantIdsByProducts($elementIds): array`

Returns variant IDs for products (used in both directions).

---

### `getElementsForType($query, $elementIds, $siteId): array`

Handles Craft queries with:

* Site filtering (`current` or `all`)
* Draft/revision control
* Optional result limiting (`limitPerType`)
* Pagination tracking

---

### `sortResults(array $results): array`

Sorts elements for display using plugin settings:

* By element type
* Or by title

---

## 📦 Events for Extensibility

Developers can hook into these events:

* `EVENT_ELEMENT_TYPE_CONFIG`: Modify which element types are mapped.
* `EVENT_ELEMENT_MAP_DATA`: Inject custom logic for rendering unsupported types.
* `EVENT_ADD_ELEMENTS`: Add custom elements to the map (used for dynamic mapping).

---

## 🔐 Permissions & Titles

### `getTitleForElement()`

Builds a user-friendly title, including:

* Draft/revision status
* Root owner’s title
* Field names for nested elements

### `canView`

Each element's `canView()` method is checked to ensure access permissions.

---

## Settings Used

* `showSites`: Current vs. All site elements
* `showThumbnails`: Whether to display thumbnails for assets
* `limitPerType`: Result count limit per element type
* `showRootOwner`: Toggle use of rootOwner vs nested elements
* `orderResults`: Sort by element type or title
* `showRevisions`: Show revisions in queries

---

## Example Output

```php
[
  'incoming' => [
    [
      'id' => 101,
      'title' => 'Homepage -> Hero Block',
      'url' => '/admin/entries/homepage',
      'icon' => '@appicons/block.svg',
      'color' => 'var(--black)',
      'sort' => '01',
      'canView' => true
    ],
    ...
  ],
  'outgoing' => [ ... ],
  'elementsNotShown' => 3
]
```

---

## Extending the Plugin

To support new element types:

1. Add an entry to `$elementTypeConfig`
2. Write a handler method like `getMyElementTypeElements()`
3. (Optional) Trigger `EVENT_ELEMENT_MAP_DATA` for more control

---

## Summary

The `ElementmapRenderer` is a central service in the Element Map plugin that generates **bidirectional visual maps** of how elements relate to each other in Craft CMS. It is **highly extensible**, performance-conscious, and integrates with a wide range of native and third-party element types.

---

Let me know if you'd like this exported to **Markdown**, **PDF**, or another format.
