# assets/frontend

Canonical home for **storefront (public site)** assets under the standardized
`assets/` root, mirroring `assets/admin/`.

## Status

Scaffolding only. The live storefront currently serves its CSS/JS/images from
`public/` through the `public_url()` helper (`base_url('public/')`). Those files
are **intentionally left in place** so no route, URL, or UI changes occur.

This folder documents the target location and is where frontend assets should be
copied when a runtime-verified migration is performed (see
`docs/ASSET-CONVENTION.md`).

## Target mapping (when migrated)

| Current (`public/`)          | Target (`assets/frontend/`) |
| ---------------------------- | --------------------------- |
| `public/site/css/`           | `assets/frontend/css/`      |
| `public/site/js/`            | `assets/frontend/js/`       |
| `public/site/bootstrap/`     | `assets/frontend/bootstrap/`|
| `public/js/raty/`            | `assets/frontend/vendor/raty/` |
| `public/js/jqzoom_ev/`       | `assets/frontend/vendor/jqzoom/` |
| `public/js/product-detail.js`| `assets/frontend/js/product-detail.js` |

Migration must be additive (copy, don't delete), update view paths to
`base_url('assets/frontend/...')`, then verify in a browser before removing the
old references.
