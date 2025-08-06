Here is the **Markdown documentation** for the Twig template that renders the **Element Map HUD content** (incoming and outgoing references) using data from the `ElementmapController`.

---


# 📘 Documentation: `_elementmap_content.twig` – Element Map Plugin HUD Display

## Overview

This Twig template is responsible for rendering the **HTML content** shown in the Element Map HUD (popup) when a user clicks the “Relationships” button in the Craft CMS control panel.

It displays:
- Elements that reference the current one (**incoming**)
- Elements referenced by the current one (**outgoing**)
- A message if results were trimmed due to plugin settings

This content is returned via AJAX from `ElementmapController::actionGetRelations()` and injected into a Garnish HUD.

---

## 🔄 Context Variables

The template expects:

### `map` (array):
From `ElementmapRenderer::getElementMap()`, with:
- `incoming`: array of referencing elements
- `outgoing`: array of referenced elements
- `elementsNotShown`: number of trimmed results

### `element` (Craft element):
The element being edited

### `settings` (plugin settings model):
Used for logic like whether to show restricted items

---

## 📌 Section: Incoming References

```twig
<label>{{ 'References to this element'|t('_elementmap') }}</label>
````

* Lists all elements that **reference** the current element.
* If the current element is a **draft or revision**, a message appears:

```twig
{{ 'Showing relations to canonical element'|t('_elementmap') }}
```

* Uses the `showElement()` macro to render each related element.

---

## 📌 Section: Outgoing References

```twig
<label>{{ 'References from this element'|t('_elementmap') }}</label>
```

* Lists all elements that the current element **references**
* Also rendered with `showElement()` macro

---

## ⚠️ Display: Elements Not Shown

If the number of related elements exceeds plugin limits (`limitPerType`), this message is shown:

```twig
{{ map.elementsNotShown }} {{ '{count,plural,=1{more element} other{more elements}} not shown due to limit'|t('_elementmap') }}
```

Uses Craft’s translation pluralization system for proper localization.

---

## 🧩 Macro: `showElement(element, settings)`

This macro handles rendering of **each individual related element**.

### 🔍 Icon Handling

* If `element.image` is defined:

    * Show a thumbnail if it’s an image
    * Use a video or download icon if it's a video or other file
* Else if `element.icon` is defined:

    * Renders the specified SVG icon with optional color

### 🔗 Element Link or Message

* If the user **can view** the element:

    * Renders a clickable link with `data-editable`
* Else:

    * Shows title if `showUnpermitted` is enabled in settings
    * Otherwise, displays:

      ```twig
      {{ 'No permission to view this element'|t('_elementmap') }}
      ```

### 💡 HTML Structure (simplified)

```html
<li>
  <div class="elementmap-item">
    [ICON/THUMBNAIL]
    <a href="[url]">[Title]</a>
  </div>
</li>
```

---

## 🎨 Styling Dependencies

The UI layout depends on styles defined in the other Twig file (`_elementmap_sidebarbutton.twig`), such as:

```css
.elementmap-item {
  display: flex;
  align-items: center;
}
.thumbnail {
  width: 64px;
  height: 48px;
}
.icon-mask {
  fill: [element color];
}
```

---

## 📦 Translation Keys Used

* `'Relationships'`
* `'References to this element'`
* `'References from this element'`
* `'None'`
* `'No permission to view this element'`
* `'Showing relations to canonical element'`
* `'{count,plural,...} not shown due to limit'`

All strings are wrapped in:

```twig
|t('_elementmap')
```

...and should be defined in the `_elementmap.php` translation file.

---

## 🔐 Access Control Display Logic

The element display logic adapts based on both:

* `element.canView`
* Plugin setting: `showUnpermitted`

This ensures that sensitive data is not revealed to unauthorized users, while allowing admins to toggle visibility of restricted elements.

---

## 🔁 Loop Examples

### Incoming Elements

```twig
{% for element in map.incoming %}
    {{ showElement(element, settings) }}
{% endfor %}
```

### Fallback

```twig
{% if map.incoming | length == 0 %}
    <li>{{ 'None'|t('_elementmap') }}</li>
{% endif %}
```

Same applies to `map.outgoing`.

---

## 🧠 Developer Notes

### Customizing the Output

You can:

* Override this template via a [template override path](https://craftcms.com/docs/4.x/extend/plugin-settings.html#overriding-templates)
* Modify icons, thumbnail logic, and permission text
* Add more metadata (e.g. element type, field name)

### Extending Element Display

Add support for other file types, element subtypes, or dynamic styling based on metadata (e.g. draft vs published).

---

## ✅ Summary

This Twig template powers the **core UI** of the Element Map plugin’s HUD popup. It:

* Cleanly separates incoming vs outgoing references
* Uses dynamic logic to render diverse element types
* Respects permission settings
* Displays limits and warnings where appropriate

Combined with the JS/Controller/Renderer code, it provides a seamless, user-friendly visual tool for understanding Craft CMS content relationships.

```

---

Would you like all these Twig and PHP docs bundled into a single developer manual (e.g. in Markdown or PDF)? I can prepare that for you.
```
