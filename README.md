# PharmaCare Backend API

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

<p align="center">
  <strong>A comprehensive pharmacy management system with dual interfaces for pharmacies and suppliers</strong>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#installation">Installation</a> •
  <a href="#api-endpoints">API</a> •
  <a href="#contact">Contact</a>
</p>

---

## Overview

PharmaCare Backend is a full-featured pharmacy management system built with Laravel 12. It provides both RESTful API for mobile/web applications and a web-based supplier portal. The system manages medicines, inventory, orders, invoices, payments, and supplier relationships with real-time notifications.

**Key Capabilities:**
- Complete medicine inventory management with batch tracking
- Order processing workflow (pending → confirmed → completed)
- Multi-language support (API localization)
- Invoice generation with PDF export
- Payment tracking (pending, confirmed, rejected)
- Supplier dashboard with order management
- Real-time notifications via Pusher
- Barcode generation for medicines
- Damaged medicine tracking
- Inventory count reports

---

## Features

### 🔐 Authentication & Authorization
- JWT authentication with Laravel Sanctum
- Role-based access control (Spatie Permission)
- Email verification system
- Google OAuth integration
- Supplier authentication portal
- Profile management

### 💊 Medicine Management
- Complete medicine database with batch tracking
- Medicine categories and pharmaceutical forms
- Brand management
- Alternative medicine suggestions
- Barcode generation (numeric)
- Low quantity alerts
- Expiry date tracking
- Damaged medicine recording

### 📦 Inventory & Stock Control
- Real-time inventory tracking
- Medicine batch management
- Inventory count system
- Low stock notifications
- Quantity adjustment tracking
- Damaged medicine reports

### 🛒 Order Management
- Order workflow: Pending → Confirmed → Completed → Cancelled
- Supplier order portal
- Order filtering by status
- Bulk expiry date updates
- Order export functionality
- PDF order printing

### 🧾 Invoice System
- Invoice creation with multiple items
- Payment status tracking (paid, unpaid, partially paid)
- PDF invoice generation (view/download)
- Invoice archiving system
- Invoice filtering and search
- Payment history tracking

### 💳 Payment Processing
- Payment record management
- Status tracking (pending, confirmed, rejected)
- Payment confirmation workflow
- Supplier payment dashboard

### 👥 Supplier Portal
- Dedicated web dashboard for suppliers
- Order management interface
- Notification system
- Profile and account management
- Invoice viewing and tracking
- Payment status monitoring

### 📊 Reports & Analytics
- Medicine expiry reports
- Inventory snapshot reports
- Category analysis
- Talif (damaged) reports
- Export capabilities

### 🔔 Real-time Notifications
- Pusher integration for live updates
- Supplier notification system
- Mark as read functionality
- Notification history

---

## Tech Stack

### Backend Framework
- **Laravel 12.x** - Latest Laravel version
- **PHP 8.2+** - Programming language
- **MySQL 8.0+** - Primary database

### Authentication
- **Laravel Sanctum** - API token authentication
- **Tymon JWT Auth** - JWT tokens
- **Laravel Socialite** - OAuth (Google)

### Key Packages
- **Spatie Laravel Permission** - Role & permission management
- **DomPDF (Barryvdh)** - PDF generation
- **Laravel Excel (Maatwebsite)** - Excel import/export
- **Picqer Barcode Generator** - Barcode generation
- **Pusher PHP Server** - Real-time notifications
- **Twilio SDK** - SMS notifications
- **Laravel Reverb** - WebSocket server

### Development Tools
- Laravel Pint - Code style
- Laravel Sail - Docker environment
- PHPUnit - Testing

---

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js & NPM
- Git

### Setup Steps

**1. Clone the repository**
```bash
git clone https://github.com/Ghaithehasan/PharmaCare-Backend.git
cd PharmaCare-Backend
```

**2. Install dependencies**
```bash
composer install
npm install
```

**3. Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Configure database**

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pharmacare_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

