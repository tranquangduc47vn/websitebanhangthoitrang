# CHANGELOG — Structure Standardization

All changes are **additive and reversible**. No files were deleted, no database
schema changed, and no public route was removed. UI/HTML/CSS/JS is unchanged
except for asset *paths*.

## Phase 1 — Audit & Repair

- Verified every controller view target (`$this->data['temp']` and
  `$this->load->view()`) resolves to an existing file across admin + storefront.
- Confirmed placeholder views `site/product/index.php` and `site/news/index.php`
  exist (prevent 500s on `Product::index` / `News::index`).
- Confirmed redirects use `admin_url()` / `base_url()`.
- **Finding (REVIEW, unchanged):** `views/site/head.php` references a missing
  `jquery.jcarousel.pack.js` (pre-existing 404, harmless). Documented in
  `docs/ASSET-CONVENTION.md`.

## Phase 2 — Standardize Structure

- Documented the logical module map (Admin, Auth, Catalog, Customer, Order,
  Content) in `docs/PROJECT-STRUCTURE.md`. Class names preserved for backward
  compatibility.
- Added `application/core/MY_Frontend_Controller.php` (`BaseFrontendController`)
  with a `render_frontend()` helper for storefront pages (opt-in, additive).
- Admin base (`MY_Admin_Controller`) and admin layout/partial split were already
  in place from the earlier admin standardization; kept as-is.
- **Deferred (REVIEW):** physically moving storefront controllers into module
  sub-folders is **not** done — CI3 maps sub-folders to URL segments and it would
  change public routes. Storefront view relocation (`site/` → `frontend/`) is
  deferred pending runtime verification; target documented.

## Phase 3 — Standardize Assets

- Established the standard asset roots and conventions in
  `docs/ASSET-CONVENTION.md`.
- Added scaffolding + migration mapping: `assets/frontend/README.md`,
  `assets/shared/README.md`.
- Admin assets already consolidated under `assets/admin/` and referenced via
  `base_url()` in the layout partials.
- **Deferred (REVIEW):** storefront assets remain in `public/` (served via
  `public_url()`) to avoid unverifiable path regressions; mapping documented.

## Phase 4 — Runtime & Security

- Created `storage/{logs,cache,temp,sessions}/` (each with `index.html` guard)
  plus `storage/.gitkeep`.
- Repointed runtime paths in `application/config/config.php`:
  - `log_path` → `FCPATH.'storage/logs/'`
  - `cache_path` → `FCPATH.'storage/cache/'`
  - `sess_save_path` → `FCPATH.'storage/sessions/'`
- Updated `.gitignore` to ignore `storage/*` contents while keeping the tracked
  guard files.
- Added `MY_Frontend_Controller`; `MY_Admin_Controller` already present.

## Phase 5 — Archive Legacy

- `archive/` already contains `lumino-admin/`, `site-demo/`, `vendor-demos/` with
  a per-file `MANIFEST.md`.
- Added `archive/README.md` explaining the origin of each folder and the
  no-deletion / rollback rules.

## Phase 6 — Documentation & Tooling

- Added `docs/PROJECT-STRUCTURE.md`, `docs/ROUTE-MAP.md`,
  `docs/ASSET-CONVENTION.md`.
- `docs/FOLDER-AUDIT.md`, `docs/ADMIN-STRUCTURE.md` and
  `tools/find-unused-assets.ps1` already present from the earlier phase.
- Added this `CHANGELOG_STRUCTURE.md`.

## Phase 7 — Storefront layouts & site assets (partial, review stop)

- Added `application/views/site/layouts/` (`head`, `header`, `footer`, `slider`,
  `scripts`, `main`, `sub`). Legacy `site/layout.php`, `site/layoutsub.php`,
  `site/head.php` delegate to layouts (controllers unchanged).
- Copied `public/site/**` → `assets/site/**` (rollback: keep using `public/site`).
- Added `site_asset_url()` in `common_helper.php`; updated layout + views that
  referenced `public/site/` for theme assets.
- Hardened `MY_Frontend_Controller` (`LAYOUT_MAIN`/`LAYOUT_SUB`,
  `render_frontend_main/sub`); required from `MY_Controller.php`.
