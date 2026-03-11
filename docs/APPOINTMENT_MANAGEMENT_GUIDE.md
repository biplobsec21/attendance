# Appointment Management Module Documentation

## Module Overview

The **Appointment Management Module** is a comprehensive system for managing soldier appointments, postings, and service assignments. It enables:

- **Appointment Creation** - Create and assign appointments/postings for soldiers
- **Multiple Appointments** - Support soldiers having multiple simultaneous roles
- **Appointment Tracking** - Monitor current and completed appointments
- **Service Management** - Manage soldier services and postings
- **Appointment Details** - Record specific information for each appointment
- **Appointment Release** - Remove soldiers from appointments
- **Status Management** - Track active, scheduled, and completed appointments
- **Appointment History** - Complete record of all soldier assignments and postings

This module ensures soldiers are properly assigned to their roles, tracked in their positions, and managed through appointment lifecycle from assignment through release.

---

## Key Features

### 1. **Appointment Assignment to Soldiers**

**What it does:**
- Create appointments and assign soldiers to specific roles or postings
- Track which soldier is assigned to which appointment
- Record appointment details and requirements
- Set start and end dates for appointments
- Link soldiers to their designated positions

**Appointment Information:**
- Appointment/position title
- Appointment type (Staff, Field, Training, etc.)
- Department or unit assigned to
- Appointment location/posting location
- Appointment classification

**Soldier Selection:**
- Choose soldier from list
- Verify soldier is active
- View soldier's current appointments
- Check availability
- Confirm assignment

**When to use:**
- New soldier postings to positions
- Staff appointments within units
- Transfer to new roles
- Special assignments
- Temporary duty assignments

---

### 2. **Multiple Simultaneous Appointments**

**What it does:**
The system allows a single soldier to hold multiple appointments simultaneously. This is useful for soldiers with multiple responsibilities, special assignments, or dual-hatted positions.

**How It Works:**
- Soldier can have appointment as "Platoon Leader" AND "Training Officer"
- Each appointment is tracked separately
- Soldier appears in multiple appointment lists
- Can have different end dates for each appointment
- Status tracked independently per appointment

**Example Scenarios:**
```
Soldier: SGT John Smith

Appointment 1: Platoon Leader (Start: Jan 1, End: Dec 31)
Appointment 2: Safety Officer (Start: Mar 1, End: Jun 30)
Appointment 3: Training Coordinator (Start: Feb 1, End: Aug 31)

All three are active simultaneously!
```

**When to use:**
- Dual-hatted positions (primary + secondary roles)
- Additional duty assignments
- Special assignments alongside main duty
- Task forces or committees
- Temporary augmentation to existing role

---

### 3. **Current Appointments View**

**What it does:**
- Display all active appointments currently assigned
- Show appointment status and soldier details
- Monitor who is assigned to what positions
- Check appointment fill status
- See appointment workload

**Information Displayed:**
- Soldier name and rank
- Appointment/position name
- Appointment type
- Department/unit
- Start date of appointment
- End date (if expiring soon)
- Days remaining in appointment
- Status indicator

**View Features:**
- Sort by soldier name
- Sort by appointment
- Sort by start date
- Sort by days remaining
- Filter by department
- Filter by appointment type
- Search by soldier name
- Search by appointment name

**When to use:**
- Checking who filled which positions
- Planning for soon-to-end appointments
- Verifying soldier assignments
- Organizational review
- Accountability reporting

---

### 4. **Past Appointments History**

**What it does:**
- View all completed and past appointments
- Track soldier's appointment history
- Show previous assignments and dates
- Maintain historical record
- View how long soldier was in each appointment

**What's Included:**
- Appointment name and type
- Soldier assigned
- Start date
- End date (completion date)
- Total duration in role
- How appointment ended (Released, Completed, etc.)
- Notes from appointment

**When to use:**
- Checking soldier's career progression
- Verifying previous assignments
- Compliance and audit purposes
- Reference for promotion decisions
- Training and qualification history

---

### 5. **Create New Appointment**

**What it does:**
- Create new appointment/position entry in system
- Define appointment details
- Make it available for soldier assignment
- Set appointment parameters

**Appointment Setup:**
- Appointment/position title
- Description of duties
- Department/unit
- Location
- Required rank/qualifications
- Appointment type
- Special considerations

**Creating an Appointment:**

