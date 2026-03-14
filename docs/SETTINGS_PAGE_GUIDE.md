# Settings Page Documentation

## Overview

The **Settings Page** is the administrative control center for your Manpower Management System. It's where system administrators can configure all aspects of the military personnel management system, from basic profile information to complex duty assignments and user permissions.

**Access:** Settings → (Select any category below)

---

## Settings Dashboard

When you first enter the Settings page, you see **four main categories** organized as cards:

1. **Profile Settings** (Orange Card)
2. **Duty Settings** (Blue Card)
3. **Role & Permissions** (Green Card)
4. **User Management** (Purple Card)

Each category contains related configuration options.

---

## 1. Profile Settings (Orange Card)

This section manages all **basic personnel and organizational information** that soldiers use in their records.

### Available Settings:

| Setting | Purpose |
|---------|---------|
| **RK (Rank)** | Create and manage military ranks (Officer, NCO, Private, etc.) |
| **Coy (Company)** | Organize soldiers into military companies/units |
| **Courses** | Track training courses and certifications |
| **Cadre** | Define cadre positions and roles |
| **Skill** | Create skill categories and competencies soldiers can have |
| **Skill Category** | Organize skills into logical groups |
| **Education** | Manage educational qualifications and levels |
| **Att** | Configure attendance types/statuses |
| **ERE** | Manage ERE (Exercise/Replacement/Evaluation) categories |
| **Comd** | Define command structure and hierarchy |
| **Ex-area** | Set up exercise areas/locations |
| **Medical Category** | Create medical classification categories |
| **Sickness** | Define permanent medical conditions/limitations |
| **Instr** | Set up instruction types and recommendations |

**When to use:**
- Set up your organizational structure when first implementing the system
- Add new ranks or companies as your organization grows
- Define new skills or qualifications soldiers can earn

---

## 2. Duty Settings (Blue Card)

This section manages **all duty-related configurations**, which is the core of military operations.

### Available Settings:

| Setting | Purpose |
|---------|---------|
| **Duty** | Create and manage duty assignments (detailed in [Duty Module Guide](DUTY_MODULE_GUIDE.md)) |
| **Appts** | Create appointment positions and titles |
| **Lve type** | Define types of leave (Annual, Sick, Emergency, etc.) |
| **Absent Type** | Create absence categories (AWOL, Medical, Approved Leave, etc.) |
| **Site Settings** | Configure system-wide settings (times, defaults, business rules) |

**When to use:**
- Set up duty schedules and assignments
- Define leave types that soldiers can request
- Configure absence types for attendance tracking
- Adjust site settings for your organization's operational hours

---

### **Detailed: Duty Section**
Learn more in the [Complete Duty Module Guide](DUTY_MODULE_GUIDE.md) which covers:
- Creating duties with soldier assignments
- Managing fixed vs. roster-based assignments
- Automatic availability checking
- Bulk operations and exports

---

### **Detailed: Site Settings**
Manages system-wide configuration:
- Operational hours (PT times, PT time settings)
- Default values
- Business rules
- System preferences

---

## 3. Role & Permissions (Green Card)

This section manages **user access control** - who can do what in the system.

### Available Settings:

| Setting | Purpose |
|---------|---------|
| **Roles** | Create user role groups (Admin, Officer, NCO, View-Only Staff) |
| **Permissions** | (Advanced) Fine-tune specific permissions for roles |

**When to use:**
- Create role groups for different user types
- Assign permissions to roles (currently use Roles for simplicity)
- Control who can view, create, edit, or delete information

---

## 4. User Management (Purple Card)

This section manages **individual user accounts** in the system.

### Available Settings:

| Setting | Purpose |
|---------|---------|
| **User List** | View all users and their details |
| **User Create** | Add new user accounts to the system |
| **Auth Manpower** | Authorize which personnel have system access |

**When to use:**
- Add new staff members to the system
- View existing user accounts
- Assign system access to military personnel
- Manage user permissions and roles

---

## Common Workflows

### **Workflow 1: Initial System Setup**

```
Step 1: Set up organizational structure
        → Profile Settings → RK (add ranks)
        → Profile Settings → Coy (add companies)
        → Duty Settings → Appts (add positions)

Step 2: Define operational parameters
        → Duty Settings → Site Settings
        → Set PT times, operational hours, business rules

Step 3: Create leave and absence types
        → Duty Settings → Lve type (Annual, Sick, etc.)
        → Duty Settings → Absent Type (AWOL, etc.)

Step 4: Set up user access
        → Role & Permissions → Roles (create admin, officer, view roles)
        → User Management → Create Users

Step 5: Authorize personnel
        → User Management → Auth Manpower
        → Select personnel who can access the system
```

