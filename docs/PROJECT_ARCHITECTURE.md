# Project Architecture Guide

## System Overview

The Manpower Management System is built using a modern **MVC (Model-View-Controller)** architecture with Laravel 10, following clean code principles and SOLID design patterns.

---

## 🏗️ Architectural Layers

### 1. **Presentation Layer (Views)**
- Location: `resources/views/`
- Technology: Blade templates + Tailwind CSS
- Responsibility: Render HTML for user interaction
- Status: Stateless, receives data from controllers

**Key Components**:
- Layout templates (`mpm/layouts/app.blade.php`)
- Page components (resource views)
- Reusable Blade components
- Form partials and includes

### 2. **Application Layer (Controllers)**
- Location: `app/Http/Controllers/`
- Technology: Laravel Controllers
- Responsibility: Handle HTTP requests, orchestrate business logic

**Types of Controllers**:
```
Controllers/
├── Admin/                    # Admin-specific operations
│   ├── UserController
│   ├── RoleController
│   ├── PermissionController
│   └── BackupController
├── API/                     # REST API endpoints
├── Auth/                    # Authentication
├── Core Controllers
│   ├── SoldierController    # Personnel CRUD
│   ├── DutyController       # Duty management
│   ├── LeaveController      # Leave management
│   ├── SettingsController   # Configuration
│   └── ...others
└── Specialized Controllers
    ├── ExportController     # Report generation
    ├── ReportController     # Analytics
    ├── DashboardController  # Dashboard
    └── ...
```

### 3. **Business Logic Layer (Services)**
- Location: `app/Services/`
- Responsibility: Complex business operations
- Principle: Controllers delegate to services

**Service Examples**:
```php
// app/Services/DutyAssignmentService.php
class DutyAssignmentService
{
    public function assignSoldier(Duty $duty, Soldier $soldier, Carbon $date)
    {
        // Complex validation
        // Check eligibility
        // Create assignment
        // Trigger notifications
        // Log activity
    }
}
```

### 4. **Data Access Layer (Models)**
- Location: `app/Models/`
- Technology: Eloquent ORM
- Responsibility: Database interaction and relationships

**Model Categories**:
```
Personnel Models:
├── Soldier          # Main personnel model
├── SoldierServices
├── SoldierDuty
├── SoldierEducation
├── SoldierCourse
├── SoldierCadre
└── SoldierExArea

Organizational Models:
├── Rank
├── Company
├── CompanyRankManpower
├── Appointment
├── Cadre

Configuration Models:
├── Duty
├── DutyRank
├── LeaveType
├── AbsentType
├── Course
├── Skill
├── Education
├── MedicalCategory
├── ...others

System Models:
├── User
├── Role (Spatie)
├── Permission (Spatie)
└── ActivityLog
```

### 5. **Database Layer**
- Location: `database/migrations/` & `database/seeders/`
- Technology: MySQL/MariaDB
- Responsibility: Data persistence

---

## 🔄 Request Flow

### Example: Create Soldier Profile

```
1. USER INITIATES REQUEST
   └─→ GET /army/personal
       └─→ Browser sends HTTP request

2. ROUTING
   └─→ routes/web.php matches route
       └─→ Route::get('army/personal', [SoldierController::class, 'personalForm'])

3. MIDDLEWARE PIPELINE
   └─→ 'auth' middleware
       ├─→ Verify user is authenticated
       └─→ Reject if not logged in
   └─→ Custom middleware (if any)

4. CONTROLLER HANDLING
   └─→ SoldierController@personalForm()
       ├─→ Create new Soldier instance
       ├─→ Call personalForm() method
       └─→ Return view with data

5. MODEL/DATABASE
   └─→ Soldier::class (no database query for new form)
       └─→ Prepare empty model instance

6. VIEW RENDERING
   └─→ resources/views/soldier/personalForm.blade.php
       ├─→ Render Blade template
       ├─→ Apply Tailwind CSS
       ├─→ Include Alpine.js for interactivity
       └─→ Generate HTML

7. RESPONSE
   └─→ Send HTML to browser
       └─→ Browser renders page

8. USER FILLS FORM
   └─→ Submits POST request
       └─→ POST /army/personal

9. REQUEST VALIDATION
   └─→ $request->validate([...])
       ├─→ Check required fields
       ├─→ Validate email format
       └─→ Return 422 if invalid

10. BUSINESS LOGIC
    └─→ SoldierController@savePersonal()
        └─→ $soldier = Soldier::create($validated)
            └─→ INSERT INTO soldiers table

11. RESPONSE
    └─→ Redirect to next step
        └─→ return redirect()->route('soldier.serviceForm', $soldier)
```

