# Soldier Management Module Documentation

## Module Overview

The **Soldier Management Module** is the central hub for managing all military personnel information in your system. It allows administrators and officers to:

- **Create and manage soldier profiles** with complete personal information
- **Track service history** including rank progression, promotions, and postings
- **Record qualifications** such as courses, skills, and certifications
- **Maintain medical records** including medical fitness and permanent conditions
- **View comprehensive soldier details** with history and status
- **Perform bulk operations** on multiple soldier records
- **Export soldier data** for reporting and analysis

This module is essential for maintaining accurate personnel records and ensuring all soldier information is up-to-date.

---

## Key Features

### 1. **Create and View Soldier Profiles**

**What it does:**
- Add new soldiers to the system with complete personal information
- View all soldiers in a searchable, filterable list
- See quick statistics about total active, on-leave, and medical status soldiers
- Track ERE (Exercise/Replacement/Evaluation) status for each soldier

**Information Captured:**
- Full name and Army Number
- Date of birth and age
- Address and contact information
- Military rank and company assignment
- Current service status (Active, On Leave, Medical)

**How it helps:**
Quick access to see all personnel in the system with their current status at a glance.

---

### 2. **Personal Information Management**

**What it does:**
- Store and manage soldier's personal details
- Track basic biographical information
- Record district/location information
- Assign soldier to rank and company

**Fields Included:**
- Full name
- Army identification number
- Date of birth
- Address
- Contact information
- Rank assignment
- Company assignment
- Personal notes

**When to use:**
- When a new soldier joins the organization
- When updating a soldier's personal information
- When a soldier is transferred to a different rank or company

---

### 3. **Service History Tracking**

**What it does:**
- Record all service milestones and history
- Track appointments and postings
- Document service periods and dates
- Maintain complete service record for each soldier

**Information Tracked:**
- Current rank and posting
- Service start date
- Previous ranks and dates
- Appointment history
- Transfer details

**Benefits:**
- Complete audit trail of soldier's career progression
- Easy promotion tracking
- Service history for reports and analysis

---

### 4. **Qualifications and Certifications**

**What it does:**
- Document all training courses soldier has completed
- Record skills and competencies
- Track educational qualifications
- Maintain certification records

**Types of Qualifications:**
- **Courses Completed** - Training programs attended (e.g., Leadership Course, Technical Training)
- **Skills Acquired** - Professional competencies (e.g., Marksmanship, Communication)
- **Education** - Academic qualifications (High School, Degree, Certification)
- **Special Skills** - Specialized abilities relevant to military duties

**Benefits:**
- Know immediately what soldiers are qualified to do
- Identify training needs
- Plan duty assignments based on qualifications
- Track professional development

---

### 5. **Medical Records Management**

**What it does:**
- Maintain complete medical fitness records
- Document permanent medical conditions
- Track medical category classifications
- Update medical status

**Medical Information:**
- **Medical Category** - Fitness classification (Fit-A, Fit-B, Temporary Unfit, Permanent Unfit)
- **Medical Examinations** - Records of medical checkups and assessments
- **Permanent Conditions** - Ongoing medical issues (e.g., Diabetes, Back Problem)
- **Medical Notes** - Doctor's observations and recommendations

**When to use:**
- During annual medical examination
- When soldier reports medical issues
- When medical category changes
- Before assigning critical duties

**System Impact:**
- Soldiers with "Unfit" status cannot be assigned to demanding duties
- Medical status automatically affects duty eligibility
- Permanent conditions flag soldiers for review in certain assignments

---

### 6. **Soldier Status Management**

**What it does:**
The system automatically tracks three key status states:

**Active Status**
- Soldier is available for duty
- Can be assigned to any duties they qualify for
- Full system access (if they have user account)

**On Leave Status**
- Soldier has approved leave
- Automatically excluded from duty assignments
- Cannot be scheduled for duties during leave period

**Medical/Sick Status**
- Soldier is marked as medically unfit
- Cannot be assigned to most duties (except approved medical duties)
- Medical records updated with condition details

**How Status is Set:**
- Active: Default status when soldier is created
- Leave: Set when leave application is approved (see Leave Module)
- Medical: Set when medical record indicates unfitness

---

### 7. **Search and Filter Capabilities**

**What it does:**
- Search soldiers by name or Army Number
- Filter by rank, company, or status
- View soldiers with/without ERE records
- Sort by various criteria
- Pagination for managing large lists

