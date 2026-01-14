# Payout System API

A comprehensive Laravel-based payout management system with support for both bank transfers and mobile financial services (MFS). This system provides secure API endpoints for merchants to process bulk payouts, track transactions, and manage balances.

## 📋 Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [System Architecture](#system-architecture)
- [Installation](#installation)
- [Configuration](#configuration)
- [API Documentation](#api-documentation)
- [Authentication](#authentication)
- [Security Features](#security-features)
- [Database Schema](#database-schema)
- [Testing](#testing)
- [Deployment](#deployment)
- [License](#license)

## ✨ Features

### Core Functionality
- **Dual Payout Systems**: Support for both bank transfers and MFS (Mobile Financial Services)
- **Bulk Processing**: Create and manage multiple payouts in a single API call
- **Merchant Management**: Multi-tenant architecture with merchant-specific balances and settings
- **Real-time Balance Tracking**: Automatic balance updates with comprehensive history
- **Webhook Integration**: Automated status notifications via webhooks
- **Batch Tracking**: Track and query payout batches by batch ID or reference key

### Security Features
- **Token-Based Authentication**: Laravel Sanctum with 24-hour token expiration
- **Role-Based Access Control**: Admin, Merchant, and User roles
- **Secure API Endpoints**: All sensitive endpoints protected with authentication middleware
- **Audit Trail**: Complete webhook logging and balance history

### Admin Features
- **Financial Institution Management**: Configure supported banks and MFS providers
- **Merchant Administration**: Create and manage merchant accounts
- **Balance Management**: Add/deduct merchant balances with remarks
- **Approval Workflow**: Multi-level approval system for payouts
- **User Management**: Role-based user administration

## 🛠 Technology Stack

- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/SQLite (configurable)
- **API Documentation**: L5-Swagger (OpenAPI/Swagger)
- **Excel Export**: Maatwebsite Excel
- **Frontend**: Vite + JavaScript
- **Queue System**: Database-backed queues

## 🏗 System Architecture

### Models
- `User` - System users with role-based access
- `Merchant` - Merchant accounts with API credentials
- `MerchantBalance` - Real-time merchant balance tracking
- `BalanceHistory` - Complete audit trail of balance changes
- `Payout` - Bank transfer payout records
- `MFSPayout` - Mobile financial service payout records
- `FinancialInstitution` - Supported banks and MFS providers
- `WebhookLog` - Webhook delivery logs

### Controllers
- `MerchantAuthController` - Merchant authentication and balance queries
- `PayoutApiController` - Bank payout API endpoints
- `MFSPayoutApiController` - MFS payout API endpoints
- `PayoutController` - Admin payout management
- `MFSPayoutController` - Admin MFS payout management
- `MerchantController` - Merchant administration
- `BalanceController` - Balance management
- `UserController` - User management
- `FIController` - Financial institution management

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL or SQLite

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd payoutsystem
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=payoutsystem
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed database (optional)**
   ```bash
   php artisan db:seed
   ```

8. **Start development server**
   ```bash
   php artisan serve
   ```

   Or use the comprehensive dev command:
   ```bash
   composer dev
   ```
   This runs server, queue worker, logs, and Vite concurrently.

## ⚙️ Configuration

### Token Expiration
Tokens expire after 24 hours. Configure in `config/sanctum.php`:
```php
'expiration' => 1440, // 1 day in minutes (24 * 60)
```

### Webhook Configuration
Set webhook URL for each merchant in the admin panel or via database:
```sql
UPDATE merchants SET webhook_url = 'https://merchant.com/webhook' WHERE id = 1;
```

### Queue Configuration
Configure queue connection in `.env`:
```env
QUEUE_CONNECTION=database
```

Run queue worker:
```bash
php artisan queue:listen --tries=1
```

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Authentication Endpoints

#### Generate Token
```http
POST /merchant/token
Content-Type: application/json

{
  "email": "merchant@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "code": 200,
  "status": "success",
  "token": "161|i5qMweiGOtssuui3ISNTXqAL7xABDMnijDZdTCJDd6295ef7",
  "expires_at": "2026-01-16T03:14:21+06:00",
  "merchant_id": "test_account",
  "user": {
    "name": "Test Merchant",
    "email": "merchant@example.com",
    "merchant_id": "test_account"
  }
}
```

### Protected Endpoints (Require Authentication)

#### Check Balance
```http
POST /merchant/balance
Authorization: Bearer {token}
Content-Type: application/json

{
  "merchant_id": "test_account"
}
```

#### Create Bank Payouts (Bulk)
```http
POST /merchant/payouts
Authorization: Bearer {token}
Content-Type: application/json

{
  "merchant_id": "test_account",
  "payouts": [
    {
      "reference_key": "TXN001",
      "account_number": "1234567890",
      "account_name": "John Doe",
      "bank_name": "Example Bank",
      "amount": 1000,
      "remarks": "Payment for services"
    }
  ]
}
```

#### Get Batch Status
```http
GET /merchant/payouts/batch/{batch_id}
Authorization: Bearer {token}
```

#### Get Payout by Reference
```http
GET /merchant/payouts/reference/{referenceKey}
Authorization: Bearer {token}
```

#### Create MFS Payouts (Bulk)
```http
POST /merchant/mfs-payouts
Authorization: Bearer {token}
Content-Type: application/json

{
  "merchant_id": "test_account",
  "payouts": [
    {
      "reference_key": "MFS001",
      "mobile_number": "01712345678",
      "account_name": "Jane Doe",
      "mfs_provider": "bKash",
      "amount": 500,
      "remarks": "Mobile payment"
    }
  ]
}
```

#### Get MFS Batch Status
```http
GET /merchant/mfs-payouts/batch/{batch_id}
Authorization: Bearer {token}
```

#### Get MFS Payout by Reference
```http
GET /merchant/mfs-payouts/reference/{referenceKey}
Authorization: Bearer {token}
```

## 🔐 Authentication

### Token-Based Authentication (Laravel Sanctum)

1. **Obtain Token**: Call `/api/merchant/token` with credentials
2. **Store Token**: Save the token and `expires_at` timestamp
3. **Use Token**: Include in `Authorization: Bearer {token}` header
4. **Token Expiration**: Tokens expire after 24 hours
5. **Refresh Token**: Re-authenticate when token expires

### Client Implementation Example

```javascript
// Login and store token
const response = await fetch('/api/merchant/token', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ email, password })
});

const data = await response.json();
localStorage.setItem('token', data.token);
localStorage.setItem('token_expires_at', data.expires_at);

// Check token expiration
function isTokenExpired() {
  const expiresAt = localStorage.getItem('token_expires_at');
  return new Date() > new Date(expiresAt);
}

// Make authenticated requests
async function apiRequest(url, options = {}) {
  if (isTokenExpired()) {
    // Redirect to login or refresh token
    throw new Error('Token expired');
  }
  
  const token = localStorage.getItem('token');
  return fetch(url, {
    ...options,
    headers: {
      ...options.headers,
      'Authorization': `Bearer ${token}`
    }
  });
}
```

## 🔒 Security Features

### Implemented Security Measures

1. **Token Expiration**: All API tokens expire after 24 hours
2. **HTTPS Ready**: Configure SSL/TLS for production
3. **CORS Protection**: Configure allowed origins in `config/cors.php`
4. **SQL Injection Protection**: Eloquent ORM with parameter binding
5. **XSS Protection**: Laravel's built-in escaping
6. **CSRF Protection**: Enabled for web routes
7. **Rate Limiting**: Configure in `app/Http/Kernel.php`
8. **Password Hashing**: Bcrypt with configurable rounds

### Best Practices

- Store tokens securely (httpOnly cookies preferred)
- Use HTTPS in production
- Implement rate limiting on API endpoints
- Regular security audits
- Keep dependencies updated
- Monitor webhook logs for suspicious activity

## 🗄 Database Schema

### Key Tables

#### `users`
- User accounts with role-based access
- Linked to merchants for API access

#### `merchants`
- Merchant accounts
- Webhook URL configuration
- Balance tracking

#### `merchant_balances`
- Real-time balance tracking
- Transaction type (credit/debit)
- Amount and remarks

#### `balance_histories`
- Complete audit trail
- All balance changes logged

#### `payouts`
- Bank transfer records
- Batch tracking
- Approval workflow
- Status tracking

#### `mfs_payouts`
- Mobile financial service records
- Similar structure to payouts
- MFS provider specific fields

#### `personal_access_tokens`
- API tokens with expiration
- Managed by Laravel Sanctum

#### `webhook_logs`
- Webhook delivery tracking
- Request/response logging

## 🧪 Testing

### Run Tests
```bash
php artisan test
```

Or using composer:
```bash
composer test
```

### Manual API Testing

Use the provided test reports in `.agent/` directory:
- `api-test-report.md` - Complete API test results
- `token-expiration-guide.md` - Token system documentation

### Testing with cURL

```bash
# Get token
curl -X POST http://localhost:8000/api/merchant/token \
  -H "Content-Type: application/json" \
  -d '{"email":"merchant@example.com","password":"password"}'

# Check balance
curl -X POST http://localhost:8000/api/merchant/balance \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"merchant_id":"test_account"}'
```

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure production database
- [ ] Set up proper `APP_URL`
- [ ] Configure mail settings
- [ ] Set up queue workers
- [ ] Configure cron for scheduled tasks
- [ ] Enable HTTPS/SSL
- [ ] Set up backup system
- [ ] Configure monitoring
- [ ] Set up log rotation

### Optimization Commands

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### Scheduled Tasks

Add to crontab:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Configure in `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Clean up expired tokens daily
    $schedule->command('sanctum:prune-expired --hours=24')->daily();
}
```

## 📄 License

This project is proprietary software. All rights reserved.

## 📞 Support

For support and inquiries, please contact the development team.

---

**Built with Laravel 12** | **Powered by Laravel Sanctum** | **API Documentation: Swagger/OpenAPI**