### Example: Assign Soldier to Duty (AJAX)

```
1. USER INTERACTION
   └─→ Click "Assign" button
       └─→ Alpine.js intercepts click
           └─→ AJAX POST /duty-assignments/assign-soldier

2. MIDDLEWARE VALIDATION
   └─→ 'auth' middleware - verify logged in
   └─→ 'role:admin' middleware - verify admin role

3. CONTROLLER
   └─→ DutyAssignmentController@assignSoldier()
       ├─→ Validate request data
       ├─→ Call DutyAssignmentService

4. SERVICE LOGIC
   └─→ DutyAssignmentService@assignSoldier()
       ├─→ Check soldier eligibility
       ├─→ Verify availability
       ├─→ Check rank requirements
       ├─→ Create SoldierDuty record
       ├─→ Log activity
       ├─→ Fire LeaveApproved event
       └─→ Return success response

5. EVENT HANDLING
   └─→ LeaveApproved event triggered
       ├─→ Notify management
       ├─→ Update dashboard
       └─→ Send notifications

6. DATABASE CHANGES
   └─→ INSERT INTO soldier_duty
   └─→ INSERT INTO activity_log
   └─→ UPDATE notifications table

7. JSON RESPONSE
   └─→ Send JSON to browser
       └─→ { "success": true, "message": "Assigned successfully" }

8. FRONTEND UPDATE
   └─→ Alpine.js receives response
       ├─→ Update DOM
       ├─→ Show success message
       ├─→ Refresh data table
       └─→ Hide modal
```

---

## 📊 Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     USER INTERFACE                              │
│       (Blade Templates + Alpine.js + Tailwind CSS)             │
└────────────────────────┬────────────────────────────────────────┘
                         │
                    ↓ HTTP Request ↓
                         │
┌─────────────────────────────────────────────────────────────────┐
│                        ROUTING                                  │
│              routes/web.php & routes/api.php                    │
└────────────────────────┬────────────────────────────────────────┘
                         │
                    ↓ Route Matching ↓
                         │
┌─────────────────────────────────────────────────────────────────┐
│                      MIDDLEWARE                                 │
│       (Authentication, Authorization, CORS, etc.)              │
└────────────────────────┬────────────────────────────────────────┘
                         │
                    ↓ Validated ↓
                         │
┌─────────────────────────────────────────────────────────────────┐
│                     CONTROLLER                                  │
│  (Request handling, input validation, response building)        │
└────────────────────────┬────────────────────────────────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
    ↓ Input ↓       ↓ Logic ↓      ↓ Database ↓
        │                │                │
┌───────┴────────────────┴────────────────┴──────────┐
│                      SERVICE                       │
│            (Business Logic Processing)            │
└────────────────────────┬────────────────────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
    ↓ Validate ↓    ↓ Check ↓     ↓ Compute ↓
        │                │                │
┌───────┴────────────────┴────────────────┴──────────┐
│                       MODEL                        │
│      (Query Building, Relationships, Scopes)       │
└────────────────────────┬────────────────────────────┘
                         │
                    ↓ SQL Query ↓
                         │
┌─────────────────────────────────────────────────────────────────┐
│                      DATABASE                                   │
│             (MySQL/MariaDB - Data Storage)                       │
└────────────────────────┬────────────────────────────────────────┘
                         │
                    ↓ Result Set ↓
                         │
        ← Collection loaded back ←
                         │
        ┌────────────────┴────────────────┐
        │                                 │
    ↓ Format Response ↓         ↓ Event Trigger ↓
        │                                 │
┌───────┴─────────────────────────────────┴───────┐
│               RESPONSE BUILDER                   │
│    (JSON or View rendering with data)           │
└──────────────────────┬──────────────────────────┘
                       │
                  ↓ Send ↓
                       │
┌──────────────────────────────────────────────────┐
│              BROWSER / CLIENT                    │
│  (Render HTML / Update DOM / Show Response)      │
└──────────────────────────────────────────────────┘
```

---

## 🎯 Design Patterns Used

### 1. **MVC Pattern**
Separates concerns into Models, Views, and Controllers.

### 2. **Service Pattern**
Encapsulates business logic in service classes.

```php
// Bad: Logic in controller
public function store(Request $request)
{
    // 50 lines of complex validation and logic...
}