---

### **Workflow 2: Adding a New Rank**

```
Step 1: Go to Settings
        ↓
Step 2: Profile Settings → RK (Rank)
        ↓
Step 3: Click "Create" or "Add New Rank"
        ↓
Step 4: Fill in rank details
        - Rank name (e.g., "Corporal", "Lance Corporal")
        - Rank abbreviation (e.g., "CPL", "LCP")
        - Any other relevant fields
        ↓
Step 5: Save
        ↓
New rank is now available for:
- Assigning soldiers
- Creating duty assignments
- Filtering and reports
```

---

### **Workflow 3: Adding a New Company**

```
Step 1: Go to Settings
        ↓
Step 2: Profile Settings → Coy (Company)
        ↓
Step 3: Click "Create" or "Add New Company"
        ↓
Step 4: Fill in company details
        - Company name
        - Company code
        - Description
        ↓
Step 5: Save
        ↓
New company is now available for:
- Assigning soldiers to companies
- Organizing soldiers
- Company-based reporting
```

---

### **Workflow 4: Creating a New User**

```
Step 1: Go to Settings
        ↓
Step 2: User Management → User Create
        ↓
Step 3: Fill in user information
        - Name
        - Email
        - Username
        - Password
        - Role assignment
        ↓
Step 4: Select which soldier record (if applicable)
        ↓
Step 5: Save
        ↓
User can now log in to the system
```

---

### **Workflow 5: Authorizing Personnel to Use System**

```
Step 1: Go to Settings
        ↓
Step 2: User Management → Auth Manpower
        ↓
Step 3: Search for or select soldiers
        ↓
Step 4: View/manage their system authorization status
        ↓
Step 5: Grant or revoke access as needed
```

---

## Navigation Guide

### **How to Access Each Section**

#### **Profile Settings → RK (Ranks)**
- Settings → Profile Settings (orange card) → RK link
- Manage all military ranks in your organization

#### **Profile Settings → Coy (Companies)**
- Settings → Profile Settings (orange card) → Coy link
- Manage all companies/units

#### **Profile Settings → Courses**
- Settings → Profile Settings (orange card) → Courses link
- Manage training courses soldiers can take

#### **Profile Settings → Skills**
- Settings → Profile Settings (orange card) → Skill link
- Define competencies and skills

#### **Duty Settings → Duty**
- Settings → Duty Settings (blue card) → Duty link
- Create and manage duty assignments

#### **Duty Settings → Leave Types**
- Settings → Duty Settings (blue card) → Lve type link
- Define types of leave soldiers can take

#### **Duty Settings → Site Settings**
- Settings → Duty Settings (blue card) → Site Settings link
- Configure system-wide operational parameters

#### **Role & Permissions → Roles**
- Settings → Role & Permissions (green card) → Roles link
- Create and manage user roles

#### **User Management → User List**
- Settings → User Management (purple card) → User List link
- View all system users

#### **User Management → Create User**
- Settings → User Management (purple card) → User Create link
- Add new user accounts

---

## Key Concepts

### **Ranks vs. Positions**
- **Rank** = Military rank (Private, Sergeant, Captain, etc.)
- **Position/Appointment** = Specific role (Squad Leader, Duty Officer, etc.)
- A soldier has **one rank** but may hold **multiple positions** over time

---

### **Roles vs. Permissions**
- **Role** = A group of permissions given to a user type (Admin, Officer, etc.)
- **Permission** = Specific action a user can do (View Reports, Create Duty, Delete User, etc.)
- Users are assigned to **Roles**, and roles have **Permissions**

---

### **Leave Types vs. Absence Types**
- **Leave Type** = Formal leave soldier requests the system tracks (Annual Leave, Sick Leave)
- **Absence Type** = Why a soldier is marked absent (Present, On Leave, AWOL, Medical)
- Leave types are formal requests; absence types are the status in attendance

---

## Best Practices

### **When Setting Up Profiles**

✓ **Do:**
- Create ranks matching your organizational structure
- Use clear, standardized naming conventions
- Add all companies before creating soldiers
- Define skills that align with your training program

✗ **Don't:**
- Create duplicate ranks with slightly different names
- Leave descriptions blank
- Set up ranks without thinking about your hierarchy

---

### **When Setting Up Duty Management**

✓ **Do:**
- Define leave types before soldiers start requesting leave
- Set site settings based on your actual operational hours
- Create clear duty names for easy searching
- Use remarks to explain duty differences

✗ **Don't:**
- Forget to set site settings before creating duties
- Use vague duty names like "Duty 1", "Duty 2"
- Leave absence types undefined

