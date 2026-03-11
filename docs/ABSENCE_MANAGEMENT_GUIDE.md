# Absence Management Module Documentation

## Module Overview

The **Absence Management Module** is used to track and manage soldier absences and unauthorized leave. It enables:

- **Absence Type Definition** - Create different absence categories (AWOL, Unauthorized, Medical, etc.)
- **Absence Application** - Submit absence records with dates and reasons
- **Status Management** - Track absence status through approval workflow
- **Document Attachment** - Upload supporting documents (hard copies, evidence)
- **Absence History** - Complete record of all soldier absences
- **Filtering & Search** - Find specific absence records quickly
- **Batch Operations** - Submit absence for multiple soldiers at once
- **Approval Workflow** - Approve or reject absences with documented reasons

This module ensures accountability and maintains official records of all soldier absences from duty.

---

## Key Features

### 1. **Absence Type Management**

**What it does:**
- Define different types of absences in the system
- Create categories for unauthorized absences
- Control which absence types are active
- Enable/disable absence types as needed

**Absence Type Information:**
- Type name (e.g., "AWOL", "Medical", "Unauthorized Leave")
- Status (Active or Inactive)
- Used in absence applications
- Helps categorize different reason types

**When to use:**
- System setup - Create standard absence categories
- Adding new absence reason (e.g., "Family Emergency")
- Deactivating unused absence types
- Organization-wide absence classification

**Example Absence Types:**
```
├─ AWOL (Absent Without Leave)
├─ Unauthorized Leave
├─ Medical Emergency
├─ Compassionate Leave (Unauthorized)
├─ Family Emergency
└─ Administrative Error
```

---

### 2. **Absence Application Submission**

**What it does:**
- Submit new absence records for soldiers
- Record absence dates and duration
- Document reason for absence
- Upload supporting documents
- Can submit for single or multiple soldiers

**Absence Application Details:**
- Soldier name and ID
- Absence type
- Start date
- End date
- Reason/Description
- Hard copy document (optional)

**When to use:**
- Recording unauthorized absence
- Documenting administrative errors
- Uploading evidence of absence
- Submitting bulk absence for multiple soldiers

**Submission Process:**

```
Step 1: Go to Absence Management
        ↓
Step 2: Click "Submit Absence Application"
        ↓
Step 3: Select Absence Type
        (AWOL, Unauthorized, Medical, etc.)
        ↓
Step 4: Choose Soldier(s)
        (Can select one or multiple soldiers)
        ↓
Step 5: Set Absence Dates
        (Start date and end date)
        ↓
Step 6: Enter Reason
        (Why soldier is absent)
        ↓
Step 7: Upload Document (Optional)
        (Hard copy or evidence)
        ↓
Step 8: Submit
        (Application saved and awaits status)
```

---

### 3. **Status Management and Approval Workflow**

**What it does:**
- Track absence status through workflow
- Approve or reject absence applications
- Document rejection reasons
- Record approval/rejection dates

**Absence Status States:**

| Status | Meaning | Who Sets | Next Action |
|--------|---------|---------|------------|
| **Pending** | Just submitted, awaiting review | System | Manager approves/rejects |
| **Approved** | Accepted by manager | Manager | Recorded in history |
| **Rejected** | Denied by manager | Manager | Can be edited and resubmitted |

**Approval Process:**

```
Step 1: View Absence Application
        ├─ Soldier details
        ├─ Absence reason
        ├─ Dates
        └─ Provided documents

Step 2: Review Information
        └─ Check soldier's service record

Step 3: Make Decision
        Option A: APPROVE
        └─ Click "Approve"
           Status changes to "Approved"
           Recorded in soldier's absence history
        
        Option B: REJECT
        └─ Click "Reject"
           Enter rejection reason
           Status changes to "Rejected"
           Soldier can resubmit

Step 4: Document Decision
        └─ Timestamp and reason recorded
```

---

### 4. **Hard Copy Document Management**

**What it does:**
- Upload physical document images/scans
- Store evidence supporting absence claim
- Manage uploaded files
- Remove or replace documents

**Document Types:**
- Medical certificates (for medical absence)
- Authorization letters
- Police reports (for emergencies)
- Photographs
- Other supporting evidence

**File Management:**