- Added `docs/FRONTEND-STRUCTURE.md`, `assets/site/README.md`.
- **Deferred:** migrate remaining storefront controllers to `MY_Frontend_Controller`; move
  `public/js/` vendor to `assets/shared/`; remove duplicate `public/site/`.
- **Done (sample):** `Home` extends `MY_Frontend_Controller`, uses `render_frontend_main()`;
  added `render_frontend_view()` helper (single render path).
- **Done (Catalog):** `Product` extends `MY_Frontend_Controller`; all layout renders use
  `render_frontend_sub()`; `catalog.php` loads `product-detail.js` from `assets/site/`.
- **Done (Catalog):** `Hethongcuahang` extends `MY_Frontend_Controller`; `render_frontend_main()`.
- **Done (Cart, SAFE RENDER ONLY):** `Cart` + `Order` extend `MY_Frontend_Controller`; layout via
  `render_frontend_sub()` on `Cart::index`, `Order::index`, `Order::checkout_qr` only. Session
  `custom_cart` / checkout logic untouched.
- **Done (Auth, SAFE RENDER ONLY):** `User` extends `MY_Frontend_Controller`; `login` + `register`
  use `render_frontend_standalone()` (`site/layouts/raw.php`). No forgot-password route. See
  `docs/PROJECT-STATUS.md`.

## Phase 8 — Storefront SEO URL standardization

- Added `application/helpers/seo_url_helper.php` (slugify, `build_*_url`, price slug map).
- Autoload `seo_url` in `application/config/autoload.php`.
- `application/config/routes.php`: canonical `/gio-hang`, `/thanh-toan`, `/tim-kiem`, `/tin-tuc`,
  category SEO + price segments, product `{slug}-p{id}`; legacy paths → `Legacy` controller (301).
- `application/controllers/Legacy.php` — permanent redirects to SEO URLs.
- `Product::catalog_seo`, search SEO, canonical URLs; `News` pagination/canonical helpers.
- Storefront views updated to use URL helpers (no hardcoded `/product/catalog/` or `/cart/`).
- Documentation: `docs/URL-MIGRATION.md`, `docs/seo-smoke-test.php`, `router.php` for local `php -S`.
- **Note:** News detail URL is `/tin-tuc/{slug}-n{id}` (id suffix avoids title collisions).

## Phase 9 — Storefront luxury UI + cart fixes (view/CSS + small core fix)

- **Cart page:** `site/cart/index.php`, `assets/site/css/cart-luxury.css` — centered, no sidebar on cart route.
- **Checkout page:** `site/order/index.php`, `assets/site/css/checkout-luxury.css` — centered luxury form.
- **Header:** cart badge from `custom_cart` in `MY_Controller.php`; red quantity styling in `layout-chrome-2026.css`.
- **PDP:** luxury layout, CSS gallery zoom (`product-detail.js`), related products typography, `product_display_name()`.
- **Footer:** layout chrome updates (`layout-chrome-2026.css`, `layouts/footer.php`).
- **Hethongcuahang:** removed `$catalog` / `total_items` overwrite (fixed empty nav dropdown on store locator page).
- **Handoff doc:** `docs/SESSION-HANDOFF.md` for “tiếp tục làm” without full project re-read.
- **Chạy project (XAMPP / ServBay):** `huong dan vo admin.txt` (PHP 7.4, MySQL, admin login).

## Rollback quick reference

| Change                         | Rollback                                                        |
| ------------------------------ | -------------------------------------------------------------- |
| `seo_url` helper + SEO routes  | Remove helper autoload; revert `routes.php` Legacy block; delete `Legacy.php`. See `docs/URL-MIGRATION.md`. |
| `config.php` runtime paths     | Restore the three lines to `''` / `APPPATH.'cache/sessions/'`. |
| `.gitignore`                   | Revert the "Runtime and generated data" block.                 |
| `MY_Frontend_Controller.php`   | Delete the file (nothing extends it yet).                      |
| New docs / READMEs / scaffolds | Delete the added markdown/dirs (no code depends on them).       |
| `storage/`                     | Delete the folder (only after reverting `config.php`).         |