// Good: Use service
public function store(Request $request, DutyAssignmentService $service)
{
    $result = $service->createAssignment($request->validated());
    return redirect()->back()->with('success', 'Created successfully');
}
```

### 3. **Repository Pattern** (Recommended)
Abstract data access into repository classes.

```php
class SoldierRepository
{
    public function getAvailableSoldiers(Rank $rank, Date $date)
    {
        return Soldier::where('rank_id', $rank->id)
            ->where('status', 'active')
            ->whereDoesntHave('duties', function ($query) use ($date) {
                $query->whereDate('assignment_date', $date);
            })
            ->get();
    }
}
```

### 4. **Observer Pattern**
Uses Eloquent events and listeners.

```php
// app/Models/LeaveApplication.php
protected static function booted()
{
    static::created(function (LeaveApplication $leave) {
        event(new LeaveCreated($leave));
    });
    
    static::updated(function (LeaveApplication $leave) {
        if ($leave->isDirty('status') && $leave->status === 'approved') {
            event(new LeaveApproved($leave));
        }
    });
}
```

### 5. **Factory Pattern**
Used in model factories and seeders.

```php
// database/factories/SoldierFactory.php
class SoldierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rank_id' => Rank::factory(),
            'company_id' => Company::factory(),
            'name' => $this->faker->name(),
            'nric' => $this->faker->unique()->numerify('###-##-#####'),
            'dob' => $this->faker->dateOfBirth(),
        ];
    }
}
```

### 6. **Middleware Pattern**
Request/response pipeline processing.

```php
// app/Http/Middleware/CheckLeaves.php
class CheckLeaves
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->hasPendingLeave()) {
            $request->merge(['pending_leave' => true]);
        }
        return $next($request);
    }
}
```

### 7. **Eloquent Scope Pattern**
Reusable query logic.

```php
// app/Models/Soldier.php
public function scopeActive($query)
{
    return $query->where('status', 'active');
}

public function scopeByRank($query, Rank $rank)
{
    return $query->where('rank_id', $rank->id);
}

// Usage:
$soldiers = Soldier::active()->byRank($rank)->get();
```

---

## 🔌 Dependency Injection

All controllers and services use constructor injection:

```php
class DutyAssignmentController extends Controller
{
    public function __construct(
        private DutyAssignmentService $service,
        private NotificationService $notification
    ) {}
    
    public function assignSoldier(Request $request)
    {
        $assignment = $this->service->create($request->validated());
        $this->notification->notify($assignment);
        return response()->json($assignment);
    }
}
```

---

## 📦 Package Architecture

### Core Packages

| Package | Purpose | Version |
|---------|---------|---------|
| Laravel | Web framework | 10.0 |
| Sanctum | API authentication | 3.2 |
| Permission | RBAC system | 6.21 |
| Activity Log | Audit trail | 4.10 |
| Excel | Excel export | 3.1 |
| DomPDF | PDF generation | 3.1 |
| DataTables | Data table library | 10.0 |

### Integration Points

```
Laravel Core
    ├── Routing
    ├── Authentication (Sanctum)
    ├── Authorization (Permission)
    ├── ORM (Eloquent)
    ├── Validation
    ├── Events
    └── Middleware
    
Frontend Tools
    ├── Tailwind CSS
    ├── Alpine.js
    ├── Axios
    └── Font Awesome
    
Data Processing
    ├── Excel Export (Maatwebsite)
    ├── PDF Generation (DomPDF)
    └── DataTables (Yajra)
    
Audit & Logging
    ├── Activity Log (Spatie)
    └── Laravel Logging
```

---

## 🔐 Security Architecture

### Authentication Flow
```
Request with credentials
    └─→ LoginController@authenticate()
        └─→ Hash::check($password)
            └─→ Create session / token
                └─→ Store in cookies / database
                    └─→ Return authenticated response
```

### Authorization Flow
```
Authenticated request
    └─→ Middleware checks authorization
        ├─→ Check role (role:admin)
        ├─→ Check permission (can:edit,soldier)
        └─→ Pass/Fail
            └─→ Allow/Deny request
```

### Data Protection
- Sensitive fields encrypted using `encrypted` cast
- CSRF tokens on all forms
- SQL injection prevented via parameterized queries
- XSS prevented via output escaping

---

## 🚀 Performance Optimization

### Query Optimization

#### Eager Loading (Prevent N+1 Problem)
```php
// ❌ Bad: 51 queries (1 + 50)
foreach (Soldier::all() as $soldier) {
    echo $soldier->rank->name;  // 1 query per soldier
}