```
Upload File:
1. Click "Upload Document"
2. Select file from computer
3. File stored in system
4. Reference link shown

Remove File:
1. View absence record
2. Click "Remove File"
3. File deleted from system
4. Record updated

Replace File:
1. Upload new file
2. Old file automatically deleted
3. New file replaces old
```

**File Storage:**
- Files stored in public storage
- Can be downloaded/viewed
- Organized by date and soldier
- Subject to retention policies

---

### 5. **Absence Application Filtering and Search**

**What it does:**
- Search absence records by multiple criteria
- Filter by soldier, date, type, or status
- Quick lookup of specific absences
- Generate filtered reports

**Filter Options:**
- **By Date Range** - Absences between specific dates
- **By Status** - Pending, Approved, Rejected
- **By Absence Type** - AWOL, Medical, Unauthorized, etc.
- **By Soldier** - Search by name or Army number
- **By Soldier Rank** - Filter by rank group

**Search Example:**

```
Find: All AWOL absences from Jan 1-31 that are still pending

Search Input:
- From Date: Jan 1, 2026
- To Date: Jan 31, 2026
- Absence Type: AWOL
- Status: Pending
- Soldier: [blank - all soldiers]

Result: Shows all matching absences
- SGT Ahmed AWOL Jan 5-7, Pending
- LCP Maria AWOL Jan 10, Pending
- PTE Rashid AWOL Jan 15-18, Pending
```

---

### 6. **Batch Absence Submission**

**What it does:**
- Submit absence applications for multiple soldiers at once
- Same absence type and dates for group
- Time-saving for mass operations
- Each soldier gets individual record

**When to use:**
- Multiple soldiers absent due to same event
- Post-exercise accountability
- After field operation
- Administrative processing

**Example Scenario:**

```
Scenario: After Field Exercise, 15 soldiers were unauthorized absent

Process:
1. Click "Submit Absence Application"
2. Select Absence Type: "AWOL"
3. Choose 15 soldiers (checkbox selection)
4. Set Dates: Jan 15-16, 2026
5. Reason: "Unauthorized absence after field exercise"
6. Upload documentation
7. Click "Submit"

Result: 15 individual absence records created
- Each soldier has own record
- Same type, dates, reason
- Can be approved/rejected individually or together
```

---

### 7. **Absence Update and Modification**

**What it does:**
- Edit pending absence applications
- Update dates or reason
- Replace documents
- Correct submission errors

**What Can Be Modified:**
- Absence dates (start/end)
- Reason description
- Absence type
- Upload/replace document
- Remove document

**When to use:**
- Incorrect dates submitted
- Wrong soldier selected
- Need to add/update documentation
- Reason clarification needed

**Update Process:**

```
Step 1: Find Absence Record
        ↓
Step 2: Click "Edit"
        ↓
Step 3: Modify Details
        ├─ Change dates
        ├─ Update reason
        ├─ Change absence type
        └─ Upload new document
        ↓
Step 4: Save Changes
        ↓
Step 5: Confirmation
        "Record updated successfully"
```

**Limitation:**
- Can only edit records with "Pending" or "Rejected" status
- Approved records locked for audit trail

---

### 8. **Absence History and Records**

**What it does:**
- Maintain complete absence history for each soldier
- Track all absence applications and decisions
- Audit trail with timestamps
- Historical data for analysis

**Information Recorded:**
- Soldier name and ID
- Absence type
- Start and end dates
- Duration (days/hours absent)
- Reason provided
- Status (Approved/Rejected/Pending)
- Approval/rejection date
- Approval/rejection reason (if rejected)
- Uploaded documents
- Created and updated timestamps

**Uses:**
- Performance evaluation
- Disciplinary action support
- Soldier record keeping
- Compliance reporting
- Promotion consideration
- Service record completeness

---

### 9. **Absence Deletion**

**What it does:**
- Remove absence records from system
- Typically for incorrect submissions
- Delete associated documents
- Clean up erroneous entries

**When to use:**
- Submitted by mistake
- Duplicate entry created
- Wrong soldier selected
- System cleanup

**Deletion Process:**

```
Step 1: Find Absence Record
        ↓
Step 2: Click "Delete"
        ↓
Step 3: Confirm Deletion
        (Warning: Cannot undo)
        ↓
Step 4: Associated files deleted
        ↓
Step 5: Confirmation
        "Record deleted successfully"
```

