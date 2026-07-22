# Archive manifest

No file in this archive is part of the active runtime. Restore a group to its
original path if a rollback is required.

| Archived path | Original path | Reason |
| --- | --- | --- |
| `archive/lumino-admin/**` | `public/admin/**` | Lumino HTML demo, Bootstrap 3 assets and duplicate admin theme |
| `archive/site-demo/*.html` | `public/site/*.html` | Static storefront mockups not routed by CodeIgniter |
| `archive/vendor-demos/jqzoom/demos/**` | `public/js/jqzoom_ev/demos/**` | Vendor examples only |
| `archive/vendor-demos/ckeditor/samples/**` | `public/js/ckeditor/samples/**` | CKEditor examples only |

Active admin dependencies were copied to `assets/admin/` before archival.
The retained compatibility files under `public/admin/` may be removed in a
future release only after production access logs confirm no direct requests.

## Rollback

1. Stop writes to the application.
2. Copy the required archived group back to the listed original path.
3. Restore the former view/asset reference from version control.
4. Clear browser cache and run the admin smoke-test checklist.
