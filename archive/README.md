# archive/

Legacy, demo, and backup material moved out of the active source tree. **Nothing
here is deleted** — every item can be restored to its original path. A precise,
per-file record with original locations and rollback commands lives in
[`MANIFEST.md`](./MANIFEST.md).

## Folder origins

| Folder            | Origin & purpose                                                                                     |
| ----------------- | ---------------------------------------------------------------------------------------------------- |
| `lumino-admin/`   | The original **Lumino** admin theme demo (static `*.html`, its Bootstrap 3 CSS/JS, fonts, sample charts/tables). Replaced by the standardized theme in `assets/admin/`. |
| `site-demo/`      | Static storefront demo pages (`index.html`, `product.html`, `cart.html`, `contact.html`) shipped with the template. Not used by any route. |
| `vendor-demos/`   | Bundled third-party **demo/sample** payloads: `ckeditor/samples/*` and `jqzoom/demos/*`. Only the runtime library files are kept in `assets/`/`public/`. |

## Rules

- Do **not** delete anything in this folder.
- To restore an item, follow the rollback command for that entry in `MANIFEST.md`.
- New legacy material should be moved here (not deleted) and recorded in `MANIFEST.md`.
