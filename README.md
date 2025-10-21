# PharmaCare Backend API

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

<p align="center">
  <strong>A comprehensive RESTful API for pharmacy management built with Laravel</strong>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#installation">Installation</a> •
  <a href="#api-documentation">API Docs</a> •
  <a href="#contact">Contact</a>
</p>

---

## Overview

PharmaCare Backend is a robust pharmacy management system designed to streamline pharmaceutical operations. Built with Laravel, it provides a complete RESTful API for managing inventory, sales, purchases, customers, and suppliers.

This system automates daily pharmacy workflows including:
- Inventory tracking and stock management
- Sales and purchase order processing
- Medicine expiry date monitoring
- Customer and supplier relationship management
- Comprehensive reporting and analytics

---

## Features

### Authentication & Security
- Secure authentication using Laravel Sanctum
- Role-based access control (Admin, Pharmacist, Cashier)
- Protection against SQL Injection and XSS attacks
- Password encryption with bcrypt
- API rate limiting

### Medicine & Inventory Management
- Complete medicine database with detailed information
- Real-time stock level tracking
- Automatic expiry date alerts
- Low stock notifications
- Category-based medicine classification
- Advanced search and filtering

### Sales Management
- Create and manage sales invoices
- PDF invoice generation
- Sales history tracking
- Return processing
- Automatic tax calculations

### Purchase Management
- Record supplier purchases
- Automatic inventory updates
- Purchase cost tracking
- Complete purchase history

### Customer & Supplier Management
- Customer database with purchase history
- Supplier information management
- Contact details and addresses
- Transaction records

### Reports & Analytics
- Sales reports (daily, weekly, monthly, yearly)
- Inventory status reports
- Profit and loss statements
- Best-selling medicines analysis
- Export to Excel and PDF

---

## Tech Stack

- **Laravel 11.x** - PHP Framework
- **PHP 8.2+** - Programming Language
- **MySQL 8.0+** - Database
- **Laravel Sanctum** - API Authentication
- **Spatie Laravel Permission** - Role & Permission Management
- **Laravel Excel** - Data Export/Import
- **DomPDF** - PDF Generation

---

## Installation

### Prerequisites

Ensure you have the following installed:
- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Git

### Setup Instructions

**1. Clone the repository**
```bash
git clone https://github.com/Ghaithehasan/PharmaCare-Backend.git
cd PharmaCare-Backend
```

**2. Install dependencies**
```bash
composer install
```

**3. Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Configure database**

Edit your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pharmacare_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

**5. Create database**
```bash
mysql -u root -p
CREATE DATABASE pharmacare_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

**6. Run migrations and seeders**
```bash
php artisan migrate --seed
```

**7. Create storage link**
```bash
php artisan storage:link
```

**8. Start the server**
```bash
php artisan serve
```

Your API is now running at `http://localhost:8000`

---

## Quick Start

### Default Credentials

After running migrations with seed data:

**Admin Account:**
```
Email: admin@pharmacy.com
Password: password
```

**Pharmacist Account:**
```
Email: pharmacist@pharmacy.com
Password: password
```

### Testing the API

**Login Request:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@pharmacy.com",
    "password": "password"
  }'
```

**Get Medicines:**
```bash
curl -X GET http://localhost:8000/api/medicines \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

## API Documentation

### Base URL
```
http://localhost:8000/api
```

### Authentication
Protected endpoints require a Bearer token in the header:
```
Authorization: Bearer {your_access_token}
```

### Endpoints

#### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/login` | User login |
| POST | `/register` | Register new user |
| POST | `/logout` | User logout |
| GET | `/user` | Get current user info |

#### Medicines
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/medicines` | List all medicines |
| POST | `/medicines` | Add new medicine |
| GET | `/medicines/{id}` | Get medicine details |
| PUT | `/medicines/{id}` | Update medicine |
| DELETE | `/medicines/{id}` | Delete medicine |
| GET | `/medicines/expiring` | Get expiring medicines |
| GET | `/medicines/low-stock` | Get low stock medicines |

#### Sales
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/sales` | List all sales |
| POST | `/sales` | Create new sale |
| GET | `/sales/{id}` | Get sale details |
| GET | `/sales/{id}/invoice` | Download PDF invoice |

#### Purchases
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/purchases` | List all purchases |
| POST | `/purchases` | Record new purchase |
| GET | `/purchases/{id}` | Get purchase details |

#### Suppliers
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/suppliers` | List all suppliers |
| POST | `/suppliers` | Add new supplier |
| GET | `/suppliers/{id}` | Get supplier details |
| PUT | `/suppliers/{id}` | Update supplier |
| DELETE | `/suppliers/{id}` | Delete supplier |

#### Customers
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/customers` | List all customers |
| POST | `/customers` | Add new customer |
| GET | `/customers/{id}` | Get customer details |
| PUT | `/customers/{id}` | Update customer |

#### Reports
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/reports/sales` | Sales report |
| GET | `/reports/inventory` | Inventory report |
| GET | `/reports/profit` | Profit report |

### Response Format

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    ...
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Operation failed",
  "errors": {
    "field": ["Error message"]
  }
}
```

---

## Database Schema

### Main Tables

```
users              - System users and staff
roles              - User roles (Admin, Pharmacist, etc.)
permissions        - Access permissions
medicines          - Medicine inventory
categories         - Medicine categories
suppliers          - Supplier information
customers          - Customer database
sales              - Sales transactions
sale_items         - Sale line items
purchases          - Purchase orders
purchase_items     - Purchase line items
notifications      - System notifications
```

---

## Testing

Run all tests:
```bash
php artisan test
```

Run specific tests:
```bash
php artisan test --filter=MedicineTest
```

---

## Project Structure

```
PharmaCare-Backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # API Controllers
│   │   ├── Middleware/     # Custom Middleware
│   │   ├── Requests/       # Form Requests
│   │   └── Resources/      # API Resources
│   ├── Models/             # Eloquent Models
│   └── Services/           # Business Logic
├── database/
│   ├── migrations/         # Database Migrations
│   ├── seeders/           # Database Seeders
│   └── factories/         # Model Factories
├── routes/
│   ├── api.php            # API Routes
│   └── web.php            # Web Routes
└── tests/                 # Application Tests
```

---

## Security

This project implements:
- Password encryption using bcrypt
- CSRF protection
- SQL Injection prevention
- XSS attack protection
- API rate limiting
- Input validation
- HTTPS ready

---

## Contributing

Contributions are welcome! To contribute:

1. Fork the project
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## Contact

**Ghaith Ehasan**

- GitHub: [@Ghaithehasan](https://github.com/Ghaithehasan)
- Email: abrahymtrkyhsn0@gmail.com

---

## Acknowledgments

- Laravel Framework
- All open-source contributors
- The amazing PHP community

---

<p align="center">
  <strong>⭐ If you find this project useful, please give it a star! ⭐</strong>
</p>

<p align="center">
  Made with ❤️ by Ghaith Ehasan
</p>