**Warning:**
- Deletion is permanent
- Cannot recover deleted records
- Better to reject than delete for audit trail

---

## Conditions and Business Rules

### **Absence Application Rules**

| Rule | Description |
|------|-------------|
| **Soldier Must Exist** | Soldier must be in system and active |
| **Type Must Be Active** | Only active absence types can be used |
| **Start Date Required** | Must specify when absence began |
| **End Date Allowed** | Optional, leave empty if single day |
| **Reason Required** | Reason for absence must be documented |
| **Document Optional** | Hard copy not required, recommended |
| **No Duplicates** | Cannot create duplicate of same absence |
| **Status Starts as Pending** | All new applications start in "Pending" status |

---

### **Approval/Rejection Rules**

| Rule | Description |
|------|-------------|
| **Only Pending Can Be Changed** | Pending status can be changed to Approved/Rejected |
| **Rejection Reason Required** | Must explain why absence rejected |
| **Date Locked** | Approval/rejection date automatically set |
| **Timestamp Recorded** | When decision made is logged |
| **Cannot Reversal** | Once approved/rejected, cannot easily undo |

---

### **Document Upload Rules**

| Rule | Description |
|------|-------------|
| **File Size Limited** | Reasonable size limit to prevent abuse |
| **File Type Limited** | Images, PDFs, or documents only |
| **One File Per Record** | Can replace but not add multiple files |
| **Auto Delete on Replace** | Old file deleted when new one uploaded |
| **Public Storage** | Accessible to authorized users |

---

## User Workflows

### **Workflow 1: Submit New Absence**

```
Step 1: Navigate to Absence Management
        ↓
Step 2: Click "Submit New Absence Application"
        ↓
Step 3: Choose Absence Type
        - AWOL
        - Medical
        - Unauthorized Leave
        - Other
        ↓
Step 4: Select Soldier(s)
        - Single soldier: Click name in dropdown
        - Multiple: Check boxes for each soldier
        ↓
Step 5: Enter Dates
        - Start Date: When absence began
        - End Date: When absence ended (if multi-day)
        ↓
Step 6: Provide Reason
        "Soldier was unauthorized absent during duty shift"
        ↓
Step 7: Upload Document (Optional)
        - Click "Choose File"
        - Select image/document
        - System stores file
        ↓
Step 8: Submit
        - Click "Submit Application"
        - Application created with "Pending" status
        - Confirmation message shown
```

---

### **Workflow 2: Review and Approve Absence**

```
Step 1: View Absence List
        ↓
Step 2: Find Target Record
        - Look for "Pending" status
        - Check soldier name and dates
        ↓
Step 3: Click Record to View Details
        ├─ Soldier information
        ├─ Absence dates
        ├─ Reason provided
        ├─ Document (if uploaded)
        └─ Current status
        ↓
Step 4: Review Information
        - Verify soldier identity
        - Check dates make sense
        - Review reason
        - Download and check document
        ↓
Step 5: Make Decision
        
        Option A: APPROVE
        ├─ Click "Approve Button"
        ├─ Status changes to "Approved"
        ├─ Timestamp recorded
        └─ Record locked (cannot edit)
        
        Option B: REJECT
        ├─ Click "Reject Button"
        ├─ Enter rejection reason in text box
        ├─ Click "Confirm Rejection"
        ├─ Status changes to "Rejected"
        ├─ Reason saved
        └─ Soldier can modify and resubmit
        
        ↓
Step 6: Notification
        "Status updated successfully"
```

---

### **Workflow 3: Edit Rejected Absence**

```
Step 1: Find Absence Record
        - Status: Rejected
        - Contains rejection reason
        ↓
Step 2: Click "Edit"
        ↓
Step 3: Review Changes Needed
        - Check rejection reason
        - Determine what to fix
        ↓
Step 4: Modify Information
        - Change dates if incorrect
        - Update reason with more detail
        - Change absence type
        - Upload new document
        - Remove previous document
        ↓
Step 5: Save Changes
        - Click "Update Application"
        - Record updated
        - Status remains "Rejected" until resubmitted
        ↓
Step 6: Resubmit for Approval
        - Manager reviews updated information
        - Can now approve (changes addressed)
        - Or reject again (still not satisfactory)
```

---

### **Workflow 4: Search Absence Records**

