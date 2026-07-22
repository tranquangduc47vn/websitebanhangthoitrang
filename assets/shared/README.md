# assets/shared

Canonical home for assets used by **both** the admin panel and the storefront
(e.g. jQuery, CKEditor) under the standardized `assets/` root.

## Status

Scaffolding only. To avoid duplication and route/UI regressions, shared vendor
libraries currently live in their existing locations:

- Admin bundle: `assets/admin/vendor/ckeditor/`, `assets/admin/js/jquery-3.1.1.js`
- Storefront/editor: `public/js/ckeditor/`, `public/js/jquery-3.1.1.js`

## Target mapping (when migrated)

| Library   | Current                                   | Target (`assets/shared/`)      |
| --------- | ----------------------------------------- | ------------------------------ |
| jQuery    | `public/js/jquery-3.1.1.js` + admin copy  | `assets/shared/jquery/jquery-3.1.1.js` |
| CKEditor  | `public/js/ckeditor/` + admin copy        | `assets/shared/ckeditor/`      |

Once migrated, both `assets/admin/` and `assets/frontend/` references should
point at `assets/shared/...` to eliminate the duplicated vendor trees. Perform
this only with runtime verification; keep the old copies until confirmed.
