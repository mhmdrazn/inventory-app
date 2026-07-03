# Inventory App - PT Telkomsel

Prototype Sistem Manajemen Inventaris Berbasis Web untuk seleksi magang Sistem Informasi PT Telkomsel.

## Tech Stack

- Laravel 13, PHP 8.5, Laravel Breeze (Blade + Alpine.js + Tailwind CSS)
- PostgreSQL via Supabase (cloud-hosted, transaction pooler port 6543)
- barryvdh/laravel-dompdf (export PDF)
- maatwebsite/excel (export Excel)
- Laravel Sanctum (REST API auth)
- Chart.js (dashboard charts)
- Laravel Boost (MCP server for AI-assisted dev)

## Database Schema

Refer to `schema.dbml` in project root for the complete DBML definition.

6 tables: roles, users (extended with role_id), categories, products, borrowings, borrowing_details.

### Relationships

- User belongsTo Role (role_id FK)
- Product belongsTo Category (category_id FK)
- Borrowing belongsTo User (user_id = who created the record)
- Borrowing belongsTo User (approved_by = nullable, admin/manager who approved)
- Borrowing hasMany BorrowingDetail
- BorrowingDetail belongsTo Product
- borrower_name on borrowings is a separate string field (borrower may not be a system user)

### Enums

- product_condition: `baik`, `rusak_ringan`, `rusak_berat`
- borrowing_status: `dipinjam`, `dikembalikan`, `terlambat`

## Roles & Authorization

3 roles enforced via `RoleMiddleware`:

| Role    | Access                                                        |
|---------|---------------------------------------------------------------|
| Admin   | Full access: CRUD products, manage borrowings, dashboard, manage users |
| Staff   | CRUD products, create/manage borrowings. Cannot manage users  |
| Manager | View-only: dashboard, reports, borrowing history. Cannot create/edit/delete |

Route groups are protected per role. Sidebar navigation renders conditionally based on `auth()->user()->role->name`.

## Commands

```
php artisan serve                  # dev server port 8000
npm run dev                        # vite frontend
php artisan migrate:fresh --seed   # reset DB with seed data
php artisan test --compact         # run tests
vendor/bin/pint --dirty            # format changed PHP files
```

## Test Accounts (after seeding)

| Role    | Email                    | Password |
|---------|--------------------------|----------|
| Admin   | admin@telkomsel.test     | password |
| Staff   | staff@telkomsel.test     | password |
| Manager | manager@telkomsel.test   | password |

## File Structure

```
app/Http/Middleware/RoleMiddleware.php
app/Http/Controllers/DashboardController.php
app/Http/Controllers/ProductController.php
app/Http/Controllers/CategoryController.php
app/Http/Controllers/BorrowingController.php
app/Http/Controllers/Api/V1/ProductController.php
app/Http/Controllers/Api/V1/BorrowingController.php
app/Http/Requests/StoreProductRequest.php
app/Http/Requests/UpdateProductRequest.php
app/Http/Requests/StoreBorrowingRequest.php
app/Models/Role.php
app/Models/Category.php
app/Models/Product.php
app/Models/Borrowing.php
app/Models/BorrowingDetail.php
database/seeders/RoleSeeder.php
database/seeders/UserSeeder.php
database/seeders/CategorySeeder.php
database/seeders/ProductSeeder.php
```

## Deployment

- Hosting: Railway (auto-build from GitHub push)
- Database: Supabase PostgreSQL (ap-southeast-1)
- Env vars set via Railway dashboard
- Procfile and nixpacks.toml in project root for Railway build config

## Design

Refer to `design.md` for UI styling decisions, color palette, and component patterns. All views must support dark mode via Tailwind `dark:` prefix.

---

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12
- alpinejs (ALPINEJS) - v3
- tailwindcss (TAILWINDCSS) - v3

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>