# Food Delivery Backend

> REST API backend for a food delivery app — manages menu, orders, payments, riders, and real-time tracking.

A full-featured REST API backend for a food delivery application built with Laravel. It manages the complete order lifecycle — from browsing menu categories and products, through cart and checkout, to rider assignment, order tracking, and delivery — with real-time event broadcasting, role-based access, and admin analytics.

## Tech Stack

| Layer | Technology |
|-------|------------|
| Language | PHP 8.3+ |
| Framework | Laravel 13 |
| Auth | Laravel Sanctum (token-based) |
| Roles & Permissions | Spatie Laravel Permission |
| Real-time Broadcasting | Laravel Reverb |
| Queue / Jobs | Laravel Horizon (Redis-backed) |
| Database | PostgreSQL (Docker) / SQLite (local dev) |
| DB Migrations & Schema | Doctrine DBAL |
| Frontend Build | Vite + Tailwind CSS 4 |
| Containerization | Docker (PHP 8.4 CLI Bookworm) |
| Testing | PHPUnit 12 |

## Features

- **Menu Management** — Categories, products, and size/price variants with sorting, filtering, and image uploads.
- **Customer & Address Management** — Customer profiles with multiple delivery addresses (default address support).
- **Shopping Cart** — Per-customer cart with add/update/remove/clear, duplicate merging, and abandoned cart detection (>24h).
- **Coupon System** — Fixed and percentage discount codes with minimum order value and expiry validation.
- **Order Lifecycle** — Full state machine: `pending → confirmed → preparing → ready_for_pickup → picked_up → on_the_way → delivered` (with cancellation). Status transitions dispatch real-time events.
- **Payment Processing** — Supports Cash on Delivery (COD), EasyPaisa, and JazzCash. Manual payment methods require transaction ID and receipt upload; orders start as `awaiting_verification`.
- **Rider Management** — Rider profiles, online/availability status, GPS location tracking, rider assignment to orders, and live order tracking.
- **Real-time Events** — Order placed, confirmed, prepared, delivered, and rider assigned events broadcast on a public channel via Laravel Reverb.
- **In-app Notifications** — Morphable notifications for customers and riders triggered on key order lifecycle events.
- **Admin Dashboard & Reports** — Stats (revenue, order counts by status), today's summary, top products, sales summary, and orders-by-status breakdown.
- **Restaurant Settings** — Singleton config for restaurant name, phone, address, delivery fee, free delivery threshold, tax %, payment account details, currency, and opening hours.
- **Role-Based Access** — Admin, Manager, Rider, and Customer roles via Spatie.

## Project Structure

```
app/
├── Events/             # Broadcast events (OrderPlaced, OrderConfirmed, etc.)
├── Http/
│   ├── Controllers/Api/  # All API controllers
│   └── Requests/         # Form request validation classes
├── Listeners/          # Notification creators for events
├── Models/             # Eloquent models (16 total)
database/
├── factories/          # Model factories
├── migrations/         # 25+ migration files
├── seeders/            # Seeders for roles, admin, categories, products, orders, settings
routes/
├── api.php             # All API routes
```

## Getting Started

### Prerequisites

- PHP 8.3+
- Composer 2
- Node.js 18+ & npm
- PostgreSQL (for Docker) or SQLite (for local dev)

### Local Setup (Without Docker)

```bash
# Clone the repository
git clone <repository-url>
cd food-delivery-backend

# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure database in .env (SQLite for local dev)
# DB_CONNECTION=sqlite
# touch database/database.sqlite

# Run migrations and seed
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

The API will be available at `http://localhost:8000/api`.

### Docker Setup

> **Note:** The Docker image uses PostgreSQL. Make sure to update your `.env` with PostgreSQL connection details before building.

```bash
# Update .env for PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=your-postgres-host
DB_PORT=5432
DB_DATABASE=food_delivery
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Build and run
docker build -t food-delivery-backend .
docker run -p 8000:8000 food-delivery-backend
```

The entrypoint automatically:
1. Installs Composer dependencies
2. Creates `.env` from `.env.example` (if missing)
3. Generates `APP_KEY` (if missing)
4. Clears framework caches
5. Runs database migrations
6. Starts the development server on port 8000

### Run Everything in Dev Mode

```bash
composer dev
```

This starts concurrently:
- **server** — `php artisan serve`
- **queue** — `php artisan queue:listen`
- **logs** — `php artisan pail` (live log tail)
- **vite** — `npm run dev` (asset compilation with HMR)

## Seeded Data

After running `php artisan db:seed`, the database includes:

| Entity | Details |
|--------|---------|
| Admin User | `admin@test.com` / `password123` |
| Roles | admin, manager, rider, customer |
| Categories | Pizza, Burgers, Drinks, Shawarma, Fries, Pasta |
| Products | 10 products with size variants (Small/Medium/Large, Regular/Meal) |
| Customers | 3 customers with addresses in Multan, Punjab |
| Orders | 20 randomized orders with line items from the last 30 days |
| Restaurant | "Flavors" with delivery fee, tax, and payment settings |

## API Reference

### Public Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/login` | Authenticate admin user, returns Sanctum token |
| POST | `/api/upload` | Upload an image file (max 5MB) |

### Authenticated Routes (`Authorization: Bearer <token>`)