---

### **When Managing Users**

✓ **Do:**
- Create appropriate role groups for different user types
- Regularly review who has system access
- Ensure officers don't have admin-only permissions unless needed
- Document which users have which roles

✗ **Don't:**
- Give all users admin access
- Create roles that are too broad
- Forget to update permissions when roles change
- Leave inactive users in the system

---

## Permission Levels (Role Examples)

### **Admin Role**
Can:
- Create and delete any records
- Create and manage users
- Modify system settings
- View all reports
- Export data

---

### **Officer Role**
Can:
- Create and edit duties
- Assign soldiers to duties
- View soldier information
- Request and approve leave
- Generate reports (limited)

---

### **NCO Role**
Can:
- Create duties (within constraints)
- View assigned soldiers
- Mark attendance
- Request leave
- View personal records

---

### **View-Only Role**
Can:
- View duty schedules
- View soldier information (basic)
- View reports
- Cannot create or edit anything

---

## Troubleshooting Settings

### **Issue: Can't add new rank**
- Verify you have Admin permissions
- Check if rank already exists (duplicates not allowed)
- Ensure all required fields are filled

---

### **Issue: Duty assignments not working**
- Verify companies and ranks are created in Profile Settings
- Check Site Settings for operational hour configuration
- Confirm soldiers are assigned to ranks and companies

---

### **Issue: Users can't log in**
- Check if user account was created in User Create
- Verify user email/username is correct
- Ensure Auth Manpower grants them access
- Check if user's role has login permission

---

### **Issue: Leave requests not working**
- Check if Leave Types are defined in Duty Settings
- Verify absence types are created
- Confirm soldier profile is active in system

---

## Settings Organization Summary

```
SETTINGS PAGE
│
├── Profile Settings (Orange)
│   ├── RK (Ranks)
│   ├── Coy (Companies)
│   ├── Courses
│   ├── Cadre
│   ├── Skill
│   ├── Skill Category
│   ├── Education
│   ├── Att (Attendance)
│   ├── ERE
│   ├── Comd (Command)
│   ├── Ex-area (Exercise Area)
│   ├── Medical Category
│   ├── Sickness
│   └── Instr (Instruction)
│
├── Duty Settings (Blue)
│   ├── Duty
│   ├── Appts (Appointments)
│   ├── Lve type (Leave Types)
│   ├── Absent Type
│   └── Site Settings
│
├── Role & Permissions (Green)
│   ├── Roles
│   └── Permissions (Advanced)
│
└── User Management (Purple)
    ├── User List
    ├── User Create
    └── Auth Manpower
```

---

## Screenshots Placeholders

### [Screenshot 1: Settings Dashboard]
Shows the main settings page with four color-coded cards (Profile, Duty, Roles, Users).

---

### [Screenshot 2: Profile Settings Card Expanded]
Shows all options under Profile Settings with icons and links.

---

### [Screenshot 3: Duty Settings Card Expanded]
Shows all options under Duty Settings with icons and links.

---

### [Screenshot 4: Create New Rank Form]
Shows the form to add a new military rank.

---

### [Screenshot 5: Create New Company Form]
Shows the form to add a new company/unit.

---

### [Screenshot 6: Leave Types Configuration]
Shows list of leave types that can be customized.

---

### [Screenshot 7: Site Settings Configuration]
Shows global system settings like operational hours.

---

### [Screenshot 8: User Creation Form]
Shows the form to create a new user account.

---

### [Screenshot 9: Role Assignment Interface]
Shows how to assign roles and permissions to users.

---

## Quick Reference Card

| Need to...? | Go to... |
|-------------|---------|
| Add a rank | Profile Settings → RK |
| Add a company | Profile Settings → Coy |
| Create a duty | Duty Settings → Duty |
| Add leave types | Duty Settings → Lve type |
| Create a user | User Management → User Create |
| Set operational hours | Duty Settings → Site Settings |
| Create roles | Role & Permissions → Roles |
| Add skills | Profile Settings → Skill |
| Add courses | Profile Settings → Courses |
| Manage user access | User Management → Auth Manpower |

---

## Additional Resources

- [Complete Duty Module Guide](DUTY_MODULE_GUIDE.md) - Comprehensive duty management documentation
- [Database Schema](DATABASE_SCHEMA.md) - Technical database structure
- [System Architecture](PROJECT_ARCHITECTURE.md) - Technical system overview
- [API Documentation](API_DOCUMENTATION.md) - For developers integrating with the system

---

## Support

For issues or questions about specific settings:
1. Check the "Troubleshooting Settings" section above
2. Refer to module-specific guides in the docs folder
3. Contact your system administrator