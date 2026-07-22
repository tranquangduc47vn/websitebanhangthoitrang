# assets/site

Canonical storefront **theme** assets (Bootstrap, `style.css`, site-specific JS),
mirroring `assets/admin/` for the public shop.

## URL root

```php
site_asset_url('css/style.css'); // → base_url('assets/site/css/style.css')
```

Defined in `application/helpers/common_helper.php` (autoloaded).

## Layout wiring

Storefront shells live in `application/views/site/layouts/`:

- `head.php`, `header.php`, `footer.php`, `slider.php`, `scripts.php`
- `main.php` — home-style page (with slider)
- `sub.php` — inner pages (sidebar rules unchanged)

Legacy entry points `site/layout.php` and `site/layoutsub.php` delegate to these
shells so existing controllers keep working without route changes.

## Migration note

Files were **copied** from `public/site/` (original tree kept for rollback).
Shared vendor JS (jQuery, raty, jqzoom) still loads from `public/js/` via
`public_url()` until a later `assets/shared/` pass.