```
Step 1: Go to Appointments → Create New

Step 2: Enter Appointment Details
        ├─ Appointment Title
        ├─ Description
        ├─ Type (Staff, Field, Training, etc.)
        ├─ Department/Unit
        └─ Location

Step 3: Assign to Soldier (After Creation)
        └─ Can assign immediately or later

Step 4: Save
        └─ Appointment available for assignment
```

---

### 6. **Assign Soldier to Appointment**

**What it does:**
- Take an existing appointment
- Assign a soldier to that appointment
- Set appointment dates
- Record assignment details

**Assignment Information:**
- Soldier name
- Appointment name
- Start date (when soldier takes on role)
- End date (when appointment expires or soldier leaves)
- Notes about the assignment
- Special duties or requirements

**Assignment Process:**

```
Step 1: Choose Appointment
        ↓
Step 2: Select Start Date
        (When soldier begins appointment)
        ↓
Step 3: Select End Date (Optional)
        (When appointment ends - can leave empty if indefinite)
        ↓
Step 4: Choose Soldier to Assign
        (Select from available soldiers)
        ↓
Step 5: Add Notes (Optional)
        (Special instructions, duties, requirements)
        ↓
Step 6: Save Assignment
        (Soldier now holds this appointment)
```

---

### 7. **Appointment Status Management**

**What it does:**
The system manages appointment status throughout soldier's tenure.

**Appointment Status States:**

| Status | Meaning | Duration |
|--------|---------|----------|
| **Scheduled** | Appointment assigned, not yet started | Before start date |
| **Active** | Soldier currently in appointment | On/after start date, before end date |
| **Completed** | Appointment finished, soldier no longer in role | After end date |
| **Released** | Soldier manually removed from appointment | Any time |
| **Expired** | Appointment end date passed | After end date |

**Status Examples:**

```
Soldier: SFC Maria Garcia
Appointment: Platoon Sergeant
Assignment: Jan 1 - June 30

Jan 1:    Status = ACTIVE (started appointment)
June 29:  Status = ACTIVE (still in role)
July 1:   Status = COMPLETED (end date passed)
```

---

### 8. **Release Appointment**

**What it does:**
- Remove a soldier from an appointment
- End soldier's tenure in that position
- Record release date and reason
- Free up appointment for another soldier

**Release Process:**

```
Step 1: Find Active Appointment
        └─ Search for soldier's active appointment

Step 2: Click "Release" Button
        ↓
Step 3: Select Release Date
        (When soldier leaves position)
        ↓
Step 4: Add Release Reason (Optional)
        ├─ Reassignment
        ├─ Promotion
        ├─ End of tour
        ├─ Performance issue
        └─ Other

Step 5: Add Release Notes
        └─ Additional context

Step 6: Confirm Release
        (Soldier removed from appointment)
        ↓
Step 7: System Updates
        ├─ Changes status to "Released"
        ├─ Removes from active list
        ├─ Moves to history
        └─ Appointment becomes available
```

**Effects of Release:**
- Soldier no longer linked to appointment
- Status shows "Released"
- Moved to past appointments
- Appointment available for new soldier
- Record kept for history and audit

---

### 9. **Update Appointment Details**

**What it does:**
- Modify existing appointment information
- Edit appointment end dates
- Add or update notes
- Change appointment details

**What Can Be Updated:**
- End date (extend or shorten appointment)
- Appointment notes
- Duties or requirements
- Department/location
- Special considerations

**When to use:**
- Appointment extended beyond original date
- Early end due to transfer
- Notes or duties changed
- Updated requirements
- Correction of information

**Update Process:**
```
Step 1: Find Appointment
        ↓
Step 2: Click "Edit" or "Update"
        ↓
Step 3: Modify Details
        └─ Change dates, notes, etc.
        ↓
Step 4: Save Changes
        └─ System updates appointment
```

---

### 10. **Search and Filter Appointments**

**What it does:**
- Search current and past appointments
- Filter by soldier, appointment, or date
- Find specific appointments quickly
- View appointment workload

**Search Options:**
- **By Soldier** - All appointments for specific soldier
- **By Appointment** - All soldiers in specific role
- **By Department** - All appointments in unit
- **By Date** - Appointments in time period
- **By Status** - Active, Released, Completed

