# AGENTS.md

## What this is

**Laravel 13 / PHP 8.3** multi-user digital cashbook ("Sistem Manajemen Keuangan"). The spec lives in `planning/` and is the source of truth — read **`planning/BRIEF.md`** and **`planning/ERD.md`** before building features. Docs are in Indonesian; keep UI copy in Indonesian (terms: "Uang Masuk" / "Uang Keluar", Rupiah format).

Auth (Laravel Breeze, Blade) is installed and working. The domain — `accounts`, `categories`, `transactions`, dashboard, reports — is not built yet; only the base `users`/`cache`/`jobs` migrations exist.

## Stack

- Backend: Laravel 13, PHP 8.3, MySQL for local dev (Laragon; db `sistem_manajemen_keuangan_multi_user`, user `root`, no password — see `.env`).
- Auth: Laravel Breeze (Blade stack). Auth routes in `routes/auth.php`; profile wiring in `routes/web.php`.
- Frontend: Blade + **Tailwind CSS v3** (compiled by PostCSS via `postcss.config.js`; `tailwind.config.js` scans `resources/views/**`) + Alpine.js + Vite. `package.json` is ESM (`"type": "module"`) — Vite/PostCSS configs use `export default`.
- Tests: **Pest 5** (wraps PHPUnit). Suites in `tests/Unit` and `tests/Feature`; `tests/Pest.php` already applies `RefreshDatabase` to Feature tests. `phpunit.xml` runs on SQLite `:memory:` — running tests needs no local MySQL.
- Code style: Laravel Pint (`vendor/bin/pint`, default Laravel preset, no config file).

## Commands

- `composer run setup` — first-time setup (composer install, copy `.env`, `key:generate`, `migrate --force`, npm build).
- `composer run dev` — runs `php artisan serve`, `queue:listen`, `pail` (logs), and `vite` concurrently.
- `composer test` — `php artisan config:clear` then `php artisan test` (Pest). Single test: `php artisan test --filter=NameOfTest`.
- `php artisan migrate` — runs against local MySQL (`.env`), NOT the sqlite file tests use.
- `vendor/bin/pint` — format code before finishing work.

## Gotchas

- **Laravel 13 models use PHP attributes, not `$fillable`/`$hidden` properties.** Follow `app/Models/User.php`: `#[Fillable([...])]`, `#[Hidden([...])]`, and `protected function casts(): array` for casts. New models must match this pattern.
- **`.npmrc` sets `ignore-scripts=true`.** npm installs run with `--ignore-scripts`; if a postinstall binary (e.g. esbuild) is missing and Vite fails, run `npm rebuild`.
- Routes use Laravel 11+ style: `bootstrap/app.php` wires routing/middleware; there is no `app/Http/Kernel.php`.
- **Stray `@tailwindcss/vite` v4 dependency:** `package.json` lists it, but it is NOT wired into `vite.config.js`. The real styling pipeline is Tailwind v3 through `postcss.config.js`. Don't "upgrade" the CSS config to v4 syntax unless both package.json and vite.config.js are changed together.

## Architecture rules (non-negotiable, from `planning/`)

MVP tables: `accounts`, `categories`, `transactions` (`recurring_transactions` is a later phase).

- **Data isolation is the core requirement.** Every query must be scoped to the logged-in user, e.g. `Transaction::where('user_id', auth()->id())->findOrFail($id)`. Never trust an ID from the URL alone. Applies to accounts, categories, transactions, and all dashboard/report queries.
- When creating a transaction, validate that its `account` and `category` belong to the same user (not just the transaction's own `user_id`).
- **Do not store a running `balance` column on transactions.** Balance = `accounts.initial_balance + sum(income) - sum(expense)`; recompute from data so edits/deletes stay consistent.
- Prefer `is_active = false` over hard-deleting accounts/categories that have transactions (FKs are `ON DELETE RESTRICT` for `account_id`/`category_id`).
- `categories`: type is `income`/`expense`; unique per user on `(user_id, name, type)`. `accounts`: unique per user on `(user_id, name)`.
- Server-side validation is required (never rely on frontend only).