**5. Configure Pusher (Real-time notifications)**
```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

**6. Configure JWT**
```bash
php artisan jwt:secret
```

**7. Run migrations**
```bash
php artisan migrate --seed
```

**8. Create storage link**
```bash
php artisan storage:link
```

**9. Start the application**
```bash
# Start Laravel server
php artisan serve

# Start queue worker (for notifications)
php artisan queue:work

# Compile frontend assets
npm run dev
```

Your application is now running at `http://localhost:8000`

---

## API Endpoints

### Base URL
```
http://localhost:8000/api
```

### Authentication

All API routes support multi-language via `Accept-Language` header.

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/login-user` | User login |
| POST | `/register-user` | Register new user |
| POST | `/logout-user` | User logout |
| POST | `/verify-email-code` | Verify email with code |
| GET | `/verify-email` | Verify email via link |
| GET | `/show-profile` | Get user profile |
| PUT | `/update-profile` | Update profile |
| GET | `/auth/google` | Initiate Google OAuth |
| GET | `/auth/google/callback` | Google OAuth callback |

### Users & Roles

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST/PUT/DELETE | `/users` | User CRUD operations |
| GET/POST/PUT/DELETE | `/roles` | Role CRUD operations |
| GET | `/show-all-permissions` | List all permissions |

### Medicines

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/medicines` | List all medicines |
| POST | `/medicines` | Add new medicine |
| GET | `/medicines/{id}` | Get medicine details |
| PUT | `/medicines/{id}` | Update medicine |
| DELETE | `/medicines/{id}` | Delete medicine |
| GET | `/medicines/categories` | Get categories |
| POST | `/add-category` | Add new category |
| DELETE | `/delete-category/{id}` | Delete category |
| GET | `/medicines/low-quantity` | Get low stock medicines |
| POST | `/medicines/{id}/update-quantity` | Update quantity |
| POST | `/add-alternative-medicines/{id}` | Add alternative |
| GET | `/show-all-alternatives/{id}` | List alternatives |
| GET | `/generaite-barcode` | Generate barcode |
| GET | `/bar-code/{id}` | Get medicine barcode |

### Medicine Batches

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/medicine-batches` | List all batches |
| GET | `/medicine-batches/{id}` | Get batch details |

### Medicine Forms

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/medicine-forms` | List pharmaceutical forms |
| POST | `/medicine-forms` | Add new form |
| DELETE | `/medicine-forms/{id}` | Delete form |

### Brands

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST/PUT/DELETE | `/brands` | Brand CRUD operations |

### Damaged Medicines

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/show-damaged-medicine` | Search by barcode |
| POST | `/add-damaged-medicine` | Record damaged medicine |
| GET | `/show-all-damaged-medicines` | List all damaged |

### Suppliers

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST/PUT/DELETE | `/suppliers` | Supplier CRUD operations |
| PUT | `/dis-active-supplier/{id}` | Deactivate supplier |
| GET | `/suppliers-purchases/{id}` | Get supplier purchases |
| GET | `/show-supplier-details/{id}` | Get detailed info |

### Orders

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST/PUT/DELETE | `/orders` | Order CRUD operations |
| GET | `/show-pending-orders` | List pending orders |
| GET | `/show-confirmed-orders` | List confirmed orders |
| GET | `/show-completed-orders` | List completed orders |
| GET | `/show-cancelled-orders` | List cancelled orders |

### Invoices

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/create-new-invoice` | Create invoice |
| GET | `/show-all-invoices` | List with filters |
| GET | `/show-paid-invoices` | List paid invoices |
| GET | `/show-unpaid-invoices` | List unpaid invoices |
| GET | `/show-partially-invoices` | List partially paid |
| GET | `/invoices/{id}/download-pdf` | Download PDF |
| GET | `/invoices/{id}/view-pdf` | View PDF |
| GET | `/show-invoice-details/{id}` | Get invoice details |

### Payments

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST/PUT/DELETE | `/payments` | Payment CRUD operations |

### Inventory Counts

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST/PUT/DELETE | `/inventory-counts` | Inventory count operations |

