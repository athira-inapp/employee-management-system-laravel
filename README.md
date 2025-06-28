Employee Management System
A comprehensive web-based Employee Management System built with Laravel 11, featuring employee records management, leave request system, and role-based access control.
System Requirements
Minimum Requirements
PHP: 8.1 or higher
Database: PostgreSQL 13+ (or MySQL 8.0+)
Web Server: Apache 2.4+ or Nginx 1.18+
Memory: 512MB RAM minimum
Storage: 1GB available space
Recommended Requirements
PHP: 8.2 or higher
Database: PostgreSQL 15+
Memory: 1GB+ RAM
Storage: 2GB+ available space
Cache: Redis 6.0+
PHP Extensions Required

-   OpenSSL PHP Extension
-   PDO PHP Extension
-   Mbstring PHP Extension
-   Tokenizer PHP Extension
-   XML PHP Extension
-   Ctype PHP Extension
-   JSON PHP Extension
-   BCMath PHP Extension
-   Fileinfo PHP Extension

Installation & Setup

1. Clone the Repository
   git clone https://github.com/your-username/employee-management-system.git
   cd employee-management-system

2. Install Dependencies

# Install PHP dependencies

composer install

# If you have Node.js for frontend assets (optional)

npm install
npm run build

3. Environment Configuration

# Copy environment file

cp .env.example .env

# Generate application key

php artisan key:generate

4. Configure Environment Variables
   Edit .env file with your settings:

# Application Settings

APP_NAME="Employee Management System"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=employee_management
DB_USERNAME=ems_project
DB_PASSWORD=password

# Cache Configuration (Optional)

CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file

# Mail Configuration (for notifications)

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@company.com"
MAIL_FROM_NAME="${APP_NAME}"

Database Setup

1. Create Database
   -- PostgreSQL
   CREATE DATABASE employee_management;
   CREATE USER emp_user WITH PASSWORD 'your_password';
   GRANT ALL PRIVILEGES ON DATABASE employee_management TO emp_user;

2. Run Migrations

# Run database migrations

php artisan migrate

# Install Laravel Passport

php artisan passport:install

3. Seed Sample Data

# Seed basic data (departments, roles)

php artisan db:seed --class=DepartmentSeeder
php artisan db:seed --class=RoleSeeder

# Seed sample employees and users (optional)

php artisan db:seed --class=DummyUsersEmployeesSeeder

# Or seed all at once

php artisan db:seed

Configuration

1. Passport Configuration

# Generate Passport keys (if not done during installation)

php artisan passport:keys

# Create client for password grants

php artisan passport:client --password

2. Storage Configuration

# Create storage symbolic link

php artisan storage:link

# Set proper permissions

chmod -R 775 storage
chmod -R 775 bootstrap/cache

3. Cache Configuration (Production)

# Optimize for production

php artisan config:cache
php artisan route:cache
php artisan view:cache

Usage
Starting the Application

# Development server

php artisan serve

# The application will be available at http://localhost:8000

Default Login Credentials
After running the seeders, you can use these test accounts:
HR Administrator:
Email: hr@company.com
Password: password
Role: hr_admin (Full system access)

Department Manager:
Email: supermanager@company.com
Password: password
Role: Manager (Team management access)

Employee:
Email: diana.prince@company.com
Password: password
Role: Employee (Self-service access)

First Time Setup
Login as HR Admin using the credentials above
Review Departments - Add or modify departments as needed
Configure Roles - Set up job roles with appropriate salaries
Add Real Employees - Replace sample data with actual employee information
Set Manager Relationships - Assign managers to employees
Configure Leave Policies - Set up leave types and policies
API Documentation
Authentication Endpoints
POST /api/login
Content-Type: application/json

{
"email": "user@company.com",
"password": "password"
}

Employee Management Endpoints

# Get all employees

GET /api/employees
Authorization: Bearer {token}

# Create employee

POST /api/employees
Authorization: Bearer {token}
Content-Type: application/json

# Get specific employee

GET /api/employees/{id}
Authorization: Bearer {token}

# Update employee

PUT /api/employees/{id}
Authorization: Bearer {token}

# Delete employee

DELETE /api/employees/{id}
Authorization: Bearer {token}

Leave Management Endpoints

# Get leave requests

GET /api/leave-requests
Authorization: Bearer {token}

# Create leave request

POST /api/leave-requests
Authorization: Bearer {token}

# Update leave request status

PATCH /api/leave-requests/{id}/status
Authorization: Bearer {token}

Employee Management System v1.0 - Making HR management simple and efficient
