<p align="center">
    <img src="public/assets/common/media/logo.png" width="96" alt="Resume Studio logo">
</p>

<h1 align="center">Resume Studio</h1>

<p align="center">
    Build, customize, save, and export professional resumes from one focused workspace.
</p>

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13">
    <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php&logoColor=white" alt="PHP 8.3 or newer">
    <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white" alt="Bootstrap 5.3">
    <img src="https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white" alt="Vite 8">
    <img src="https://img.shields.io/badge/Tests-Pest-805AD5" alt="Tested with Pest">
</p>

## Overview

Resume Studio is a Laravel-based resume builder designed to help users move from
a blank page to a polished, print-ready resume. Users create a verified account,
choose a professionally coded template, edit their information beside a live
preview, and retain multiple resumes in their personal dashboard.

Every template is built with Blade and CSS rather than displayed as a static
image. Resume content therefore remains editable, responsive, accessible, and
ready for A4 printing or PDF export.

## Features

- Secure registration, login, logout, and signed email verification.
- Separate administrator authentication with role-based permissions.
- Ordered job-category and subcategory management for organizing resume templates.
- Verified-user middleware protecting the dashboard and resume workspace.
- Six selectable, professionally designed resume templates.
- Responsive template slider with embedded live previews.
- Multiple saved resumes per user with direct edit access from the dashboard.
- Live resume builder with debounced autosaving.
- Personal details, summary, experience, education, skills, projects, courses,
  awards, languages, and custom sections.
- Drag-and-drop ordering for sections and individual entries.
- Section visibility controls, editable section names, and custom sections.
- Profile image uploads stored on the public disk under `images/`.
- Live A4 preview with print and Save as PDF support.
- Ownership authorization to prevent users from accessing another user's resume.
- Responsive Bootstrap interface with application-branded email templates.

## Application Workflow

1. Register an account and verify the submitted email address.
2. Open the dashboard and browse the resume template slider.
3. Select a template to create a separate resume in the user's workspace.
4. Add content, upload a profile image, and organize resume sections.
5. Review changes immediately in the live A4 preview.
6. Print the finished resume or save it as a PDF.
7. Return to the dashboard later to edit any previously saved resume.

## Resume Templates

Template configuration is centralized in
[`config/resume_templates.php`](config/resume_templates.php). The current catalog
contains:

| Slug | Template |
| --- | --- |
| `template-one` | Professional Cyan |
| `template-two` | Classic Blue Sidebar |
| `template-three` | Modern Mint Professional |
| `template-four` | Teal Impact |
| `template-five` | Structured Indigo |
| `template-six` | Indigo Profile Sidebar |

Template Blade files are located in
[`resources/views/resumes/templates`](resources/views/resumes/templates), with
their dynamic and sample partials stored in
[`resources/views/resumes/partials`](resources/views/resumes/partials).

## Technology Stack

| Layer | Technology |
| --- | --- |
| Backend | PHP 8.3+, Laravel 13 |
| Frontend | Blade, Bootstrap 5.3, JavaScript, jQuery |
| Asset pipeline | Vite 8 |
| Database | MySQL by default; Laravel-supported databases can be configured |
| Authentication | Laravel session authentication and email verification |
| File storage | Laravel public filesystem disk |
| Testing | Pest 5, PHPUnit 13 |
| Code style | Laravel Pint |

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and npm
- MySQL or another Laravel-supported database
- A configured mail transport for delivering verification emails

## Installation

Clone the repository, enter the project directory, and install the dependencies:

```bash
composer install
npm install
```

Create the environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, use the following command instead of `cp`:

```powershell
Copy-Item .env.example .env
```

Configure the application URL and database connection in `.env`:

```dotenv
APP_NAME="Resume Studio"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=makeresume
DB_USERNAME=root
DB_PASSWORD=
```

Run the migrations and expose public uploads:

```bash
php artisan migrate
php artisan storage:link
```

The storage link is required for profile images. Uploaded files are saved to
`storage/app/public/images` and served through `/storage/images`.

## Administrator access

Resume Studio has a separate administrator guard, login, roles, and exact
route-level permissions. Seed the initial RBAC records after migrating:

```bash
php artisan db:seed
```

The administration login uses the named route `admin.loginpage`; run
`php artisan route:list --name=admin.loginpage` to inspect its configured URL.
Local development defaults are `admin@example.com` and `password`; override
`ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env` before seeding any shared or
deployed environment. Administrator profile images are stored on the public
disk under `admin-users/`.

## Email Verification

Local development uses the `log` mail driver by default, so verification
messages are written to `storage/logs/laravel.log`. For real email delivery,
configure an SMTP provider:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_SCHEME=smtp
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Verification URLs are signed, throttled, and expire according to
`EMAIL_VERIFICATION_EXPIRE`.

## Running the Application

Start the Laravel server, queue listener, and Vite development server together:

```bash
composer run dev
```

Then open the URL configured in `APP_URL`.

When using Laravel Herd, the PHP application is already served by Herd, so the
frontend development server can be started separately:

```bash
npm run dev
```

## Production Build

Compile optimized frontend assets with:

```bash
npm run build
```

For production deployment, also ensure that:

- `APP_ENV=production`
- `APP_DEBUG=false`
- the database is migrated with `php artisan migrate --force`
- the public storage link exists
- a production mail transport is configured
- the web server points to the `public` directory

## Testing and Code Quality

Run the complete automated test suite:

```bash
php vendor/bin/pest
```

Run only the resume feature tests:

```bash
php vendor/bin/pest tests/Feature/ResumeTest.php
```

Check PHP formatting without modifying files:

```bash
vendor/bin/pint --test
```

Verify that frontend assets compile:

```bash
npm run build
```

## Project Structure

```text
app/
|-- Http/Controllers/       Thin HTTP coordinators for admin, auth, and resume flows
|-- Http/Requests/          Admin CRUD, registration, builder, and upload validation
|-- Http/Resources/         Resume builder JSON representation
|-- Models/                 User, Resume, ResumeSection, and ResumeSectionItem
|-- Notifications/          Branded email verification notification
|-- Policies/               Resume ownership authorization
|-- Services/Admin/         Injected admin CRUD and authentication workflows
|-- Services/Frontend/      Dashboard and resume-page workflows
\-- Services/               Shared resume-builder domain workflows

config/
\-- resume_templates.php    Template catalog and sample content

resources/
|-- css/app.css             Application, builder, and resume template styles
|-- js/                     Slider, selection, autosave, and reordering behavior
|-- views/components/admin/ Reusable admin form fields and permission-aware actions
\-- views/                  Blade layouts, emails, dashboard, builder, and resumes

public/
\-- assets/common/media/    Resume Studio logo and favicon

tests/
\-- Feature/                Authentication, verification, and resume workflows
```

Admin controllers delegate queries and mutations to injected services under
`App\Services\Admin`. Validation stays in Form Requests, while reusable fields
such as inputs, selects, Select2 controls, radios, checkboxes, file inputs, and
inline errors live under `resources/views/components/admin/forms`.

## Adding Another Resume Template

1. Add the sequential Blade file under `resources/views/resumes/templates`.
2. Add any template-specific partials under `resources/views/resumes/partials`.
3. Add an entry and sample data to `config/resume_templates.php`.
4. Add the template's A4, embedded preview, responsive, and print styles.
5. Add feature coverage for selection and dynamic section rendering.
6. Update [`docs/Templates/README.md`](docs/Templates/README.md) with the source
   reference and sequential application slug.

Keep the same sequential slug across configuration, Blade filenames, partials,
CSS namespaces, and tests so future template work remains predictable.
