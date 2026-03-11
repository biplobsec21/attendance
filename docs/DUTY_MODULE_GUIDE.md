# Duty Management Module Documentation

## Module Overview

The **Duty Management Module** is a comprehensive system for creating, assigning, and managing duty assignments for military personnel. It allows administrators to:

- **Create duty schedules** with specific time periods and requirements
- **Assign soldiers** to duties in multiple ways (fixed, roster-based, or by rank groups)
- **Track availability** to prevent scheduling conflicts
- **Manage duty status** and organize duties efficiently
- **Generate reports** and export duty information

This module is the central hub for organizing who does what, when, and for how long within your military operations.

---

## Key Features

### 1. **Create and Manage Duties**

**What it does:**
- Create new duty records with specific details like duty name, time, and duration
- Set status (Active or Inactive) to control which duties are currently in use
- Add remarks or notes for each duty for internal documentation
- Edit or delete duties as organizational needs change

**How it works:**
1. Navigate to Settings → Duty
2. Click "Create New Duty"
3. Fill in duty details (name, start time, end time, duration)
4. Set duty as Active or Inactive
5. Save and assign soldiers

---

### 2. **Assign Soldiers to Duties**

**What it does:**
The system offers THREE different ways to assign soldiers, depending on your needs:

#### **Option A: Fixed Assignments (Specific Soldiers)**
- Assign **named individuals** to a duty
- Set priority levels for each soldier (1-10, where 1 is highest priority)
- Add remarks for specific soldiers (e.g., "Primary selection", "Backup only")
- Useful for: Important duties requiring specific personnel

**Example:** Assigning soldier "Ahmed" as primary and soldier "Karim" as backup for a parade duty.

#### **Option B: Roster Assignments (By Rank)**
- Assign soldiers **by rank**, not by individual name
- Specify how many soldiers of each rank are needed
- The system automatically selects available soldiers from that rank
- Useful for: General duties needing specific manpower levels

**Example:** "Need 5 corporals and 3 privates" - system will pick them automatically based on availability.

#### **Option C: Rank Groups (Multiple Ranks Together)**
- Group multiple ranks together and assign them as one unit
- Specify total manpower needed from the entire group
- System distributes the work across the group ranks
- Useful for: Complex duties requiring mixed teams

**Example:** "Need 8 personnel from (Corporals + Lance Corporals)" as a team.

---

### 3. **Automatic Availability Checking**

**What it does:**
The system **automatically validates** whether a soldier can be assigned before adding them:

- ✓ Checks if soldier is on leave
- ✓ Checks if soldier already has conflicting duty assignments
- ✓ Checks if soldier's time schedule conflicts with the duty
- ✓ Prevents double-booking and scheduling conflicts
- ✓ Alerts you if a soldier cannot be assigned

**How it helps:**
You don't need to manually track who's already assigned. The system does it automatically and prevents mistakes.

---

### 4. **Search and Filter Duties**

**What it does:**
- Search duties by name or keyword
- Filter by status (Active or Inactive)
- Sort by creation date, duty name, or other criteria
- View statistics (total duties, active duties, etc.)
- Pagination to handle large numbers of duties

**How it helps:**
Quickly find the duty you need when managing dozens or hundreds of duties.

---

### 5. **View Duty Details**

**What it does:**
- View complete duty information (times, duration, status)
- See all soldier assignments for that duty
- See assignment breakdown (fixed vs. roster)
- View total duty hours calculated
- Check the duty schedule description

**How it helps:**
Verify all details before making final assignments or changes.

---

### 6. **Bulk Operations**

**What it does:**
- Update multiple duties at once (e.g., change status from Active to Inactive for several duties)
- Perform the same action on multiple records simultaneously
- Save time when managing many duties

**Example:** Select 5 duties and set them all to "Inactive" in one action.

---

### 7. **Duplicate Existing Duties**

**What it does:**
- Copy an existing duty to create a similar one
- Preserves all settings (time, duration, roster assignments)
- Automatically labels it as "(Copy)" to avoid confusion
- Does NOT duplicate fixed soldier assignments (you add those separately)