### Reports

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/reports/Medicines-Expiry-date` | Expiry report |
| GET | `/reports/inventory-snapshot` | Inventory snapshot |
| GET | `/reports/category-analysis` | Category analysis |
| GET | `/reports/talif-reports` | Damaged medicine report |

---

## Supplier Web Portal

### Authentication
```
POST /login - Supplier login
POST /logout - Supplier logout
```

### Dashboard Routes
- `/suppliers/dashboard` - Main dashboard
- `/supplier/profile` - View/edit profile
- `/supplier/account` - Account settings

### Order Management
- `/supplier/orders/new-orders` - View new orders
- `/supplier/orders/accepted-orders` - View accepted orders
- `/supplier/orders/show-orders-cancelled` - View cancelled
- `/supplier/orders/show-all-orders` - View all orders
- `/supplier/orders/completed-order` - View completed
- `/supplier/orders/print-order/{id}` - Print order
- `/supplier/orders/exports_orders` - Export orders

### Invoice Management
- `/supplier/invoices` - View all invoices
- `/supplier/invoices/show/{id}` - View invoice details
- `/supplier/invoices/show-pdf/{id}` - View PDF
- `/supplier/invoices/unpaid` - Unpaid invoices
- `/supplier/invoices/paid` - Paid invoices
- `/supplier/invoices/partially` - Partially paid
- `/supplier/invoices/show-archive-invoices` - Archived

### Payment Management
- `/supplier/payments/show-all-payments` - All payments
- `/supplier/payments/show-all-pending-payments` - Pending
- `/supplier/payments/show-all-confirmed-payments` - Confirmed
- `/supplier/payments/show-all-rejected-payments` - Rejected

### Notifications
- `/supplier/notifications` - View notifications
- `/supplier/notifications/{id}` - View specific
- `/supplier/notifications/{id}/mark-as-read` - Mark as read
- `/supplier/notifications/mark-all-as-read` - Mark all

---

## Response Format

**Success:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

**Error:**
```json
{
  "success": false,
  "message": "Operation failed",
  "errors": { ... }
}
```

---

## Database Schema

### Main Tables
```
users                 - System users
roles                 - User roles
permissions           - Access permissions
medicines             - Medicine inventory
medicine_batches      - Medicine batch tracking
medicine_forms        - Pharmaceutical forms
categories            - Medicine categories
brands                - Medicine brands
suppliers             - Supplier information
orders                - Purchase orders
order_items           - Order line items
invoices              - Sales invoices
invoice_items         - Invoice line items
payments              - Payment records
damaged_medicines     - Damaged stock records
inventory_counts      - Stock count records
notifications         - System notifications
alternative_medicines - Medicine alternatives
```

---

## Multi-language Support

All API endpoints support localization via the `Accept-Language` header:

```bash
curl -H "Accept-Language: en" http://localhost:8000/api/medicines
curl -H "Accept-Language: ar" http://localhost:8000/api/medicines
```

---

## Real-time Features

The system uses Pusher for real-time notifications:
- New order notifications for suppliers
- Payment status updates
- Inventory alerts
- Order status changes

Configure in `.env`:
```env
BROADCAST_DRIVER=pusher
```

---

## Testing

Run tests:
```bash
php artisan test
```

Code style check:
```bash
./vendor/bin/pint --test
```

Fix code style:
```bash
./vendor/bin/pint
```

---

## Security Features

- JWT & Sanctum authentication
- Role-based access control
- Email verification
- Password encryption (bcrypt)
- CSRF protection
- SQL injection prevention
- XSS protection
- API rate limiting
- Secure file uploads

---

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit changes (`git commit -m 'Add new feature'`)
4. Push to branch (`git push origin feature/new-feature`)
5. Open a Pull Request

---

## License

This project is open-sourced under the MIT License.

---

## Contact

**Ghaith hasan**

- GitHub: [@Ghaithehasan](https://github.com/Ghaithehasan)
- Email: abrahymtrkyhsn0@gmail.com

---

## Acknowledgments

- Laravel Framework
- All open-source package contributors
- The PHP community

---

<p align="center">
  <strong>⭐ Star this repository if you find it helpful! ⭐</strong>
</p>

<p align="center">
  Built with ❤️ using Laravel
</p>