**Filter Combinations:**
- Soldiers in Department X who are "Active"
- Appointments "expiring in 30 days"
- Soldiers "released in past 90 days"
- All appointments "created in January"

---

### 11. **Appointment Details View**

**What it does:**
- Show all information about single appointment assignment
- View complete appointment history
- Show soldier details
- Display dates and status
- View notes and special requirements

**Information Shown:**
- Soldier name and rank
- Appointment title and type
- Department/unit assignment
- Start date of appointment
- End date (if applicable)
- Current status
- Days in current appointment
- Assignment notes
- Release notes (if applicable)
- Appointment history for this soldier

---

### 12. **Appointment History and Records**

**What it does:**
- Maintain complete history of soldier appointments
- Track all past assignments
- Show career progression through positions
- Create audit trail
- Enable compliance documentation

**Information Tracked:**
- Soldier's name
- Each appointment held
- Dates in each appointment
- Duration in role
- How appointment ended
- Notes and observations
- Career progression timeline

**Uses:**
- Verify soldier work history
- Promotion decisions
- Security clearance reviews
- Compliance auditing
- Calculating tenure in positions

---

## Conditions and Business Rules

### **Rules for Appointment Creation**

| Rule | Description |
|------|-------------|
| **Unique Title** | Appointment title should be meaningful and clear |
| **Department Required** | Appointment must be assigned to unit/department |
| **No Duplicate Active** | Generally only one soldier per appointment at a time |
| **Requirements Documented** | Rank/qualification requirements should be noted |
| **Type Specified** | Appointment type (Staff, Field, etc.) must be set |

---

### **Rules for Soldier Assignment**

| Rule | Description |
|------|-------------|
| **Soldier Must Be Active** | Only active soldiers can be appointed |
| **Valid Date Range** | Start date must be before end date |
| **Start Date Required** | When appointment begins |
| **End Date Optional** | Can be indefinite if no set end |
| **Multiple Allowed** | Soldier can have multiple simultaneous appointments |
| **No Duplicate** | Soldier cannot be assigned same appointment twice simultaneously |

---

### **Rules for Appointment Release**

| Rule | Description |
|------|-------------|
| **Active Appointment Only** | Can only release active appointments |
| **Release Date Required** | When soldier leaves appointment |
| **Reason Optional** | Note why release occurred if possible |
| **Cannot Undo** | Release action is permanent (can reassign if needed) |
| **Release Logged** | All releases recorded for audit trail |

---

### **Rules for Multiple Appointments**

| Rule | Description |
|------|-------------|
| **Concurrent Allowed** | Soldier can hold multiple appointments simultaneously |
| **Separate Tracking** | Each appointment tracked independently |
| **Independent Status** | Each appointment has own status and dates |
| **Separate Releases** | Each appointment released independently |
| **Full History** | All appointments appear in soldier's record |

---

## User Workflows

### **Workflow 1: Create New Appointment Position**

```
Step 1: Navigate to Appointments Module
        ↓
Step 2: Click "Create Appointment" or "New Position"
        ↓
Step 3: Enter Appointment Details
        ├─ Position Title (e.g., "Platoon Sergeant")
        ├─ Appointment Type (Staff, Field, Training)
        ├─ Department/Unit
        ├─ Location/Posting
        └─ Description of duties
        ↓
Step 4: Set Requirements (Optional)
        ├─ Rank required
        ├─ Qualifications needed
        └─ Special considerations
        ↓
Step 5: Save Appointment
        ├─ Position created in system
        ├─ Now available for assigning soldiers
        └─ Confirmation displayed
```

---

### **Workflow 2: Assign Soldier to Active Appointment**

```
Step 1: Navigate to Appointments
        ↓
Step 2: Click "Assign Soldier" or "Current Appointments"
        ↓
Step 3: Choose Appointment Position
        ├─ Select from available positions
        ├─ Review appointment details
        └─ Verify type and requirements
        ↓
Step 4: Select Soldier to Assign
        ├─ Choose from active soldiers
        ├─ Verify soldier is active
        └─ Check soldier's current appointments
        ↓
Step 5: Set Appointment Dates
        ├─ Start Date (when soldier begins)
        └─ End Date (when appointment ends - optional)
        ↓
Step 6: Add Assignment Notes (Optional)
        ├─ Special duties
        ├─ Requirements
        └─ Observations
        ↓
Step 7: Save Assignment
        ├─ Soldier assigned to appointment
        ├─ Status: "Active" (if start date reached)
        └─ System begins tracking
        ↓
Step 8: Confirmation
        └─ "Soldier assigned to appointment"
```

