Here is the **Markdown documentation** for the provided Craft CMS controller code (`ElementmapController`). It explains the purpose and function of the controller within the **Element Map** plugin context.

---


# 📘 Documentation: `ElementmapController` – Element Map Plugin for Craft CMS

## Overview

The `ElementmapController` is the web controller for the **Element Map** plugin in Craft CMS. It handles requests to retrieve the **element relationship map** for a given element and renders the results in either **JSON** or a **Twig template**.

This controller is typically called from the Control Panel when viewing an element, to display what other elements reference or are referenced by it.

---

## 📍 Namespace

```php
namespace wsydney76\elementmap\controllers;
```

---

## 🔐 Access Control

```php
protected array|bool|int $allowAnonymous = [];
```

* The controller **does not allow anonymous access**.
* All actions require a logged-in user.
* This ensures that only authenticated users can view element relationship data.

---

## 🚀 Main Action: `actionGetRelations($siteId, $elementId)`

### Purpose:

Handles requests to fetch **incoming** and **outgoing** relationships for a specific element (by `elementId`) on a given site (`siteId`).

---

### 🔒 Requires Login

```php
$this->requireLogin();
```

Ensures only authenticated users can access this route.

---

### 🧩 Element Lookup

```php
$element = Craft::$app->elements->getElementById($elementId, siteId: $siteId);
```

* Retrieves the specified element for the given site context.
* Throws a `NotFoundHttpException` if the element does not exist.

---

### 🔄 Relationship Map Generation

```php
$map = $plugin->renderer->getElementMap($element, $element->siteId);
```

* Uses the `ElementmapRenderer` service to generate:

    * **Incoming** elements (that reference this one)
    * **Outgoing** elements (that this element references)

---

### 🔁 Dual Response Modes

1. **JSON Output**

    * Triggered if the request contains `?json=1`.
    * Returns an array with the full element map:

      ```php
      return $this->asJson([
          'map' => $map,
      ]);
      ```

2. **Twig Template Rendering**

    * Renders `_elementmap/_elementmap_content` with:

        * `element`: The requested element
        * `map`: The relationship map
        * `settings`: Plugin settings
    * Example:

      ```php
      return Craft::$app->view->renderTemplate('_elementmap/_elementmap_content', [...]);
      ```

---

## Example Usage

A request to view the map for an entry (ID `101`, site ID `1`):

```
/actions/elementmap/elementmap/get-relations?siteId=1&elementId=101
```

* Logged-in users see the rendered element map.
* Appending `?json=1` returns the raw data structure.

---

## Summary

The `ElementmapController` acts as the bridge between **requests** and the **ElementmapRenderer** service. It ensures:

* Secure access to relationship maps
* Clean JSON or UI-based responses
* Seamless integration with the Craft Control Panel

---

## Related Components

* [`ElementmapRenderer`](./ElementmapRenderer_Documentation.md): Core logic for building the element map
* `_elementmap/_elementmap_content.twig`: UI template for displaying the element map
* `ElementmapPlugin`: Access point for plugin settings and services

```

---

Let me know if you'd like this saved as a `.md` file (once file saving is available again) or combined with the previous service documentation into a single document.
```
