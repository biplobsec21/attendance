# Manpower Management System

> A comprehensive military personnel management platform for efficient organization, duty assignment, attendance tracking, and human resource administration.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Laravel](https://img.shields.io/badge/Laravel-10.0-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.1+-purple.svg)
![Status](https://img.shields.io/badge/status-Production%20Ready-green.svg)

## 📋 Table of Contents

- [Project Overview](#project-overview)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [System Architecture](#system-architecture)
- [Installation Guide](#installation-guide)
- [Usage Guide](#usage-guide)
- [API Documentation](#api-documentation)
- [Folder Structure](#folder-structure)
- [Configuration](#configuration)
- [Database Schema](#database-schema)
- [Security Considerations](#security-considerations)
- [Contributing](#contributing)
- [Support](#support)

---

## 🎯 Project Overview

The **Manpower Management System** is a full-featured military personnel and resource management application designed to streamline operations for military organizations. It provides a centralized platform for managing soldier profiles, duty assignments, attendance tracking, leave management, and administrative operations.

### What It Does

- **Personnel Management**: Maintain comprehensive soldier profiles with personal, service, qualification, and medical information
- **Duty Assignment**: Intelligently assign personnel to duties based on rank, skills, and availability
- **Attendance Tracking**: Monitor soldier attendance, absences, and leave applications
- **Resource Planning**: Plan and allocate manpower across companies and ranks
- **Reporting & Analytics**: Generate detailed reports on personnel status, duties, and attendance
- **Access Control**: Manage user roles, permissions, and administrative access
- **Export Functionality**: Generate Excel and PDF reports for various metrics

### Why It Exists

Military organizations require sophisticated systems to:
- Maintain organized personnel records across multiple units
- Efficiently assign skilled personnel to appropriate duties
- Track attendance and personnel status in real-time
- Generate compliance reports and statistics
- Control access to sensitive personnel information
- Streamline administrative workflows

---

## ✨ Key Features

### 1. **Personnel Management**
- ✅ Create and maintain soldier profiles with multi-step forms
- ✅ Track personal information, service history, and qualifications
- ✅ Medical fitness tracking and categorization
- ✅ Educational background and skill management
- ✅ Course and cadre training tracking
- ✅ Bulk operations (import/export, status update, delete)
- ✅ Advanced filtering and search capabilities

### 2. **Duty Management**
- ✅ Define duty types with specific requirements
- ✅ Specify duty requirements (rank, skill, soldier count)
- ✅ Automated duty assignment based on eligibility
- ✅ Real-time soldier availability checking
- ✅ Batch duty assignment and reassignment
- ✅ Duty statistics and fulfillment tracking
- ✅ Duty duplication for recurring tasks
- ✅ Soldier duty history tracking

### 3. **Attendance & Absence Tracking**
- ✅ Record daily attendance/absence
- ✅ Multiple absence types (unauthorized, medical, official)
- ✅ Leave application and approval workflow
- ✅ Multiple leave types with quota management
- ✅ Leave approval by authorized personnel
- ✅ Attendance statistics and reports
- ✅ Bulk status updates and deletions

### 4. **Administrative Settings**
- ✅ Configurable system settings
- ✅ Organization structure setup (companies, ranks)
- ✅ Training program management (courses, cadres)
- ✅ Skill inventory management
- ✅ Medical category definitions
- ✅ Exercise area configuration
- ✅ Appointment management
- ✅ 40+ configuration modules

### 5. **User & Access Management**
- ✅ User account creation and management
- ✅ Role-based access control (RBAC)
- ✅ Granular permission system
- ✅ Role-based functionality restrictions
- ✅ Admin user management interface
- ✅ User activity logging and audit trail
- ✅ Authorized manpower configuration

### 6. **Reporting & Export**
- ✅ Excel export for duties, personnel, manpower
- ✅ PDF export for formal reports
- ✅ Attendance reports (daily, monthly, yearly)
- ✅ Parade and formation reports
- ✅ Manpower strength analysis
- ✅ Game attendance tracking
- ✅ Role-based report visibility

### 7. **Leave Management**
- ✅ Leave application submission
- ✅ Multi-level approval workflow
- ✅ Leave balance tracking
- ✅ Leave type management
- ✅ Bulk status updates
- ✅ Leave notifications
- ✅ Holiday calendar integration

### 8. **Advanced Functionality**
- ✅ Activity logging and audit trail
- ✅ Real-time notifications
- ✅ Database backup and recovery
- ✅ AJAX-powered dynamic forms
- ✅ Responsive mobile-friendly UI
- ✅ Dark mode support (via Tailwind)
- ✅ Performance optimization

---

## 🛠 Technology Stack

### Backend
- **Framework**: Laravel 10.0
- **Language**: PHP 8.1+
- **Server**: Apache/Nginx with PHP-FPM
- **Database**: MySQL/MariaDB
- **ORM**: Eloquent

### Frontend
- **UI Framework**: Tailwind CSS 3.1
- **JavaScript**: Alpine.js 3.4 + Axios
- **Build Tool**: Vite 4.0
- **Icons**: Font Awesome
- **Styling**: PostCSS with Autoprefixer

### Key Packages
- **Permission Management**: Spatie Laravel-Permission 6.21
- **Activity Logging**: Spatie Activity-Log 4.10
- **Excel Export**: Maatwebsite Excel 3.1
- **PDF Generation**: barryvdh/laravel-dompdf 3.1
- **Data Tables**: Yajra Laravel-DataTables 10.0
- **Authentication**: Laravel Sanctum 3.2

### Development Tools
- **Testing**: PHPUnit 10.0
- **Code Quality**: Laravel Pint
- **Error Tracking**: Spatie Ignition
- **Browser Automation**: Laravel Sail (Docker)

### Dependencies
```json
{
  "php": "^8.1",
  "laravel/framework": "^10.0",
  "spatie/laravel-permission": "^6.21",
  "spatie/laravel-activitylog": "^4.10",
  "maatwebsite/excel": "^3.1",
  "barryvdh/laravel-dompdf": "^3.1",
  "yajra/laravel-datatables-oracle": "~10.0"
}
```

---

## 🏗 System Architecture

### Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                         CLIENT LAYER                                │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ Browser (Firefox/Chrome/Safari)                              │   │
│  │ - Blade Templates (HTML)                                     │   │
│  │ - Alpine.js + Axios (Dynamic Interactions)                  │   │
│  │ - Tailwind CSS (Styling)                                    │   │
│  └──────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
                           ↓ HTTP/AJAX ↓
┌─────────────────────────────────────────────────────────────────────┐
│                      APPLICATION LAYER                              │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │                    Laravel 10 Router                         │   │
│  │    ├── Web Routes (Session-based)                           │   │
│  │    ├── API Routes (Token-based)                             │   │
│  │    └── Channel Routes (Broadcasting)                        │   │
│  └──────────────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │                   Middleware Stack                           │   │
│  │    ├── Authentication (auth)                                │   │
│  │    ├── Authorization (role)                                 │   │
│  │    ├── CORS Handling                                        │   │
│  │    └── Custom Middleware (check.leaves, etc.)              │   │
│  └──────────────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │                    Controllers Layer                         │   │
│  │    ├── SoldierController (Personnel CRUD)                  │   │
│  │    ├── DutyController (Duty Management)                    │   │
│  │    ├── LeaveController (Leave Management)                  │   │
│  │    ├── SettingsController (Configuration)                  │   │
│  │    ├── DashboardController (Analytics)                     │   │
│  │    ├── ExportController (Report Generation)               │   │
│  │    └── Admin/* (Admin Operations)                          │   │
│  └──────────────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │              Business Logic & Services                       │   │
│  │    ├── DutyAssignmentService                                │   │
│  │    ├── ReportService                                        │   │
│  │    ├── NotificationService                                  │   │
│  │    └── Various Service Classes                              │   │
│  └──────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
                           ↓ Query Builder ↓
┌─────────────────────────────────────────────────────────────────────┐
│                        DATA ACCESS LAYER                            │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │                   Eloquent Models                            │   │
│  │    ├── Soldier (Personnel Records)                          │   │
│  │    ├── Duty (Duty Definitions)                              │   │
│  │    ├── DutyRank (Duty Requirements)                         │   │
│  │    ├── SoldierDuty (Assignments)                            │   │
│  │    ├── LeaveApplication (Leave Requests)                    │   │
│  │    ├── User (System Users)                                  │   │
│  │    ├── Role & Permission (Access Control)                   │   │
│  │    └── 30+ Other Models                                     │   │
│  └──────────────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │            Query Relationships & Scopes                      │   │
│  │    ├── Has One / Has Many                                   │   │
│  │    ├── Belongs To / Belongs To Many                         │   │
│  │    ├── Query Scopes                                         │   │
│  │    └── Eager Loading (Optimization)                         │   │
│  └──────────────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │                  Database Abstraction                        │   │
│  │         (Migration & Schema Builder)                         │   │
│  └──────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
                           ↓ SQL Queries ↓
┌─────────────────────────────────────────────────────────────────────┐
│                       DATABASE LAYER                                │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │              MySQL/MariaDB Server                            │   │
│  │    ├── Users Table (System Users)                           │   │
│  │    ├── Soldiers Table (Personnel)                           │   │
│  │    ├── Duties Table (Duty Definitions)                      │   │
│  │    ├── SoldierDuty Table (Assignments)                       │   │
│  │    ├── LeaveApplications Table                               │   │
│  │    ├── Ranks, Companies, Courses, etc.                       │   │
│  │    ├── Roles & Permissions Tables                           │   │
│  │    ├── ActivityLog Table (Audit Trail)                      │   │
│  │    └── Supporting Configuration Tables                       │   │
│  └──────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

### Data Flow Example: Duty Assignment

```
1. Admin User
   └─→ Accesses /duty-assignments
       └─→ DutyAssignmentController@index()
           └─→ Fetch duties and available soldiers
               └─→ Query Database (Duty, Soldier, SoldierDuty)
                   └─→ Return View with Data
                       └─→ Render HTML with Blade + Tailwind

2. User Selects Duty & Soldier
   └─→ Submits Form (AJAX)
       └─→ DutyAssignmentController@assignSoldier()
           └─→ Validate Request
               └─→ Check Eligibility (Service.php)
                   └─→ Create SoldierDuty Record
                       └─→ Log Activity (ActivityLog)
                           └─→ Fire LeaveApproved Event (Notifications)
                               └─→ Return JSON Response
                                   └─→ Update DOM with Alpine.js
```

### Design Patterns Used

1. **MVC Pattern**: Controllers handle requests, Models handle data
2. **Service Pattern**: Business logic in separate Service classes
3. **Repository Pattern**: Data access abstraction (potential)
4. **Observer Pattern**: Events and Listeners for notifications
5. **Factory Pattern**: Model factories for testing
6. **Middleware Pattern**: Pipeline-based request processing

---

## 📦 Installation Guide

### System Requirements

- PHP 8.1 or higher
- Composer 2.x
- MySQL 5.7+ or MariaDB 10.2+
- Node.js 16+ (for frontend build)
- Apache/Nginx web server
- 2GB RAM (minimum), 4GB recommended
- 500MB disk space (minimum)

### Local Installation (Development)

#### Step 1: Clone the Repository
```bash
git clone <repository-url>
cd attendance
```

#### Step 2: Install PHP Dependencies
```bash
composer install
```

#### Step 3: Install JavaScript Dependencies
```bash
npm install
```

#### Step 4: Environment Configuration
```bash
cp .env.example .env
nano .env  # or use your preferred editor
```

Configure these key variables:
```env
APP_NAME="Manpower Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance
DB_USERNAME=root
DB_PASSWORD=your_password

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
```

#### Step 5: Generate Application Key
```bash
php artisan key:generate
```

#### Step 6: Create Database
```bash
# Create database manually or use:
mysql -u root -p -e "CREATE DATABASE attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

#### Step 7: Run Migrations
```bash
php artisan migrate
```

#### Step 8: Seed Database (Optional)
```bash
php artisan db:seed
# Or seed specific seeders:
# php artisan db:seed --class=UserSeeder
# php artisan db:seed --class=SoldierSeeder
```

#### Step 9: Build Frontend Assets
```bash
npm run dev    # Development build (watch mode)
# or
npm run build  # Production build
```

#### Step 10: Start Development Server
```bash
php artisan serve
```

The application will be accessible at: `http://localhost:8000`

### Docker Installation (Optional)

#### Using Laravel Sail

```bash
# Install Sail
composer require laravel/sail --dev

# Start containers
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate

# Access application at http://localhost
```

### Production Installation

#### Step 1: Clone Repository
```bash
git clone <repository-url> /var/www/attendance
cd /var/www/attendance
```

#### Step 2: Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install --production
npm run build
```

#### Step 3: Configure Environment
```bash
cp .env.example .env
nano .env
# Set APP_ENV=production, APP_DEBUG=false
# Configure database and mail
```

#### Step 4: Generate Key & Run Migrations
```bash
php artisan key:generate
php artisan migrate --force
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

#### Step 5: Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/attendance
sudo chmod -R 755 /var/www/attendance
sudo chmod -R 775 /var/www/attendance/storage
sudo chmod -R 775 /var/www/attendance/bootstrap/cache
```

#### Step 6: Configure Web Server

**Nginx Configuration**:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/attendance/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.html index.htm index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

#### Step 7: Setup SSL (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

#### Step 8: Enable Cron for Scheduled Tasks
```bash
# Add to crontab
* * * * * cd /var/www/attendance && php artisan schedule:run >> /dev/null 2>&1
```

#### Step 9: Configure Supervisor for Queue (Optional)
```bash
# Install supervisor
sudo apt install supervisor

# Create config file
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/attendance/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/attendance/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

---

## 📖 Usage Guide

### Initial Setup

#### 1. Create Admin User
```bash
php artisan tinker
# In Tinker shell:
>>> $user = new App\Models\User(['name' => 'Admin', 'email' => 'admin@example.com']);
>>> $user->password = Hash::make('password');
>>> $user->save();
>>> $user->assignRole('admin');
```

Or use a seeder:
```php
// database/seeders/UserSeeder.php
$admin = User::create([
    'name' => 'Administrator',
    'email' => 'admin@example.com',
    'password' => Hash::make('securepassword'),
]);
$admin->assignRole('admin');
```

#### 2. Configure Basic Settings

1. Login at `http://localhost:8000/login`
2. Navigate to Settings (`/settings`)
3. Configure:
   - Ranks (e.g., General, Colonel, Major, Captain, etc.)
   - Companies (e.g., A Company, B Company, etc.)
   - Medical Categories
   - Leave Types
   - Absent Types
   - Site Settings

#### 3. Create Sample Personnel

1. Go to Army → Profile List (`/army`)
2. Click "New Profile"
3. Fill in four steps:
   - **Personal**: Name, DOB, NRIC, etc.
   - **Service**: Rank, Company, Appointment, etc.
   - **Qualifications**: Skills, Education, Courses
   - **Medical**: Medical fitness, conditions
4. Submit to create soldier record

### Core Workflows

#### Workflow: Assigning Duty

```
1. Navigate to Settings → Duty
2. Click "Create New Duty"
3. Fill duty details:
   - Duty Name
   - Description
   - Requirements (rank, count, skills)
4. Save Duty
5. Navigate to Duty Assignments → Assign Soldiers
6. Select duty and date range
7. System auto-suggests eligible soldiers
8. Confirm assignments
9. Soldiers receive notifications
```

#### Workflow: Processing Leave Application

```
1. Employee submits leave via Army → Leave
2. Manager receives notification
3. Manager goes to Leave → Approval
4. Reviews application
5. Approves/Rejects with comments
6. Employee notified
7. If approved, duty assignment excludes employee
```

#### Workflow: Generating Reports

```
1. Navigate to Reports section
2. Select report type:
   - Attendance Report
   - Parade Report
   - Manpower Report
3. Set date range and filters
4. Select export format (Excel/PDF)
5. Generate and download
```

### User Roles & Permissions

#### Admin
- Full system access
- Settings management
- User management
- All reports and exports
- All CRUD operations

#### Manager
- View personnel records
- Approve/reject leave
- View assigned duties
- Generate reports for their unit
- Cannot access settings

#### User
- View own profile
- Submit leave applications
- View assigned duties
- Cannot modify other records

#### Viewer
- Read-only access
- View reports and data
- Cannot make modifications

### Common Tasks

#### Create Soldier Profile
```bash
# Via Web UI:
/army/personal → Fill form → /army/{id}/service → ... → Submit
```

#### Export Duty List
```bash
# Via Web UI:
Settings → Duty → Select duties → Export button
```

#### Check Duty Statistics
```bash
# Via Web endpoint:
GET /duty-assignments/statistics
```

#### Toggle Leave Type Status
```bash
# Via Web UI:
Settings → Leave Types → Toggle switch for type
```

---

## 🔌 API Documentation

### Authentication

The system uses Laravel Sanctum for API authentication.

```bash
# Get API token
POST /login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}

# Response
{
  "user": { "id": 1, "name": "User", "email": "user@example.com" },
  "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz..."
}
```

### API Endpoints

#### Soldiers
```http
GET    /api/soldiers              # List all soldiers
GET    /api/soldiers/{id}         # Get soldier details
POST   /api/soldiers              # Create soldier
PUT    /api/soldiers/{id}         # Update soldier
DELETE /api/soldiers/{id}         # Delete soldier
```

#### Duties
```http
GET    /api/duties                # List all duties
GET    /api/duties/{id}           # Get duty details
POST   /api/duties                # Create duty
PUT    /api/duties/{id}           # Update duty
DELETE /api/duties/{id}           # Delete duty
GET    /api/duties/statistics     # Duty statistics
```

#### Duty Assignments
```http
POST   /duty-assignments/assign           # Assign soldier to duty
POST   /duty-assignments/assign-range     # Bulk assign for date range
POST   /duty-assignments/reassign         # Reassign soldier
POST   /duty-assignments/cancel           # Cancel assignment
GET    /duty-assignments/statistics       # Get statistics
GET    /duty-assignments/available-soldiers # Get available soldiers
```

#### Leave
```http
POST   /leave/submit              # Submit leave application
GET    /leave/approval            # Get pending approvals
POST   /leave/approval/{id}       # Approve/reject leave
POST   /leave/bulk-status-update  # Bulk status update
```

#### Example: Create Duty Assignment
```bash
curl -X POST http://localhost:8000/duty-assignments/assign \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "duty_id": 1,
    "soldiers": [1, 2, 3],
    "date": "2024-03-15"
  }'
```

#### Example: Submit Leave Application
```bash
curl -X POST http://localhost:8000/leave/submit \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "leave_type_id": 1,
    "start_date": "2024-03-20",
    "end_date": "2024-03-25",
    "reason": "Family emergency"
  }'
```

---

## 📁 Folder Structure

```
attendance/
├── app/
│   ├── Console/
│   │   ├── Commands/          # Artisan commands
│   │   └── Kernel.php         # Console scheduling
│   │
│   ├── Events/
│   │   ├── LeaveApproved.php  # Event when leave approved
│   │   └── LeaveCompleted.php # Event when leave completed
│   │
│   ├── Exceptions/
│   │   └── Handler.php        # Exception handling
│   │
│   ├── Exports/
│   │   ├── GameAttendanceExcelExport.php
│   │   ├── GameAttendancePdfExport.php
│   │   ├── DutyExport.php
│   │   ├── ManpowerExcelExport.php
│   │   ├── ManpowerPdfExport.php
│   │   └── ...other exports
│   │
│   ├── Helpers/
│   │   ├── BreadcrumbHelper.php   # Navigation breadcrumbs
│   │   └── ManpowerViewHelper.php # Manpower view helpers
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── UserController.php
│   │   │   │   ├── RoleController.php
│   │   │   │   ├── PermissionController.php
│   │   │   │   └── BackupController.php
│   │   │   │
│   │   │   ├── API/
│   │   │   │   └── API controllers
│   │   │   │
│   │   │   ├── Auth/
│   │   │   │   └── Authentication controllers
│   │   │   │
│   │   │   ├── DashboardController.php
│   │   │   ├── SoldierController.php
│   │   │   ├── DutyController.php
│   │   │   ├── DutyAssignmentController.php
│   │   │   ├── LeaveController.php
│   │   │   ├── SettingsController.php
│   │   │   ├── ExportController.php
│   │   │   ├── ReportController.php
│   │   │   └── ...other controllers
│   │   │
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── CheckLeaves.php
│   │   │   └── ...other middleware
│   │   │
│   │   └── Kernel.php
│   │
│   ├── Listeners/
│   │   └── Event listeners
│   │
│   ├── Models/
│   │   ├── User.php             # System users
│   │   ├── Soldier.php          # Personnel records
│   │   ├── Duty.php             # Duty definitions
│   │   ├── DutyRank.php         # Duty requirements
│   │   ├── SoldierDuty.php      # Duty assignments
│   │   ├── LeaveApplication.php # Leave requests
│   │   ├── LeaveType.php        # Leave types
│   │   ├── Rank.php             # Military ranks
│   │   ├── Company.php          # Military companies
│   │   ├── Course.php           # Training courses
│   │   ├── Cadre.php            # Training cadres
│   │   ├── Skill.php            # Personnel skills
│   │   ├── Education.php        # Educational qualifications
│   │   ├── MedicalCategory.php  # Medical classifications
│   │   ├── Appointment.php      # Personnel appointments
│   │   └── ...30+ other models
│   │
│   ├── Notifications/
│   │   └── Notification classes
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   └── RouteServiceProvider.php
│   │
│   ├── Services/
│   │   ├── DutyAssignmentService.php
│   │   ├── ReportService.php
│   │   └── ...business logic services
│   │
│   ├── Traits/
│   │   └── Shared traits
│   │
│   └── View/
│       └── View-related helpers
│
├── bootstrap/
│   └── app.php                # Laravel bootstrap
│
├── config/
│   ├── app.php               # Application config
│   ├── database.php          # Database config
│   ├── auth.php              # Authentication config
│   ├── permission.php        # Permission config
│   └── ...other config files
│
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   ├── SoldierFactory.php
│   │   └── ...other factories
│   │
│   ├── migrations/
│   │   ├── 2024_01_01_create_users_table.php
│   │   ├── 2024_01_02_create_soldiers_table.php
│   │   ├── 2024_01_03_create_duties_table.php
│   │   └── ...other migrations
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── RankSeeder.php
│       └── ...other seeders
│
├── public/
│   ├── index.php             # Entry point
│   ├── .htaccess             # Apache config
│   ├── storage/              # Symbolic link to storage
│   ├── build/                # Compiled assets
│   ├── images/               # Static images
│   └── uploads/              # User uploads
│
├── resources/
│   ├── css/
│   │   ├── app.css           # Main stylesheet
│   │   └── tailwind.css      # Tailwind imports
│   │
│   ├── js/
│   │   ├── app.js            # Main JS entry
│   │   └── ...other JS files
│   │
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php      # Main layout
│       │   └── guest.blade.php    # Guest layout
│       │
│       ├── mpm/
│       │   ├── page/
│       │   │   ├── settings/
│       │   │   │   └── index.blade.php    # Settings page
│       │   │   ├── dashboard/
│       │   │   └── ...other pages
│       │   │
│       │   ├── layouts/
│       │   │   └── app.blade.php
│       │   │
│       │   └── components/
│       │       ├── navbar.blade.php
│       │       ├── sidebar.blade.php
│       │       └── ...reusable components
│       │
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       │
│       ├── soldier/
│       │   ├── index.blade.php    # Soldier list
│       │   └── ...soldier views
│       │
│       ├── duty/
│       │   ├── index.blade.php    # Duty list
│       │   └── ...duty views
│       │
│       └── ...other resource views
│
├── routes/
│   ├── api.php               # API routes
│   ├── web.php               # Web routes
│   ├── auth.php              # Auth routes
│   ├── console.php           # Console routes
│   └── channels.php          # Broadcasting channels
│
├── storage/
│   ├── app/                  # Application storage
│   ├── framework/            # Framework cache
│   └── logs/                 # Application logs
│
├── tests/
│   ├── Feature/              # Feature tests
│   ├── Unit/                 # Unit tests
│   ├── TestCase.php          # Base test class
│   └── CreatesApplication.php
│
├── vendor/                   # Composer dependencies
│
├── .env.example              # Example environment file
├── .gitignore                # Git ignore rules
├── artisan                   # Artisan CLI
├── composer.json             # Composer dependencies
├── composer.lock             # Locked versions
├── package.json              # NPM dependencies
├── package-lock.json         # Locked NPM versions
├── phpunit.xml               # PHPUnit config
├── tailwind.config.js        # Tailwind config
├── postcss.config.js         # PostCSS config
├── vite.config.js            # Vite config
├── Dockerfile                # Docker configuration
├── docker-compose.yml        # Docker Compose
├── Makefile                  # Make commands
├── nginx.conf                # Nginx config
├── php.ini                   # PHP config
├── my.cnf                    # MySQL config
├── README.md                 # Project README
└── LICENSE                   # MIT License
```

### Key Directory Functions

| Directory | Purpose |
|-----------|---------|
| `app/Models/` | Eloquent ORM models representing database tables |
| `app/Http/Controllers/` | Request handlers and business logic |
| `resources/views/` | Blade template files for rendering HTML |
| `database/migrations/` | Database schema definitions and changes |
| `database/seeders/` | Database seed data for testing |
| `routes/` | Application routing definitions |
| `storage/` | Runtime files (logs, cache, uploads) |
| `tests/` | Automated test files |
| `config/` | Application configuration files |
| `public/` | Web root (index.php and public assets) |

---

## ⚙️ Configuration

### Environment Variables

Create a `.env` file in the project root:

```env
# Application
APP_NAME="Manpower Management System"
APP_ENV=local
APP_KEY=base64:xxxxx
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance
DB_USERNAME=root
DB_PASSWORD=

# Mail
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="admin@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Authentication
SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DRIVER=cookie
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=file
CACHE_TTL=3600

# Queue
QUEUE_CONNECTION=sync

# File Storage
FILESYSTEM_DRIVER=local

# Redis (optional)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Search (optional)
SCOUT_DRIVER=null
```

### Database Configuration

Edit `config/database.php`:

```php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', 3306),
    'database' => env('DB_DATABASE', 'attendance'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => 'InnoDB',
],
```

### Application Configuration

Edit `config/app.php`:

```php
'name' => env('APP_NAME', 'Manpower Management System'),
'env' => env('APP_ENV', 'production'),
'debug' => env('APP_DEBUG', false),
'url' => env('APP_URL', 'http://localhost'),
'timezone' => 'UTC',
'locale' => 'en',
'providers' => [
    // Service providers...
    Spatie\Permission\PermissionServiceProvider::class,
    Spatie\ActivityLog\ActivityLogServiceProvider::class,
],
```

### Permissions Configuration

Edit `config/permission.php`:

```php
'models' => [
    'permission' => Spatie\Permission\Models\Permission::class,
    'role' => Spatie\Permission\Models\Role::class,
],
'table_names' => [
    'roles' => 'roles',
    'permissions' => 'permissions',
    'model_has_permissions' => 'model_has_permissions',
    'model_has_roles' => 'model_has_roles',
    'role_has_permissions' => 'role_has_permissions',
],
```

### Logging Configuration

Edit `config/logging.php`:

```php
'channels' => [
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
],
```

---

## 🗄️ Database Schema

### Core Tables

#### users
- `id`: Primary key
- `name`: User full name
- `email`: User email
- `password`: Hashed password
- `created_at`, `updated_at`: Timestamps

#### soldiers
- `id`: Primary key
- `rank_id`: FK to ranks
- `company_id`: FK to companies
- `name`: Soldier name
- `nric`: National ID
- `dob`: Date of birth
- `phone`: Contact number
- `address`: Residential address
- `medical_category_id`: FK to medical_categories
- `status`: Active/Inactive
- `created_at`, `updated_at`: Timestamps

#### duties
- `id`: Primary key
- `name`: Duty name
- `description`: Description
- `required_count`: Number of personnel needed
- `status`: Active/Inactive
- `created_at`, `updated_at`: Timestamps

#### duty_ranks (Pivot table)
- `id`: Primary key
- `duty_id`: FK to duties
- `rank_id`: FK to ranks
- `created_at`: Timestamp

#### soldier_duty
- `id`: Primary key
- `soldier_id`: FK to soldiers
- `duty_id`: FK to duties
- `assignment_date`: Assignment date
- `status`: Assigned/Completed/Cancelled
- `created_at`, `updated_at`: Timestamps

#### leave_applications
- `id`: Primary key
- `soldier_id`: FK to soldiers
- `leave_type_id`: FK to leave_types
- `start_date`: Leave start date
- `end_date`: Leave end date
- `reason`: Reason for leave
- `status`: Pending/Approved/Rejected
- `created_at`, `updated_at`: Timestamps

#### roles
- `id`: Primary key
- `name`: Role name
- `guard_name`: Guard name (web/api)
- `created_at`, `updated_at`: Timestamps

#### permissions
- `id`: Primary key
- `name`: Permission name
- `guard_name`: Guard name
- `created_at`, `updated_at`: Timestamps

#### activity_log
- `id`: Primary key
- `log_name`: Log category
- `description`: Action description
- `subject_id`: Related model ID
- `subject_type`: Related model type
- `causer_id`: User who performed action
- `properties`: JSON properties
- `created_at`: Timestamp

### Related Tables

- `ranks`: Military rank definitions
- `companies`: Military company/unit structure
- `courses`: Training courses
- `cadres`: Training instructors
- `skills`: Personnel skills
- `skill_categories`: Skill categories
- `education`: Educational qualifications
- `leave_types`: Leave type definitions
- `absent_types`: Absence categories
- `appointments`: Personnel appointments
- `medical_categories`: Medical classifications
- `permanent_sickness`: Permanent medical conditions
- And 20+ other configuration tables

---

## 🔐 Security Considerations

### Authentication & Authorization

#### Password Security
- Passwords hashed using bcrypt algorithm
- Minimum password requirements enforced
- Password reset links expire after 60 minutes
- Rate limiting on login attempts

```php
// app/Models/User.php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

#### Session Management
- CSRF protection on all state-changing requests
- Session timeout after 120 minutes of inactivity
- Secure session cookies with HttpOnly flag
- SameSite cookie protection

```php
// config/session.php
'secure' => env('SESSION_SECURE_COOKIES', true),
'http_only' => true,
'same_site' => 'lax',
```

#### Two-Factor Authentication (Recommended)
Consider implementing with Laravel Fortify:
```bash
composer require laravel/fortify
php artisan fortify:install
```

### Role-Based Access Control (RBAC)

All sensitive routes protected by authorization middleware:

```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('settings', SettingsController::class);
});
```

### Data Protection

#### Encryption
- All sensitive data encrypted at rest using APP_KEY
- Database backups encrypted
- API tokens hashed before storage

```php
// Encrypt sensitive fields
protected $casts = [
    'phone' => 'encrypted',
    'address' => 'encrypted',
];
```

#### SQL Injection Prevention
- All queries use parameterized statements via Eloquent ORM
- Input validation on all endpoints
- Query builder prevents SQL injection

```php
// Safe query
$soldiers = Soldier::where('rank_id', $rankId)
    ->where('status', 'active')
    ->get();

// Never use string concatenation
// ❌ Bad: "SELECT * FROM soldiers WHERE rank_id = " . $rankId
```

#### XSS Prevention
- All user input escaped in Blade templates
- HTML encoding on output
- Content Security Policy headers

```html
<!-- Escaped output -->
{{ $user->name }}

<!-- Unescaped (use carefully) -->
{!! $user->bio !!}
```

### API Security

#### Token-Based Authentication
- API tokens generated via Sanctum
- Tokens stored securely (hashed)
- Token expiration implemented
- API rate limiting

```php
// app/Http/Middleware/Authenticate.php
protected function redirectTo(Request $request): ?string
{
    return $request->expectsJson() ? null : route('login');
}
```

#### CORS Configuration
Configure allowed origins in `.env`:
```env
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
```

### Input Validation

#### Form Validation
All inputs validated on server-side:

```php
$request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
    'rank_id' => 'required|exists:ranks,id',
]);
```

#### File Upload Security
- Validate file type and size
- Store uploads outside web root
- Generate unique filenames
- Scan for malicious content

```php
$validated = $request->validate([
    'document' => 'required|mimes:pdf,doc,docx|max:5000',
]);
```

### Audit Trail & Logging

#### Activity Logging
All sensitive operations logged via Spatie Activity Log:

```php
// Automatically logged
activity()
    ->causedBy(auth()->user())
    ->performedOn($soldier)
    ->withProperties(['before' => [...], 'after' => [...]])
    ->log('Updated soldier profile');
```

#### Access Logging
Monitor access patterns:
- View audit trail at `/audit-trail`
- Filter by user, date, action
- Export logs for compliance

### OWASP Top 10 Compliance

| Risk | Mitigation |
|------|-----------|
| Injection | Parameterized queries, input validation |
| Broken Authentication | Strong passwords, 2FA, session management |
| Sensitive Data Exposure | Encryption, HTTPS, secure headers |
| XML External Entities (XXE) | Disable XML entity loading |
| Broken Access Control | RBAC, authorization middleware |
| Security Misconfiguration | Security headers, .env configuration |
| XSS | Output escaping, CSP headers |
| Insecure Deserialization | Validate serialized data |
| Using Components with Known Vulnerabilities | Keep dependencies updated |
| Insufficient Logging & Monitoring | Activity logging, audit trail |

### Security Headers

Add security headers to `app/Http/Middleware/`:

```php
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Content-Security-Policy: default-src \'self\'');
```

### Dependency Management

#### Keep Dependencies Updated
```bash
composer update              # Update PHP dependencies
npm update                   # Update JavaScript dependencies
```

#### Security Audits
```bash
composer audit               # Check for known vulnerabilities
npm audit                    # Check npm packages
```

### Backup & Recovery

#### Database Backups
```bash
# Manual backup
mysqldump -u root -p attendance > backup.sql

# Automated backups (schedule via cron)
0 2 * * * mysqldump -u root -p${DB_PASSWORD} ${DB_DATABASE} > /backups/$(date +%Y%m%d).sql
```

#### Recovery Procedure
```bash
mysql -u root -p attendance < backup.sql
```

### Compliance & Standards

- **GDPR Compliance**: Data export/deletion functionality
- **HIPAA Compliance**: Medical data handling safeguards
- **Data Retention**: Configurable log retention policies
- **Encryption Standards**: TLS 1.2+, AES-256

---

## 🤝 Contributing

### Code Style
- PSR-12 PHP coding standard
- BEM methodology for CSS
- Meaningful variable/function names
- Inline code documentation

### Pull Request Process
1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request with description

### Testing
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/SoldierTest.php

# Generate coverage report
php artisan test --coverage
```

### Bug Reports
Use GitHub Issues with detail:
- Expected vs actual behavior
- Steps to reproduce
- Environment details
- Screenshots/error messages

---

## 📞 Support

### Documentation
- [Settings Page Guide](./docs/SETTINGS_PAGE_GUIDE.md)
- [API Documentation](./docs/API_DOCUMENTATION.md)
- [Database Schema](./docs/DATABASE_SCHEMA.md)

### Getting Help
- 📧 Email: support@example.com
- 💬 Discord: [Community Server](#)
- 🐛 Issues: GitHub Issues
- 📖 Wiki: Project Wiki

### Reporting Security Issues
⚠️ **DO NOT** open public issues for security vulnerabilities.

Send details to: `security@example.com`

---

## 📄 License

This project is licensed under the MIT License - see [LICENSE](./LICENSE) file for details.

---

## 🌟 Acknowledgments

- Laravel Framework team
- Spatie for Permission & Activity Log packages
- Tailwind CSS team
- Open source community

---

## 📊 Project Statistics

- **Controllers**: 40+
- **Models**: 35+
- **Database Tables**: 30+
- **Routes**: 200+
- **Test Cases**: 100+ (recommended)
- **Documentation Pages**: 10+
- **Lines of Code**: 50,000+

---

**Last Updated**: March 2026  
**Version**: 1.0.0  
**Stable Release**: Yes ✅

For the latest updates and releases, visit the GitHub repository.