---

### **Workflow 3: Soldier with Multiple Appointments**

```
Step 1: Soldier Has Primary Appointment
        └─ Platoon Sergeant (Jan 1 - Dec 31)
        
        ↓
        
Step 2: Soldier Gets Additional Assignment
        └─ Safety Officer (Mar 1 - Jun 30)
        
        ↓
        
Step 3: Both Appointments Active Simultaneously
        ├─ Appointment 1: Platoon Sergeant - ACTIVE
        └─ Appointment 2: Safety Officer - ACTIVE
        
        ↓
        
Step 4: When First Appointment Ends
        ├─ Appointment 1: Platoon Sergeant - COMPLETED
        └─ Appointment 2: Safety Officer - ACTIVE (still active)
        
        ↓
        
Step 5: When Second Appointment Ends
        ├─ Appointment 1: Platoon Sergeant - COMPLETED
        └─ Appointment 2: Safety Officer - COMPLETED
        
        ↓
        
Step 6: Both in History
        └─ Soldier's complete appointment record shown
```

---

### **Workflow 4: Check Current Appointments**

```
Step 1: Go to Appointments Module
        ↓
Step 2: Click "Current Appointments" or "Active"
        ↓
Step 3: View All Active Appointments
        ├─ Shows all soldiers currently assigned
        ├─ Shows appointment names
        ├─ Shows days remaining
        └─ Shows status
        ↓
Step 4: Optional - Filter or Search
        ├─ Filter by department
        ├─ Search by soldier name
        ├─ Filter by appointment type
        └─ Sort by end date
        ↓
Step 5: View Results
        └─ See all current assignments
        
        ↓
Step 6: Take Action if Needed
        ├─ Click on appointment to see detail
        ├─ Edit dates if extending
        ├─ Release if ending
        └─ Add notes
```

---

### **Workflow 5: Release Soldier from Appointment**

```
Step 1: Navigate to Current Appointments
        ↓
Step 2: Find Soldier's Active Appointment
        └─ Search or filter to locate
        
        ↓
Step 3: Click "Release" Button
        ↓
Step 4: Select Release Date
        ├─ Today (immediate release)
        ├─ Specific future date
        └─ Retroactive date
        ↓
Step 5: Enter Release Information
        ├─ Reason for release:
        │  ├─ Reassignment
        │  ├─ Promotion
        │  ├─ End of tour
        │  ├─ Performance issue
        │  └─ Other
        └─ Additional notes
        ↓
Step 6: Confirm Release
        └─ Click "Release Soldier"
        
        ↓
Step 7: System Updates
        ├─ Changes status to "Released"
        ├─ Records release date/time
        ├─ Removes soldier from appointment
        ├─ Moves to past appointments
        └─ Appointment becomes available
        
        ↓
Step 8: Confirmation
        └─ "Soldier released from appointment"
```

---

### **Workflow 6: View Soldier's Appointment History**

```
Step 1: Navigate to Appointments
        ↓
Step 2: Search for Soldier
        ├─ By name
        └─ By rank/ID
        ↓
Step 3: View Soldier's Profile
        ↓
Step 4: Click "Appointment History" Tab
        ↓
Step 5: See All Past Appointments
        ├─ Each appointment listed with:
        │  ├─ Appointment name
        │  ├─ Start date
        │  ├─ End date
        │  ├─ Duration
        │  └─ How it ended
        
        ↓
Step 6: View Career Progression
        └─ See how soldier moved through roles
        
        ↓
Step 7: Optional - Export
        └─ Print or download history
```

---

### **Workflow 7: Extend or Modify Appointment**

```
Step 1: Find Active Appointment
        └─ In current appointments list
        
        ↓
Step 2: Click "Edit" or "Modify"
        ↓
Step 3: Update Appointment Details
        ├─ Change End Date
        │  └─ Extend appointment, change removal date
        ├─ Update Notes
        │  └─ Add new information
        └─ Other details
        ↓
        Can NOT change:
        ├─ Soldier assigned
        ├─ Appointment position
        └─ Start date (would require release/reassignment)
        
        ↓
Step 4: Save Changes
        ├─ System updates appointment
        ├─ New details saved
        └─ Confirmation shown
        
        ↓
Step 5: Updated Appointment
        └─ Changes take effect immediately
```