**Searchable Fields:**
- Soldier name
- Army number
- Rank
- Company
- Service status
- Medical status

**Benefits:**
- Quickly find specific soldiers
- Identify groups of soldiers (e.g., all privates, all medical unfit)
- Generate filtered lists for reports

---

### 8. **Bulk Operations**

**What it does:**
- Perform actions on multiple soldier records at once
- Update status of many soldiers simultaneously
- Delete multiple inactive profiles
- Save time managing large groups

**Bulk Operations Available:**
- **Bulk Status Update** - Change status for multiple soldiers (Active/Inactive/On Leave/Medical)
- **Bulk Delete** - Remove multiple soldier profiles
- **Batch Updates** - Update shared information for multiple soldiers

**When to use:**
- Marking multiple soldiers as active/inactive
- Deleting records of discharged soldiers
- Updating group assignments

---

### 9. **Soldier Details View**

**What it does:**
- Display complete profile for a single soldier
- Show all service history and qualifications
- Display medical information and status
- Present all duty assignments
- Show training and development history

**Information Displayed:**
- Personal details
- Rank and company
- Current service status
- Medical fitness level
- Qualifications and certifications
- Duty history
- Leave history
- Contact information

**Benefits:**
- Get complete picture of soldier in one view
- Make informed decisions about assignments
- Verify all information before creating duties

---

### 10. **Export to Reports**

**What it does:**
- Export soldier data to external formats
- Generate reports of soldier information
- Create lists for printing or emailing
- Share soldier data with other departments

**Export Options:**
- **Excel Export** - Spreadsheet format for analysis
- **PDF Export** - Document format for printing
- **List Export** - Selected columns only
- **Filtered Export** - Export only soldiers matching current filters

**When to use:**
- Creating personnel reports
- Generating duty rosters
- Sharing information with HR
- Creating backup records
- Analysis and planning

---

### 11. **Rank and Company Assignment**

**What it does:**
- Assign soldiers to military ranks
- Assign soldiers to companies/units
- Update rank when soldier is promoted
- Track rank history

**What This Affects:**
- Duty assignment eligibility (only soldiers of specific ranks can do certain duties)
- Salary and benefits calculations
- Organizational hierarchy
- Reporting structure
- Leave entitlements

**How It Works:**
1. Select soldier
2. Go to Personal Information section
3. Choose rank from dropdown
4. Select company assignment
5. Save changes

**Important:**
- A soldier can only have ONE rank at a time
- Changing rank creates a history record
- Reports use rank information for analysis

---

## Conditions and Business Rules

### **Rules for Creating Soldier Profiles**

| Rule | Description |
|------|-------------|
| **Unique Army Number** | Each soldier must have a unique Army Number (no duplicates allowed) |
| **Rank Required** | Soldier must be assigned to a rank before assignment to duties |
| **Company Required** | Soldier must belong to a company/unit |
| **Status Default** | New soldiers default to "Active" status |
| **Medical Category Optional** | Medical data can be added anytime, not required at creation |

---

### **Rules for Soldier Status**

| Rule | Description |
|------|-------------|
| **Active Soldiers Only Can Work** | Only "Active" soldiers can be assigned to duties |
| **Leave Blocks Duty** | Soldier marked as "On Leave" cannot be assigned any duties |
| **Medical Unfit Cannot Work** | Soldiers marked "Medical Unfit" cannot be assigned demanding duties |
| **Status Override by System** | Leave applications automatically change status to "On Leave" |
| **Status Change is Tracked** | All status changes are recorded in soldier's history |

---

### **Rules for Qualifications**

| Rule | Description |
|------|-------------|
| **Courses Must Exist First** | Only courses created in Settings can be assigned to soldiers |
| **Skills Must Be Defined** | Only pre-defined skills can be recorded |
| **Education Records Optional** | Education is not required but recommended |
| **Date Tracking** | Completion dates and expiry dates can be tracked |
| **Qualification History** | All qualifications are retained in soldier's record |

---

### **Rules for Medical Records**

| Rule | Description |
|------|-------------|
| **Medical Category Affects Duties** | Medical fitness directly impacts what duties soldier can do |
| **Permanent Conditions Stay** | Permanent medical conditions remain in record until updated |
| **Medical Status Changes Impact** | Changing medical status affects current duty assignments |
| **Examination Records Kept** | All medical examination records are maintained |
| **Doctor Notes Tracked** | Medical notes from doctors are permanently stored |

---

### **Rules for Profile Deletion**