```
Step 1: Go to Absence List
        ↓
Step 2: Use Filter Options
        └─ Enter search criteria:
           - From Date: Jan 1, 2026
           - To Date: Jan 31, 2026
           - Absence Type: AWOL
           - Status: Approved
           - Soldier: SGT Ahmed
           ↓
Step 3: Click "Apply Filters"
        ↓
Step 4: Results Displayed
        - All matching records shown
        - Can sort by column
        - Can export results
        ↓
Step 5: View Specific Record
        - Click on record to see full details
        - Download document if attached
        - Edit or delete if needed
```

---

### **Workflow 5: Bulk Submit Absence for Multiple Soldiers**

```
Step 1: Navigate to Submit Absence
        ↓
Step 2: Select Absence Type
        "AWOL" for all soldiers involved
        ↓
Step 3: Choose Multiple Soldiers
        - Click checkbox for each soldier
        - Or use "Select All" button
        - Shows count of selected
        ↓
Step 4: Set Common Details
        - Start Date (same for all)
        - End Date (same for all)
        - Reason (same for all)
        ↓
Step 5: Upload Supporting Document
        (Applies to all soldier records)
        ↓
Step 6: Submit All
        - Click "Submit for All Selected"
        - System creates individual record for each
        - Confirmation shows: "Submitted for 15 soldiers"
        ↓
Step 7: Each Gets Own Record
        - Each soldier has individual entry
        - Can be approved/rejected separately
        - All share same document
```

---

## System Behavior & Automation

### **What Happens When Absence Is Submitted?**

1. **Application Created** with "Pending" status
2. **Timestamp Recorded** - Submission date/time noted
3. **File Uploaded** - If document provided, stored in system
4. **Status Set** - Initially "Pending" awaiting review
5. **Record Visible** - Appears in absence list for managers
6. **Notification** - Appropriate staff alerted of new submission

---

### **What Happens When Absence Is Approved?**

1. **Status Changes** to "Approved"
2. **Approval Date Recorded** - When approved noted
3. **Record Locked** - Cannot be edited further
4. **Moves to History** - Part of soldier's permanent record
5. **Affects Duty Assignment** - May exclude from duty during period
6. **Included in Reports** - Shows in absence statistics

---

### **What Happens When Absence Is Rejected?**

1. **Status Changes** to "Rejected"
2. **Reason Recorded** - Why rejection explanation saved
3. **Rejection Date Noted** - When rejected timestamp
4. **Returned to Soldier** - Can be edited and resubmitted
5. **Soft Locked** - Cannot be approved, can only be edited or deleted
6. **Not in Active Records** - Not considered official absence

---

## Quick Reference Guide

| Need to...? | What to Do |
|-------------|-----------|
| Create absence type | Settings → Absence Types → Create |
| Submit absence | Absence List → Submit New → Fill form |
| Approve absence | Absence List → Find pending → Approve |
| Reject absence | Absence List → Find pending → Reject → Add reason |
| View absence history | Absence List → Search soldier name |
| Edit pending absence | Absence List → Find record → Edit |
| Upload document | Submit form → Choose file button |
| Search absences | Use filter → Set criteria → Apply |
| Delete absence | Find record → Delete button (confirm) |
| Export report | Use filters → Export button |

---

## Status Indicators

```
🟡 PENDING     - Waiting for approval decision
✅ APPROVED    - Official absence recorded
❌ REJECTED    - Denied, can be modified and resubmitted
```

---

## Screenshots Placeholders

### [Screenshot 1: Absence List Dashboard]
Shows all absence applications with status indicators and action buttons.

---

### [Screenshot 2: Submit Absence Form]
Shows form to select absence type, soldier, dates, reason, and document upload.

---

### [Screenshot 3: Absence Details View]
Shows full absence record with all information and approval/rejection options.

---

### [Screenshot 4: Approval/Rejection Modal]
Shows dialog to approve or reject with reason field.

---

### [Screenshot 5: Filter and Search Interface]
Shows filter options for date range, status, type, and soldier search.

---

### [Screenshot 6: Document Upload Area]
Shows file upload widget and document preview.

---

### [Screenshot 7: Bulk Submission Selection]
Shows multiple soldier selection for batch absence submission.

---

### [Screenshot 8: Absence History for Soldier]
Shows all absences related to specific soldier over time.

---