---

## System Behavior & Automation

### **What Happens When Appointment Is Assigned?**

1. **Assignment Created** with start/end dates
2. **Status Set** based on dates:
   - If start date in future → "Scheduled"
   - If start date today or past → "Active"
3. **Soldier Linked** to appointment
4. **Removed from Available** list
5. **Record Created** for tracking
6. **History Updated** with new appointment

---

### **What Happens When Start Date Arrives?**

1. **System Checks** scheduled appointments
2. **Status Auto-Updates** to "Active"
3. **Soldier Now** occupies appointment
4. **Visible in Current** appointments
5. **Removed from Scheduled** list
6. **Progress Begins** tracking

---

### **What Happens When End Date Arrives?**

1. **Status Auto-Changes** to "Completed"
2. **Soldier No Longer** in active appointment
3. **Appointment Marked** as completed
4. **Moved to History** - Past appointments
5. **Position Becomes** available again
6. **Soldier Can Be** assigned to new appointment

---

### **What Happens When Soldier Is Released?**

1. **Release Recorded** with date/reason
2. **Status Changes** to "Released"
3. **Soldier Immediately** removed from appointment
4. **Appointment Ends** prematurely
5. **Moved to History** - Shows how ended
6. **Available for** new soldier assignment

---

## Quick Reference Guide

| Need to...? | What to Do |
|-------------|-----------|
| Create new appointment | Create New → Enter details → Save |
| Assign soldier to appointment | Select appointment → Choose soldier → Set dates |
| Release soldier from appointment | Find appointment → Click Release → Confirm |
| Extend appointment end date | Find appointment → Edit → Change end date |
| View all current appointments | Click "Current Appointments" → View active list |
| Check soldier's appointment history | Search soldier → View History tab |
| Find who's in specific appointment | Search appointment name → View assigned soldier |
| End appointment early | Find appointment → Release → Select today's date |
| View soldier's multiple appointments | Search soldier → See all active/past appointments |
| Export appointment records | Select appointments → Click Export → Download |

---

## Status Indicators

```
🟡 SCHEDULED   - Assigned but not yet started (before start date)
🟢 ACTIVE      - Currently in appointment
✅ COMPLETED   - Appointment finished (after end date)
🔴 RELEASED    - Soldier manually removed from appointment
⏳ EXPIRING    - Less than 30 days remaining
```

---

## Screenshots Placeholders

### [Screenshot 1: Appointments Module Dashboard]
Shows current, scheduled, and past appointments with quick statistics.

---

### [Screenshot 2: Create New Appointment Form]
Shows form to enter appointment title, type, department, and details.

---

### [Screenshot 3: Soldier Assignment Modal]
Shows selection of soldier and date entry for appointment assignment.

---

### [Screenshot 4: Current Appointments List]
Shows all active appointments with soldier names, dates, and status.

---

### [Screenshot 5: Release Appointment Modal]
Shows release date selection, reason dropdown, and notes field.

---

### [Screenshot 6: Soldier's Appointment History]
Shows timeline of all appointments with dates and durations.

---

### [Screenshot 7: Multiple Appointments View]
Shows soldier with several simultaneous appointments with different status.

---

### [Screenshot 8: Edit Appointment Form]
Shows form to modify dates and notes of active appointment.

---

### [Screenshot 9: Appointments Expiring Soon]
Shows appointments ending within 30 days with countdown indicators.

---

### [Screenshot 10: Department Appointments Overview]
Shows all appointment positions in specific department with fill status.

---

## Common Use Cases

### **Use Case 1: Regular Appointment Assignment**

1. New soldier arrives at unit
2. Review available appointments for rank
3. Assign soldier to appropriate position
4. Set start date and reasonable end date
5. Record begins tracking appointment
6. Monitor through appointment duration
7. Near end date, plan replacement
8. Release when complete, assign new soldier

---

### **Use Case 2: Dual-Role Assignment**

1. Soldier has primary appointment (Platoon Sergeant)
2. Due to workload, soldier also assigned as Safety Officer
3. Both appointments tracked simultaneously
4. Different end dates for each role
5. Soldier reports to both positions
6. Can release from one while keeping other active
7. Both appear in soldier's complete record

---