**When to use it:**
- Recurring duties (same duty every week/month)
- Similar duties needing quick setup
- Creating template duties for reuse

---

### 8. **Duty Excusal Options**

**What it does:**
When creating/editing a duty, you can automatically excuse assigned soldiers from:
- Next Physical Training (PT) session
- Next Games session
- Next Roll Call session
- Next Parade session

**When to use it:**
If soldiers are assigned a demanding duty, automatically excuse them from the next session of a particular activity to allow recovery.

---

### 9. **Export and Reporting**

**What it does:**
- Export duty information in various formats (Excel, PDF)
- Generate reports of all duties and assignments
- Share duty information with other stakeholders

---

## Conditions and Business Rules

### **Rules for Creating Duties**

| Rule | Description |
|------|-------------|
| **Unique Duty Name** | Each duty should have a descriptive, unique name |
| **Time Requirements** | Must have start time and end time (duty cannot span across midnight easily) |
| **Duration** | Duty duration is specified in days (1-30 days) |
| **Status Required** | Must be set as Active or Inactive |
| **Manpower Optional** | Total manpower needed is optional but recommended |

### **Rules for Soldier Assignments**

| Rule | Description |
|------|-------------|
| **Soldier Must Be Available** | Cannot assign soldiers who are on leave or have conflicting duties |
| **No Time Conflicts** | Soldier's proposed duty time cannot overlap with existing assignments |
| **Rank Matching** | Roster assignments only select soldiers of the specified rank |
| **Status Active** | Only active soldiers can be assigned |
| **Priority Levels** | Fixed assignments can have priority (1=highest, 10=lowest) |
| **Exclusivity** | A soldier can be in fixed OR roster assignments, not both for same duty |

### **Rules for Roster Assignments**

| Rule | Description |
|------|-------------|
| **Manpower Count** | Must specify how many soldiers needed from each rank |
| **Automatic Selection** | System picks available soldiers - you don't manually select them |
| **Rank-Based Only** | Cannot specify individual names, only rank requirements |

### **Rules for Fixed Assignments**

| Rule | Description |
|------|-------------|
| **Individual Selection** | You manually select specific soldier by name |
| **Priority Optional** | Can set priority, but optional |
| **Remarks Optional** | Can add notes explaining why this soldier was chosen |

---

## User Workflow

### **Creating a New Duty**

```
Step 1: Navigate to Settings Page
        ↓
Step 2: Click "Duty Settings" → "Duty" option
        ↓
Step 3: Click "Create New Duty" button
        ↓
Step 4: Fill in Basic Information
        - Duty Name (e.g., "Gate Guard May 15")
        - Start Time (e.g., 06:00 AM)
        - End Time (e.g., 06:00 PM)
        - Duration in Days (e.g., 1 day)
        - Status (Active/Inactive)
        - Remarks (optional notes)
        ↓
Step 5: Choose Assignment Type(s)
        Option A: Fixed Assignments (pick specific soldiers)
        Option B: Roster Assignments (pick by rank and count)
        Option C: Rank Groups (group ranks together)
        ↓
Step 6: Set Assignment Details
        - Select soldiers/ranks as needed
        - Set priority levels (fixed assignments)
        - Add remarks if needed
        ↓
Step 7: Configure Excusals (Optional)
        - Check boxes if soldiers should be excused from
          next PT/Games/Roll Call/Parade
        ↓
Step 8: Save Duty
        - System validates all information
        - Confirms no scheduling conflicts
        - Saves duty to database
        ↓
Duty is now Active and soldiers know their assignments
```

---

### **Editing an Existing Duty**

```
Step 1: Navigate to Duty list
        ↓
Step 2: Find the duty you want to edit
        ↓
Step 3: Click "Edit" button
        ↓
Step 4: Modify Any Fields
        - Change times if needed
        - Update assignments
        - Add/remove soldiers
        - Change status
        ↓
Step 5: System Validates Changes
        - Checks for conflicts with modified times
        - Verifies soldier availability
        ↓
Step 6: Save Changes
        ↓
Updated duty information is saved
```