// ✅ Good: 2 queries (1 + 1)
foreach (Soldier::with('rank')->get() as $soldier) {
    echo $soldier->rank->name;
}
```

#### Query Scopes
```php
// Reusable query logic
public function scopeWithRelations($query)
{
    return $query->with(['rank', 'company', 'duties']);
}

// Usage:
$soldiers = Soldier::withRelations()->get();
```

#### Pagination
```php
// Efficient data loading
$soldiers = Soldier::paginate(15);  // 15 records per page
```

### Caching Strategy

```php
// Cache query results
$ranks = Cache::remember('ranks', 3600, function () {
    return Rank::all();
});
```

### Frontend Performance

- Lazy loading for images
- Minified CSS/JS (Vite)
- Alpine.js for DOM efficiency
- Tailwind's JIT compiler

---

## 🧪 Testing Architecture

### Test Types

#### Unit Tests
```php
// tests/Unit/DutyTest.php
class DutyTest extends TestCase
{
    public function test_duty_requires_soldiers()
    {
        $duty = Duty::factory()->create();
        $this->assertGreater($duty->required_count, 0);
    }
}
```

#### Feature Tests
```php
// tests/Feature/DutyAssignmentTest.php
class DutyAssignmentTest extends TestCase
{
    public function test_admin_can_assign_soldier_to_duty()
    {
        $admin = User::factory()->admin()->create();
        $soldier = Soldier::factory()->create();
        $duty = Duty::factory()->create();
        
        $response = $this->actingAs($admin)->post(
            '/duty-assignments/assign-soldier',
            ['soldier_id' => $soldier->id, 'duty_id' => $duty->id]
        );
        
        $response->assertSuccessful();
        $this->assertDatabaseHas('soldier_duty', [
            'soldier_id' => $soldier->id,
            'duty_id' => $duty->id
        ]);
    }
}
```

### Running Tests
```bash
php artisan test                    # All tests
php artisan test --coverage         # With coverage
php artisan test tests/Unit         # Specific suite
```

---

## 📚 Module Structure

### Personnel Module
```
Personnel Management
├── Profile Creation (4-step form)
├── Profile Viewing & Editing
├── Search & Filtering
├── Bulk Operations
├── Medical Records
├── Qualifications & Skills
└── Activity Tracking
```

### Duty Module
```
Duty Management
├── Duty Definition
├── Requirement Specification
├── Availability Checking
├── Soldier Assignment
├── Batch Assignment
├── Statistics & Analytics
└── Export Reports
```

### Leave Module
```
Leave Management
├── Application Submission
├── Approval Workflow
├── Balance Tracking
├── Type Management
├── Bulk Updates
└── Notifications
```

### Settings Module
```
System Configuration
├── Organization Structure
├── Personnel Data
├── Training Programs
├── System Behavior
├── Access Control
└── Site Settings
```

---

## 🔄 Integration Points

### External Systems Integration

#### Email Service
- Leave notifications
- Approvals
- System alerts

#### Database Replication
- Backup automation
- Data sync

#### Report Generation
- Excel export
- PDF generation

---

## 📈 Scalability Considerations

### Database Optimization
- Add indexes on frequently queried columns
- Archive old records
- Partition large tables
- Use read replicas for reporting

### Caching
- Cache frequently accessed data
- Use Redis for session storage
- Cache permission checks

### Queue Processing
- Move heavy operations to queue job
- Process exports asynchronously
- Send notifications via queue

```php
// Move to queue
dispatch(new ExportDuties($parameters))->onQueue('exports');
```

### Load Balancing
- Multiple application servers
- Session stored in Redis/Database
- Database read replicas
- CDN for static assets

---

## 🛠️ Extending the System

### Adding New Feature

1. **Create Migration**
   ```bash
   php artisan make:migration create_new_features_table
   ```

2. **Create Model**
   ```bash
   php artisan make:model NewFeature -m
   ```

3. **Create Controller**
   ```bash
   php artisan make:controller NewFeatureController --resource
   ```

4. **Add Routes**
   ```php
   Route::resource('new-features', NewFeatureController::class);
   ```

5. **Create Views**
   ```
   resources/views/new-feature/
   ├── index.blade.php
   ├── create.blade.php
   ├── edit.blade.php
   └── show.blade.php
   ```

6. **Test**
   ```bash
   php artisan make:test NewFeatureTest --feature
   ```

---

**Last Updated**: March 2026  
**Version**: 1.0  
**Architecture**: MVC + Service Layer + SOLID Principles