### **Use Case 3: Short-Term Augmentation**

1. Soldier has primary appointment
2. Special project needs additional person
3. Assign soldier to temporary appointment (3 months)
4. Primary appointment continues
5. Temporary appointment expires
6. System auto-completes temporary role
7. Soldier continues in primary appointment

---

### **Use Case 4: Appointment Extension**

1. Soldier in appointment ending in month
2. Leadership decides to extend tenure
3. Edit appointment end date (extend 6 months)
4. Saves updated end date
5. Soldier continues in appointment
6. New completion date tracked
7. Process repeats if extended again

---

## Best Practices

✓ **Do:**
- Assign appointments with clear start dates
- Set realistic end dates for appointments
- Regularly review appointments expiring soon
- Keep notes updated with current duties
- Release soldiers promptly when done
- Maintain appointment history for records
- Plan replacements before appointments end
- Document release reasons

✗ **Don't:**
- Leave appointments undefined indefinitely
- Assign same soldier to conflicting appointments
- Forget to release soldiers when done
- Delete appointment history
- Ignore soldiers with multiple appointments
- Change start dates on active appointments
- Assign unqualified soldiers
- Skip release documentation

---

## Frequently Asked Questions

### **Q: Can a soldier have multiple appointments at the same time?**
A: Yes! This is fully supported. Soldier can hold primary appointment plus one or more additional roles simultaneously.

---

### **Q: What's the difference between Release and Completed?**
A: **Release** = Manually ending appointment immediately. **Completed** = Automatic when end date passes. Both move to history.

---

### **Q: Can I change an appointment's start date?**
A: Generally no - you would need to release and reassign. But you can change the end date.

---

### **Q: What happens to past appointments?**
A: They stay in history permanently. Good for audit trail and showing soldier's career progression.

---

### **Q: Can I reassign a soldier to appointment they already had before?**
A: Yes, you can reassign any soldier to any appointment. Each assignment is independent.

---

### **Q: How do I plan for appointments ending soon?**
A: Use the "Expiring Soon" view (usually filtered to 30-day window) to see which appointments need planning.

---

### **Q: Can I see all appointments in my department at once?**
A: Yes, filter by department to see all active appointments in your unit.

---

### **Q: What if soldier transferred but status wasn't updated?**
A: Edit or release appointment manually. Better to release properly than delete.

---

### **Q: Can deleted appointments be recovered?**
A: System typically archives (not deletes). Contact administrator if urgent recovery needed.

---

### **Q: How often is appointment data backed up?**
A: System maintains full history. All changes logged with timestamps for audit.

---

## Troubleshooting

### **Issue: Cannot assign soldier to appointment**

**Possible Causes:**
- Soldier status is inactive
- Soldier is already assigned to same appointment
- Start date is invalid

**Solutions:**
- Verify soldier is active status
- Check if soldier already has this appointment
- Confirm start date is set correctly
- Try assigning different soldier to verify

---

### **Issue: Appointment status not updating**

**Possible Causes:**
- Start date hasn't arrived yet
- System hasn't performed daily update
- Dates may be set incorrectly

**Solutions:**
- Verify dates are correct
- Check current date vs. start/end dates
- Wait for next system update (usually overnight)
- Manually complete appointment if needed

---

### **Issue: Cannot find soldier in current appointments**

**Possible Causes:**
- Appointment completed (moved to past)
- Soldier was released
- Wrong time period selected
- Soldier may have multiple appointments

**Solutions:**
- Check "Past Appointments" if already completed
- Search soldier's history to see all previous roles
- Use filters to narrow search
- Verify soldier isn't in multiple appointments

---

### **Issue: Release function not working**

**Possible Causes:**
- Appointment is already completed
- Soldier is not active
- System permission issue

**Solutions:**
- Verify appointment is still active (not completed)
- Try refreshing page and trying again
- Contact administrator if permission issue

---

## Additional Resources

- [Soldier Management Guide](SOLDIER_MANAGEMENT_GUIDE.md) - Manage soldier information
- [Settings Page Guide](SETTINGS_PAGE_GUIDE.md) - Configure appointment types
- [Reports and Analytics Guide](REPORTS_AND_ANALYTICS_GUIDE.md) - Appointment reports

---

## Support

For issues or questions:
1. Check FAQ section above
2. Review step-by-step workflows
3. Contact your system administrator