| Rule | Description |
|------|-------------|
| **Permanent Deletion** | Deleting soldier profile cannot be undone |
| **History Lost** | All service history is deleted with the profile |
| **Duty Records Remain** | Past duty assignments are not deleted, only soldier link is removed |
| **Recommendation: Deactivate** | Instead of deleting, set status to "Inactive" to keep history |

---

## User Workflow

### **Creating a New Soldier Profile**

```
Step 1: Navigate to Soldier Management
        ↓
Step 2: Click "Create New Soldier" or "Add Soldier" button
        ↓
Step 3: Fill Personal Information
        - Army Number (unique identifier)
        - Full Name
        - Date of Birth
        - Address
        - Contact details
        ↓
Step 4: Assign Rank
        - Select from available military ranks
        - Confirm rank assignment
        ↓
Step 5: Assign Company
        - Choose company/unit
        - Set as primary company
        ↓
Step 6: Save Profile
        - System creates soldier record
        - Initially set to "Active" status
        ↓
Step 7: (Optional) Add Additional Information
        - Service history
        - Qualifications
        - Medical records
        - Contact information
```

---

### **Updating Soldier Personal Information**

```
Step 1: Find the soldier in the list
        - Use search by name or Army Number
        - Use filters to narrow list
        ↓
Step 2: Click on soldier name or "View" button
        ↓
Step 3: Click "Edit Personal Information"
        ↓
Step 4: Modify fields as needed
        - Update address if moved
        - Update phone number
        - Update contact person
        ↓
Step 5: Save Changes
        - System updates personal record
        - Changes are timestamped
        ↓
Step 6: Confirm update successful
```

---

### **Adding Service History**

```
Step 1: Open soldier's profile
        ↓
Step 2: Navigate to "Service" tab
        ↓
Step 3: Click "Add Service Record"
        ↓
Step 4: Fill in Service Details
        - Rank at time of service
        - Start date
        - End date (if applicable)
        - Appointment details
        - Service notes
        ↓
Step 5: Save Service Record
        ↓
Step 6: Record is added to soldier's service history timeline
```

---

### **Recording Qualifications**

```
Step 1: Open soldier's profile
        ↓
Step 2: Go to "Qualifications" section
        ↓
Step 3: Add Course
        - Select course from list
        - Enter completion date
        - Add certificate reference
        - Set expiry date (if applicable)
        ↓
Step 4: Add Skill
        - Select skill from available list
        - Enter proficiency level
        - Add notes
        ↓
Step 5: Add Education
        - Select education level
        - Enter institution and field
        - Add completion date
        ↓
Step 6: Save All Qualifications
```

---

### **Updating Medical Records**

```
Step 1: Open soldier's profile
        ↓
Step 2: Navigate to "Medical" section
        ↓
Step 3: Update Medical Category
        - Select medical fitness classification
        - Update examination date
        ↓
Step 4: Add Medical Conditions (if any)
        - Select permanent conditions
        - Add doctor notes
        - Upload medical documents (if applicable)
        ↓
Step 5: Save Medical Records
        - System updates medical status
        - Affects automatic duty eligibility
        ↓
Step 6: If Status Changed to Unfit
        - System removes soldier from conflicting duties
        - Notifies administrators
```

---

### **Searching for Soldiers**

```
Step 1: Go to Soldier List
        ↓
Step 2: Enter Search Term
        - Search by name
        - Search by Army Number
        - Search is case-insensitive
        ↓
Step 3: (Optional) Apply Filters
        - Filter by rank
        - Filter by company
        - Filter by status
        - Filter by medical fitness
        ↓
Step 4: Results are Displayed
        - Matching soldiers shown
        - Statistics updated
        ↓
Step 5: Click soldier to view details
```

---

### **Exporting Soldier Data**

```
Step 1: Optional - Apply filters to see only soldiers you want
        ↓
Step 2: Click "Export" button
        ↓
Step 3: Choose export format
        - Excel (.xlsx) - for spreadsheet analysis
        - PDF - for printing or archiving
        ↓
Step 4: Select which columns to include
        - Personal info
        - Service details
        - Qualifications
        - Medical info
        ↓
Step 5: Click "Download"
        - File is generated
        - Saved to your computer
        ↓
Step 6: Open file in preferred application
```

---

### **Bulk Updating Soldier Status**