---

### **Assigning Soldiers Dynamically**

```
Step 1: Create or Edit a Duty
        ↓
Step 2: Choose Assignment Type
        ↓
Step 3: For Fixed Assignments:
        - Search for soldier by name or number
        - System loads available soldiers
        - Click to select soldier
        - Add priority and remarks
        - Add more soldiers if needed
        ↓
Step 4: System Checks Availability Automatically
        - Shows if soldier is on leave
        - Shows other active assignments
        - Shows time conflicts (if any)
        - Prevents assignment if conflicts exist
        ↓
Step 5: For Roster Assignments:
        - Select rank from dropdown
        - Enter number of soldiers needed
        - System will auto-select when duty is active
        ↓
Step 6: Save Assignments
```

---

### **Deleting a Duty**

```
Step 1: Find the duty in the list
        ↓
Step 2: Click "Delete" button
        ↓
Step 3: Confirm deletion
        ↓
Duty and ALL its assignments are permanently removed
(Caution: This cannot be undone)
```

---

### **Viewing Duty Statistics**

```
Step 1: Go to Duty Management Dashboard
        ↓
Step 2: System displays:
        - Total duties created
        - Active duties (currently in use)
        - Inactive duties
        - Total soldiers assigned
        - Assignment breakdown (fixed vs roster)
```

---

## System Behavior & Automation

### **What Happens When You Create a Duty?**

1. **System saves basic duty information** (name, times, duration)
2. **System creates assignment slots** for soldiers as specified
3. **For Fixed Assignments:** Marks specific named soldiers as assigned
4. **For Roster Assignments:** Creates a requirement (e.g., "5 privates needed")
5. **System automatically selects soldiers** from roster when duty becomes active
6. **Availability checks run** automatically before assignments confirmed
7. **Soldiers receive notification** of their assignment in the system

---

### **What Happens When a Duty Status Changes?**

#### **Active → Inactive:**
- Duty stops showing in active lists
- Soldiers are notified the duty is cancelled
- Existing assignments remain in history

#### **Inactive → Active:**
- Duty appears in active lists again
- For roster assignments, system re-checks soldier availability
- Soldiers are notified of their assignments

---

### **What Happens When a Soldier Goes on Leave?**

1. System automatically checks all soldier's assigned duties
2. If assigned to an active duty:
   - Soldier is removed from that duty (if unconfirmed)
   - OR soldier's leave period is flagged for review (if confirmed)
3. Duty may need re-assignment if critical personnel are unavailable

---

### **What Happens When You Duplicate a Duty?**

1. New duty created with all settings from original
2. Name automatically changed to "Original Name (Copy)"
3. Roster assignments copied over
4. **Fixed soldier assignments NOT copied** (you must re-add them)
5. New duty is set to Inactive by default (you activate it when ready)

---

## Screenshots Placeholders

### [Screenshot 1: Duty Settings Page]
Shows the main dashboard with list of all duties, search bar, filter options, and action buttons.

---

### [Screenshot 2: Creating a New Duty - Basic Information]
Shows form fields for duty name, start time, end time, duration, and status selection.

---

### [Screenshot 3: Duty Assignment Options]
Shows three tabs/sections: Fixed Assignments, Roster Assignments, and Rank Groups to choose how to assign soldiers.

---

### [Screenshot 4: Fixed Assignment Selection]
Shows soldier selection interface with search, availability status, and priority/remarks fields.

---

### [Screenshot 5: Roster Assignment Configuration]
Shows rank selection, manpower count input, and preview of how many soldiers are available.

---

### [Screenshot 6: Duty Details View]
Shows complete duty information with all assignments, statistics, and schedule description.

---

### [Screenshot 7: Edit Duty Form]
Shows the form with pre-filled information ready to be modified.

---

### [Screenshot 8: Soldier Availability Alert]
Shows warnings when a soldier is on leave or has conflicting duties.

---

### [Screenshot 9: Duty Statistics Dashboard]
Shows overview metrics and charts of duty usage patterns.

---

## Common Use Cases

### **Use Case 1: Creating a Weekly Gate Guard Duty**