## Common Use Cases

### **Use Case 1: Recording AWOL (Absent Without Leave)**

1. Officer discovers soldier AWOL during duty
2. Document the dates and time period
3. Submit Absence Application with AWOL type
4. Upload any evidence or incident report
5. Manager reviews and approves
6. Record becomes part of soldier's file

---

### **Use Case 2: Medical Emergency Absence Documentation**

1. Soldier calls in - medical emergency
2. Later provides medical certificate
3. Submit Absence Application with Medical type
4. Upload medical certificate as document
5. Manager approves with supporting document
6. Noted as legitimate absence in records

---

### **Use Case 3: Unauthorized Leave Discovery**

1. Soldier missed multiple duty shifts
2. Later found to have been on unauthorized leave
3. Submit Absence Application with date range
4. Include reason discovered
5. Manager reviews circumstances
6. Approve or reject based on policy

---

### **Use Case 4: Administrative Error Correction**

1. Absence submitted with wrong dates
2. Soldier or clerk identifies error
3. Rejection issued explaining error
4. Record edited with correct information
5. Resubmitted for approval
6. Corrected version approved

---

## Best Practices

✓ **Do:**
- Document absence immediately when discovered
- Provide clear, specific reasons
- Upload supporting documents when available
- Review absences regularly
- Keep records organized
- Make timely approval/rejection decisions
- Include rejection reasons for clarity

✗ **Don't:**
- Leave absences in "Pending" indefinitely
- Delete records without good reason
- Approve without reviewing details
- Leave reason blank when submitting
- Ignore uploaded documents
- Forget to update documented absences
- Create duplicate entries

---

## Frequently Asked Questions

### **Q: Can multiple soldiers have same absence on same date?**
A: Yes, each soldier gets individual record but can have same dates and reason.

---

### **Q: What happens if absence overlaps with another?**
A: System allows it - each is separate record. Other modules (duty assignment) handle conflict detection.

---

### **Q: Can rejected absence be resubmitted?**
A: Yes, can be edited and saved. Will go back to "Pending" for re-review.

---

### **Q: Is document upload required?**
A: No, but recommended for supporting evidence, especially medical or emergency absences.

---

### **Q: What file types can be uploaded?**
A: Images (JPG, PNG), PDFs, and document formats typically accepted depending on system configuration.

---

### **Q: How long are approved absences kept?**
A: Indefinitely - part of permanent soldier record. Check retention policies.

---

### **Q: Can approved absence be changed?**
A: No, approved records are locked. Can only delete and resubmit if change needed.

---

### **Q: Who can approve absences?**
A: Managers and authorized supervisory staff (depends on role configuration).

---

### **Q: Can soldier view their own absence records?**
A: Depends on role permissions - usually yes, soldiers can view their history.

---

### **Q: How is absence tracked during duty assignment?**
A: If approved, soldier potentially excluded from roster duty during absence period.

---

## Troubleshooting

### **Issue: Cannot upload document**

**Possible Causes:**
- File too large
- Wrong file type
- Storage permission issue

**Solutions:**
- Try smaller file
- Use supported format (JPG, PNG, PDF)
- Contact administrator

---

### **Issue: Absence not appearing in list**

**Possible Causes:**
- Wrong filter applied
- Status not visible in current filter
- Record deleted

**Solutions:**
- Clear all filters
- Check if "Pending" is in filter
- Search by soldier name

---

### **Issue: Cannot approve/reject absence**

**Possible Causes:**
- Status already set (not Pending)
- Permission issue
- System error

**Solutions:**
- Verify status is "Pending"
- Check user role/permissions
- Try reloading page

---

### **Issue: Error deleting absence**

**Possible Causes:**
- Associated file not accessible
- Database constraint
- Permission issue

**Solutions:**
- Remove document first
- Try again after delay
- Contact administrator

---

## Additional Resources

- [Settings Page Guide](SETTINGS_PAGE_GUIDE.md) - Configure absence types
- [Duty Module Guide](DUTY_MODULE_GUIDE.md) - Duty assignments affected by absence
- [Soldier Management Guide](SOLDIER_MANAGEMENT_GUIDE.md) - Soldier records and status

---

## Support

For issues or questions:
1. Check FAQ section above
2. Review troubleshooting guide
3. Review step-by-step workflows
4. Contact your system administrator

