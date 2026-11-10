# MotoCare

**A web application for motorcycle service booking and workshop operations.** MotoCare helps customers book services, track repair progress, and submit payment receipts. Administrators can manage workshop operations from a single dashboard.

> Academic and portfolio project. Demo accounts and sample data are intended for local development.

## Features

### Customer

- Register, sign in, and manage account details.
- Create service bookings with one or more services, motorcycle details, a preferred schedule, mechanic selection, and service notes.
- Select available spare parts and view an estimated total.
- Receive a tracking code to check service status and progress updates.
- Upload payment receipts for bank transfer or QRIS payments.
- View booking history and notifications.

### Administrator

- View workshop summaries and the service queue.
- Manage bookings, service status, schedules, mechanics, and payment information.
- Verify submitted payment receipts.
- Manage services, pricing, spare part inventory, and mechanics.
- Create mechanic login accounts and assign mechanics to bookings.
- Let assigned mechanics view their work queue, update service progress, and record installed spare parts with quantities.
- Deduct inventory atomically when parts are recorded as installed, update booking totals, and show configurable low stock alerts on the admin dashboard.
- Export booking data.

## Tech Stack

- PHP 8.2+
- Laravel 12
- Blade templates
- MySQL (the project is configured to use a MySQL connection)
- PHPUnit for testing

PHP dependencies are managed with Composer. This repository does not include a `package.json` or a separate frontend build pipeline.

## Requirements

- PHP 8.2 or later, with the extensions required by Laravel
- Composer
- MySQL and an empty database accessible to the application

## Local Installation

1. Clone the repository and enter the project directory:

   ```bash
   git clone <REPOSITORY-URL>
   cd <PROJECT-DIRECTORY>
   ```

2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Create a local environment file from the example:

   ```bash
   cp .env.example .env
   ```

   On Windows Command Prompt, use `copy .env.example .env`. Update `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` to match your local MySQL configuration. Create the database before continuing.

4. Generate the application key, run migrations, and seed demo data:

   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

5. Start the local development server:

   ```bash
   php artisan serve
   ```

   Open the address shown by Artisan, usually `http://127.0.0.1:8000`.

## Demo Accounts

The database seeder creates these accounts. Both use the password `password`.

| Role | Email |
| --- | --- |
| Administrator | `admin@motocare.test` |
| Customer | `alex@motocare.test` |

> Demo accounts and seeded data are for local demonstration only. Replace the credentials before using the application in another environment.

## Application Workflow

1. A customer signs in and selects services to create a booking.
2. The application validates the selected services and spare part stock, calculates an estimated total, and generates a tracking code.
3. An administrator assigns a mechanic and verifies payment when the customer submits a receipt.
4. The assigned mechanic updates service progress and records parts used. The application updates stock, totals, and customer notifications.
5. The customer checks service progress and history from the booking detail page.

## Project Structure

```text
app/                  Application models, controllers, middleware, and support code
database/migrations/  Database schema and migrations
database/seeders/     Sample data for local demonstrations
resources/views/      Blade pages for customers, administrators, and authentication
routes/               Web and authentication routes
tests/Feature/        Application feature tests
```

## Tests

Run the test suite with:

```bash
php artisan test
```

The suite includes coverage for administrator operations, notifications, payment workflows, profiles, spare part booking, and tracking privacy.

## Security Notes

- Do not commit `.env` files, credentials, or real user data.
- Demo accounts use a simple password and are intended only for local development.
- Configure payment receipt storage, email, and deployment settings before production use.

## About

MotoCare is a web application project focused on digitizing motorcycle workshop booking and service operations. To make this repository more useful as a portfolio, add application screenshots, a live demo URL, contributor information, and an accurate description of your individual contributions.
