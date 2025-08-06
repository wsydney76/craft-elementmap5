Here’s the **Markdown documentation** for your Craft CMS **Twig template**, which generates and wires up the “Relationships” button and the UI for the Element Map plugin.

---


# 📘 Documentation: `_elementmap_sidebarbutton.twig` – Element Map Plugin UI Button

## Overview

This Twig template renders a **“Relationships” button** in the Craft CMS control panel, allowing editors to view **incoming** and **outgoing** relationships for the currently edited element.

It is used by the plugin's PHP service (`ElementmapService`) to inject the button into the sidebar of Entry, Asset, Category, User, Product, and other element types.

---

## 📌 Button Output

### HTML Output

```twig
<a class="btn {{ id }}" id="{{ id }}">{{ 'Relationships'|t('_elementmap') }}</a>
````

* A random ID (`elementmap-button_{{ random number }}`) ensures uniqueness, even if multiple buttons are rendered on the same page.
* Uses Craft's `|t` filter for localization via the `_elementmap` translation domain.

---

## 🧠 JavaScript Functionality

### ✅ Feature: AJAX Relationship Map Loader

The following JavaScript is attached to the button:

```js
$.get(url).done(function(data) {
  const hud = new Garnish.HUD($btn, data, {
    orientations: ['top', 'bottom', 'right', 'left'],
    hudClass: 'hud guide-hud',
  });
})
```

#### Key Behaviors:

* Builds the URL using `cpUrl('elementmap-getrelations/siteId/elementId')`
* Uses jQuery `.get()` to request rendered map content
* Injects result into a Craft HUD popup attached to the button

#### Error Handling:

If the AJAX call fails, it alerts the user and logs the error in the browser console.

---

### 🧪 Provisional Draft Detection

The second JavaScript block listens for `createProvisionalDraft` events in Craft 5:

```js
Garnish.on(Craft.ElementEditor, 'createProvisionalDraft', function(ev) {
    var elementId = ev.target.settings.elementId;
    elementmapElementId = elementId;
});
```

* Updates the element ID used by the AJAX button when a provisional draft is created.
* Prevents the wrong element from being used in HUD when editing drafts.

> ⚠️ **Known issue**: This logic doesn’t currently catch all provisional draft creations in **slideout editors**. See TODO comment in source.

---

## 🎨 CSS Styling

This template includes two CSS blocks.

### 1. General Styles

```css
.elementmap svg { width: 32px; height: 32px; padding-right: 12px; }
.elementmap label { font-weight: bold; }
.elementmap ul { padding: 12px 0; }
.elementmap .thumbnail { padding-right: 12px; }
.elementmap-table svg { width: 16px; height: 16px; }
.elementmap-info { margin-top: 6px; font-size: 13px; }
.elementmap-item { display: flex; align-items: center; }
```

Styles for elements inside the map HUD, including icons, thumbnails, and layout.

### 2. Button Margin Styles

```css
.{{ id }} { margin: 6px 0; }
.edit-global-set .{{ id }} { margin-top: 36px; }
```

Applies spacing to the button based on context (standard vs global set editor).

---

## 🔐 Dynamic ID Handling

```twig
{% set nr = random(999999999) %}
{% set id = "elementmap-button_#{nr}" %}
```

* A random number (`nr`) is used to avoid ID collisions.
* All related JS variables, selectors, and element attributes are tied to this dynamic ID.

---

## 🧩 Integration Summary

| Feature            | Description                                                  |
| ------------------ | ------------------------------------------------------------ |
| **Rendered By**    | `ElementmapService::renderSidebarButton()`                   |
| **Triggered From** | CP sidebar for supported element types                       |
| **Displays**       | HUD overlay with relationship map                            |
| **Backed By**      | AJAX request to `ElementmapController::actionGetRelations()` |
| **Dependencies**   | jQuery, Garnish.HUD, Craft CP styles                         |

---

## 🔄 Developer Customizations

To modify behavior or extend it:

* Customize the AJAX request or its payload
* Replace HUD with a modal or drawer if needed
* Add support for detecting slideout provisional drafts more robustly
* Adjust the CSS styling for consistency with your control panel theme

---

## Summary

This Twig template is a **UI component** that adds a dynamic, AJAX-powered **“Relationships” button** into Craft CMS's element editors. It integrates tightly with the plugin’s backend to display visual maps of references and relations — helping content editors better understand and navigate content dependencies.

```

---

Let me know if you'd like to:
- Combine this documentation with the other pieces into a single dev doc
- Export to Markdown or PDF (once file saving works again)
- Add visuals or embed screenshots for the button/HUD

I'm happy to help format it further!
```