```
Step 1: Go to Soldier List
        ↓
Step 2: Select Multiple Soldiers
        - Check boxes next to names
        - Or select "Select All"
        ↓
Step 3: Click "Bulk Actions" menu
        ↓
Step 4: Choose "Change Status"
        ↓
Step 5: Select New Status
        - Active
        - Inactive
        - On Leave
        - Medical
        ↓
Step 6: Confirm Action
        ↓
Step 7: Status Updated for All Selected
        - Notification displayed
        - Status changes reflected immediately
```

---

### **Deleting a Soldier Profile**

```
Step 1: Find the soldier in the list
        ↓
Step 2: Click "Delete" or more options menu
        ↓
Step 3: System Shows Warning
        - "This cannot be undone"
        - "All profile data will be deleted"
        ↓
Step 4: Confirm Deletion
        - Soldier profile is permanently removed
        - Service history cannot be recovered
        ↓
⚠️  WARNING: This is permanent! Consider deactivating instead.

Alternative: Set Status to "Inactive"
        Step 1: Select soldier
        Step 2: Change status to "Inactive"
        Step 3: Data is preserved for future reference
```

---

## System Behavior & Automation

### **What Happens When You Create a Soldier?**

1. **Profile Created** with personal information
2. **Status Set to Active** by default
3. **Record Timestamp** created
4. **Soldier ID Generated** for system reference
5. **Awaiting Qualifications** - Can now add service history, courses, medical info
6. **Ready for Duty Assignment** - Once all info is complete

---

### **What Happens When Medical Status Changes to "Unfit"?**

1. **System Checks** all active duty assignments
2. **Conflicts Flagged** if soldier has demanding duties assigned
3. **Soldier Marked** as medically unavailable
4. **Duty Hours Removed** if in roster-based duties
5. **Notification Sent** to administrators
6. **Future Duties Blocked** - Cannot assign new demanding duties

---

### **What Happens When Soldier Gets Promoted?**

1. **Old Rank Saved** in service history
2. **New Rank Applied** in profile
3. **Service Record Created** of promotion with date
4. **Duty Eligibility Changes** - May now qualify for rank-specific duties
5. **Reports Updated** to reflect new rank

---

### **What Happens When You Export Soldier Data?**

1. **Current Data Captured** exactly as shown in system
2. **Filters Applied** (if any filters were active)
3. **File Generated** in selected format
4. **Download Started** to your computer
5. **Timestamp Added** to file for record tracking
6. **Export Logged** in system audit trail

---

## Important Soldier Status Effects

### **Status: Active**
- ✓ Can be assigned to duties
- ✓ Visible in duty selection
- ✓ Can request leave
- ✓ Counts in manpower reporting

### **Status: On Leave**
- ✗ Cannot be assigned new duties
- ✗ Removed from roster-based duties
- ✓ Leave dates tracked
- ✓ Auto-returns to Active when leave ends

### **Status: Medical Unfit**
- ✗ Cannot be assigned demanding duties
- ✓ Can be assigned light duties (if configured)
- ✓ Medical need tracked
- ✓ Waiting for medical clearance

### **Status: Inactive**
- ✗ Not visible in normal lists
- ✗ Cannot be assigned anything
- ✓ Data preserved in system
- ✓ Used for retired/discharged soldiers

---

## Quick Reference Guide

| Need to...? | Where to go |
|-------------|-----------|
| Add new soldier | Soldier Management → Create New |
| View all soldiers | Soldier Management → List |
| Update personal info | Soldier → Personal Information → Edit |
| Add service history | Soldier → Service → Add Record |
| Record qualifications | Soldier → Qualifications → Add |
| Update medical info | Soldier → Medical → Update |
| Change soldier status | Soldier → Status dropdown |
| Search for soldier | Use search bar at top of list |
| Export data | Select soldiers → Export button |
| Delete soldier | Soldier → More Options → Delete |

---

## Screenshots Placeholders

### [Screenshot 1: Soldier Management Dashboard]
Shows the main list with all soldiers, search bar, filters, and action buttons.

---

### [Screenshot 2: Create New Soldier Form]
Shows the form to add a new soldier with personal information fields.

---

### [Screenshot 3: Soldier Profile - Personal Tab]
Shows personal information display and edit interface.

---

### [Screenshot 4: Soldier Profile - Service History Tab]
Shows timeline of service records and rank progression.

---

### [Screenshot 5: Soldier Profile - Qualifications Tab]
Shows courses, skills, education records with add/remove options.

---

### [Screenshot 6: Soldier Profile - Medical Tab]
Shows medical fitness category and permanent conditions records.

---