1. Create duty: "Gate Guard - Weekly"
2. Set time: 6:00 AM - 6:00 PM, 7 days
3. Use Roster Assignment: "5 Privates, 2 Corporals"
4. Save
5. System automatically picks available personnel each cycle

---

### **Use Case 2: Assigning a Specific Team to Special Event**

1. Create duty: "Annual Parade - May 15"
2. Set time: 8:00 AM - 2:00 PM, 1 day
3. Use Fixed Assignments to select:
   - "Officer A" - Priority 1
   - "Officer B" - Priority 2
   - "Soldier C" - Priority 1
4. Add remarks for each: "Team Lead", "Backup", "Equipment Handler"
5. Check "Excuse from next PT session"
6. Save
7. Only these specific soldiers are assigned, not others

---

### **Use Case 3: Quick Duty Duplication**

1. Find duty "Gate Guard - Weekly" (already created and proven)
2. Click "Duplicate"
3. System creates "Gate Guard - Weekly (Copy)" with same assignments
4. Edit the copy: Change dates, times, or roster numbers as needed
5. Activate and assign

---

### **Use Case 4: Managing Duty Conflicts**

1. Try to assign "Soldier X" to Duty A (Monday 8-12 PM)
2. System detects Soldier X already assigned to Duty B (Monday 10 AM-1 PM)
3. System displays alert: "Soldier X is unavailable - has existing duty"
4. You choose:
   - Cancel this assignment
   - Remove from Duty B and assign to Duty A
   - Use a different soldier for Duty A

---

## Frequently Asked Questions

### **Q: Can I assign a soldier to multiple duties at the same time?**
A: No. The system automatically detects time conflicts and prevents this. Soldiers can be in different duties if times don't overlap.

---

### **Q: What happens if a soldier assigned to a duty goes on leave?**
A: The system flags this as a conflict. You'll need to either reassign that duty slot to another soldier or adjust the leave dates.

---

### **Q: Can I change a duty from Roster to Fixed assignments?**
A: Yes. Edit the duty and change the assignment type. The system will require you to specify the new assignment details.

---

### **Q: If I delete a duty, can I get it back?**
A: No, deletion is permanent. If unsure, set the status to "Inactive" instead to hide it without losing data.

---

### **Q: What's the difference between Priority levels?**
A: Priority 1 = most preferred soldier for the duty. Priority 10 = least preferred. Used when you want the system to know who should be assigned first if there's a choice.

---

### **Q: Can roster assignments pick specific soldiers?**
A: No, roster assignments only specify rank and count. The system automatically picks available soldiers from that rank. For specific soldiers, use Fixed Assignments.

---

### **Q: How does the system know which soldiers to pick for roster duties?**
A: The system picks based on:
1. Rank matches the requirement
2. Soldier is Active (not deleted)
3. Soldier is not on leave
4. Soldier has no time conflicts with the duty
5. Soldier is not already assigned to this duty

---

## Important Notes

- **Duty times use 24-hour format** (e.g., 14:00 for 2:00 PM, 06:00 for 6:00 AM)
- **Duration in days includes weekends** if specified
- **Excusals are automatic** once duty is created - soldiers don't need to request
- **Status changes don't delete assignments** - assignments remain in history
- **Exporting duties** is available in multiple formats for reporting
- **Search is case-insensitive** - search will find "Guard" as "guard" or "GUARD"

---

## Support & Troubleshooting

### **Issue: Cannot assign soldier to duty**
- Check if soldier is on leave (dates may overlap)
- Check if soldier already has a duty at that time
- Verify soldier status is Active in the system

### **Issue: Roster assignment not picking soldiers**
- Verify there are active soldiers of that rank available
- Check if too many are already assigned to other duties
- Ensure duty time doesn't conflict with their other assignments

### **Issue: Duty not appearing in active list**
- Check if duty status is set to Active (not Inactive)
- Verify today's date is within the duty schedule period
- Try refreshing the page

---

## Module Location in Settings

**Settings → Duty Settings → Duty**

All duty management functions are centralized in this location for easy access.

