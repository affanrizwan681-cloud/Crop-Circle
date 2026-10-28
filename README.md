# CropCircle

CropCircle is a Laravel 12 marketplace connecting local growers, community farmers markets, and customers. Customers can discover seasonal produce, pre-order from multiple farms, coordinate market pickup, message growers, and leave reviews. Farmers manage their catalog, weekly stock, market memberships, pickup slots, and orders. Administrators oversee accounts, approvals, listings, orders, reviews, reports, categories, markets, announcements, and activity logs.

## Current project status

The application includes public marketplace pages, authentication and profiles, customer cart and checkout, order history, farmer and administrator dashboards, management and moderation pages, messaging, notifications, reviews, and sample seed data. It is ready for local configuration and continued feature work.

## Local setup

1. Install PHP 8.2 or newer, Composer, Node.js, and a database supported by Laravel.
2. Copy .env.example to .env if a local environment file does not already exist.
3. Set APP_NAME=CropCircle, APP_URL, CONTACT_EMAIL, and the database connection values in .env. To continue with the previously configured local database, keep its database name marketlink_app.
4. Install dependencies with composer install and npm install.
5. Generate an application key with php artisan key:generate.
6. Run database migrations and sample data with php artisan migrate --seed.
7. Link uploaded product images with php artisan storage:link.
8. Build frontend assets with npm run build.
9. Start the local server with php artisan serve and open the URL Laravel displays.

Seeded accounts are for local development only. Existing demonstration account email addresses are retained for compatibility with the local database. The contact form sends to CONTACT_EMAIL; MAIL_MAILER=log writes messages to the local log, while production requires a configured mail transport and support address.

## Main areas

- app/Http/Controllers - authentication, customer, farmer, admin, marketplace, order, messaging, and review workflows.
- resources/views - public pages and customer, farmer, and admin interfaces.
- routes/web.php - public, authenticated, and role-protected routes.
- database/migrations and database/seeders - application schema and demonstration data.

## Continuation notes

Preserve the existing Laravel 12 structure and role workflows. Before extending a feature, inspect its controller, route, model, migration, and Blade view together. Use CropCircle as the displayed product name. The previous local database name marketlink_app stays in place to preserve compatibility with the existing developer database; it is infrastructure configuration rather than public branding.