### [Screenshot 7: Soldier Profile - Complete Details View]
Shows unified view of all soldier information.

---

### [Screenshot 8: Bulk Operations Menu]
Shows checkboxes for multiple selection and bulk action options.

---

### [Screenshot 9: Export Dialog]
Shows format options and column selection for export.

---

### [Screenshot 10: Search and Filter Interface]
Shows search bar, filter dropdowns, and applied filters display.

---

## Common Use Cases

### **Use Case 1: Onboarding a New Soldier**

1. Create soldier profile with personal details
2. Add rank assignment
3. Add company assignment
4. If applicable, record existing qualifications
5. Schedule medical examination (add medical info)
6. Add to duty roster if needed

---

### **Use Case 2: Promoting a Soldier**

1. Open soldier profile
2. Go to Service history
3. Add new service record with promotion details
4. Update rank in Personal Information
5. New rank reflects in future duty assignments

---

### **Use Case 3: Soldier Returns from Leave**

- Leave period automatically ends
- Status automatically changes back to "Active"
- Can be assigned to duties again

---

### **Use Case 4: Medical Issue Discovered**

1. Update Medical records with new condition
2. Change medical status to "Temporarily Unfit"
3. System automatically removes from demanding duties
4. When recovered, update medical status to "Fit"
5. Can be reassigned to duties

---

### **Use Case 5: Generate Manpower Report**

1. Apply filters (e.g., rank, company)
2. Export to Excel
3. Use spreadsheet for analysis
4. Create reports and presentations

---

## Frequently Asked Questions

### **Q: Can I change a soldier's Army Number?**
A: Server best practice is to not change the Army Number as it's a unique identifier. Create a new profile if needed instead.

---

### **Q: What happens if I promote a soldier?**
A: Update the rank in Personal Information and add a service record. The old rank is saved in history. The soldier's duty eligibility updates automatically based on new rank.

---

### **Q: If I delete a soldier, can I recover the profile?**
A: No, deletion is permanent. Better to set status to "Inactive" if you want to preserve the history.

---

### **Q: Can a soldier have multiple qualifications?**
A: Yes, you can add unlimited courses, skills, and education records to each soldier.

---

### **Q: What does "Medical Fit-A" mean?**
A: It's a medical classification. Fit-A means fully fit for all duties. Check your Site Settings for what each category means in your organization.

---

### **Q: How do I know which soldiers are available for duty?**
A: Filter by "Active" status and "Fit" medical status. These soldiers can be assigned to duties immediately.

---

### **Q: Can I change multiple soldiers' company at once?**
A: If your system has bulk update feature, yes. Otherwise update one at a time in personal information.

---

### **Q: What's the difference between Service History and Qualifications?**
A: Service History tracks rank changes and postings. Qualifications tracks courses, skills, and education completed.

---

## Troubleshooting

### **Issue: Can't find soldier in list**
- Verify soldier exists (check "Inactive" filter too)
- Search by Army Number instead of name
- Check if you've filtered out their status
- Verify soldier's rank/company matches your filters

---

### **Issue: Can't update medical status**
- Check if you have necessary permissions
- Verify medical data exists first
- Try refreshing page and trying again

---

### **Issue: Soldier appears in list but has no rank**
- Open soldier profile
- Go to Personal Information → Edit
- Assign rank from dropdown
- Save changes

---

### **Issue: Export not working**
- Check if you have soldiers selected/filtered
- Verify browser allows file downloads
- Try different export format
- Clear browser cache and retry

---

## Best Practices

✓ **Do:**
- Keep personal information updated
- Record all qualifications as soldiers complete courses
- Update medical status when medical examination occurs
- Document rank changes with service records
- Review soldier profiles before assigning to critical duties

✗ **Don't:**
- Leave soldiers unassigned to rank or company
- Delete profiles (better to deactivate them)
- Ignore medical status changes
- Forget to update status when soldier goes on leave
- Assign unqualified soldiers to specialized duties

---

## Additional Resources

- [Settings Page Guide](SETTINGS_PAGE_GUIDE.md) - Profile setup instructions
- [Duty Module Guide](DUTY_MODULE_GUIDE.md) - Assigning soldiers to duties
- [Leave Management Guide](LEAVE_MANAGEMENT_GUIDE.md) - Managing leave requests
- [Database Schema](DATABASE_SCHEMA.md) - Technical database structure

---

## Support

For issues or questions:
1. Check the Troubleshooting section above
2. Review FAQ section
3. Contact your system administrator