<details>
<summary><strong>Authentication</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/logout` | Revoke current token |
| GET | `/api/me` | Get authenticated user |

</details>

<details>
<summary><strong>Categories</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/categories` | List all categories |
| POST | `/api/categories` | Create a category |
| GET | `/api/categories/{id}` | Get a category |
| PUT | `/api/categories/{id}` | Update a category |
| DELETE | `/api/categories/{id}` | Delete a category |

</details>

<details>
<summary><strong>Products</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/products` | List products (filter: `search`, `category_id`, `is_active`, `is_featured`) |
| POST | `/api/products` | Create a product |
| GET | `/api/products/{id}` | Get a product |
| PUT | `/api/products/{id}` | Update a product |
| DELETE | `/api/products/{id}` | Delete a product |

</details>

<details>
<summary><strong>Product Variants</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/variants` | List all variants |
| POST | `/api/variants` | Create a variant |
| GET | `/api/variants/{id}` | Get a variant |
| PUT | `/api/variants/{id}` | Update a variant |
| DELETE | `/api/variants/{id}` | Delete a variant |

</details>

<details>
<summary><strong>Customers</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/customers` | List all customers |
| POST | `/api/customers` | Create a customer |
| GET | `/api/customers/{id}` | Get a customer |
| PUT | `/api/customers/{id}` | Update a customer |
| DELETE | `/api/customers/{id}` | Delete a customer |
| GET | `/api/customers/{id}/orders` | Get a customer's orders |

</details>

<details>
<summary><strong>Addresses</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/customers/{id}/addresses` | List customer addresses |
| POST | `/api/customers/{id}/addresses` | Create an address |
| GET | `/api/customers/{id}/addresses/{addressId}` | Get an address |
| PUT | `/api/customers/{id}/addresses/{addressId}` | Update an address |
| DELETE | `/api/customers/{id}/addresses/{addressId}` | Delete an address |

</details>

<details>
<summary><strong>Orders</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/orders` | List orders (filter/sort support) |
| POST | `/api/orders` | Place a new order |
| GET | `/api/orders/{id}` | Get an order |
| PUT | `/api/orders/{id}` | Update an order |
| DELETE | `/api/orders/{id}` | Delete an order |
| PATCH | `/api/orders/{id}/status` | Update order status (state machine) |
| PATCH | `/api/orders/{id}/payment` | Update payment status |
| GET | `/api/orders/{id}/timeline` | Get order status timeline |

</details>

<details>
<summary><strong>Cart</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/carts/{customerId}` | Get customer cart |
| POST | `/api/carts/{customerId}/items` | Add item to cart |
| PATCH | `/api/carts/items/{cartItemId}` | Update cart item quantity |
| DELETE | `/api/carts/items/{cartItemId}` | Remove cart item |
| DELETE | `/api/carts/{customerId}` | Clear entire cart |
| GET | `/api/carts/abandoned` | List abandoned carts (>24h old) |

</details>

<details>
<summary><strong>Coupons</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/coupons` | List all coupons |
| POST | `/api/coupons` | Create a coupon |
| GET | `/api/coupons/{id}` | Get a coupon |
| PUT | `/api/coupons/{id}` | Update a coupon |
| DELETE | `/api/coupons/{id}` | Delete a coupon |
| POST | `/api/carts/{customerId}/coupon` | Apply coupon to cart |

</details>

<details>
<summary><strong>Riders</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/riders` | List all riders |
| POST | `/api/riders` | Create a rider |
| GET | `/api/riders/{id}` | Get a rider |
| PUT | `/api/riders/{id}` | Update a rider |
| DELETE | `/api/riders/{id}` | Delete a rider |
| GET | `/api/riders/{id}/location` | Get rider's latest location |
| PATCH | `/api/orders/{id}/assign-rider` | Assign rider to order |
| GET | `/api/orders/{id}/track` | Track order via rider location |
| GET | `/api/rider/orders` | Get rider's assigned orders |
| POST | `/api/rider/location` | Update rider GPS location |
| POST | `/api/rider/status` | Toggle rider online/available status |

</details>

<details>
<summary><strong>Notifications</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/notifications` | List notifications (filter: `type`, `id`, `unread_only`) |
| GET | `/api/notifications/{id}` | Get a notification |
| DELETE | `/api/notifications/{id}` | Delete a notification |
| GET | `/api/notifications/unread-count` | Get unread notification count |
| POST | `/api/notifications/{id}/read` | Mark notification as read |
| POST | `/api/notifications/read-all` | Mark all notifications as read |

</details>

<details>
<summary><strong>Reports</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/reports/stats` | Aggregate stats (counts, revenue) |
| GET | `/api/reports/dashboard` | Today's summary, pending orders, online riders, monthly sales, weekly income |
| GET | `/api/reports/top-products` | Top 10 products by quantity sold |
| GET | `/api/reports/sales-summary` | Sales summary for delivered orders |
| GET | `/api/reports/orders-by-status` | Order count breakdown by status |

</details>

<details>
<summary><strong>Settings</strong></summary>

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/settings` | Get restaurant settings |
| PUT | `/api/settings` | Update restaurant settings |

</details>

## Testing

```bash
# Run all tests
composer test

# Or directly
php artisan test
```

Tests use an SQLite in-memory database with synchronous queue and array cache.

## License

MIT

## GitHub Topics

Add these topics to your repository settings for better discoverability:

```
laravel, php, rest-api, food-delivery, backend, sanctum, real-time, broadcasting, laravel-reverb, laravel-horizon, spatie-permission, postgresql, docker, ecommerce, order-management, rider-tracking, cart-system, coupon-system, payment-processing, admin-dashboard
```
