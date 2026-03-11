# Leave Management Module Documentation

## Module Overview

The **Leave Management Module** is the comprehensive system for handling all leave-related activities in your military organization. It enables:

- **Soldiers to request leave** with detailed information about dates and reasons
- **Administrators to review and approve/reject** leave applications
- **Automatic status tracking** for soldiers on approved leave
- **Leave type management** (Annual, Sick, Emergency, etc.)
- **Absence tracking** for attendance records
- **Leave history** for auditing and reporting
- **Bulk approval operations** for processing multiple requests efficiently

This module ensures fair leave distribution, maintains accurate personnel status, and keeps attendance records current.

---

## Key Features

### 1. **Leave Application Submission**

**What it does:**
- Soldiers can submit formal requests for leave
- Multiple leave types supported (Annual, Sick, Casual, Compassionate, etc.)
- Detailed date selection
- Reason documentation
- Attachment capability (medical certificates for sick leave, etc.)

**Information Required:**
- Leave type (chosen from predefined list)
- Start date and end date
- Number of days
- Reason for leave
- Contact details during absence
- Optional attachments/documents

**How it Works:**
1. Soldier selects "Request Leave"
2. Fills in leave details
3. Provides reason
4. Submits for approval
5. Status becomes "Pending" awaiting review

**Who Can Request:**
- All soldiers with user account access
- Can only request leave as themselves
- Cannot request leave for other soldiers

---

### 2. **Leave Type Configuration**

**What it does:**
Settings administrators can define what types of leave are available in the system. Each type has specific rules and quotas.

**Leave Types Include:**
- **Annual Leave** - Yearly vacation entitlement
- **Sick Leave** - Medical/illness absence
- **Casual Leave** - Short notice personal leave
- **Compassionate Leave** - For emergencies (death, illness of family)
- **Study Leave** - For professional development
- **Unpaid Leave** - Without pay (if allowed)
- **Other Types** - Custom leave types for your organization

**For Each Leave Type, You Can Set:**
- Leave name
- Number of days available per year
- Whether it requires supporting documents
- Whether advance notice is required
- Whether it can be carried over to next year

**Benefits:**
- Consistent leave policies
- Clear entitlements
- Automatic quota tracking (if implemented)

---

### 3. **Leave Application Review and Approval**

**What it does:**
- Administrators receive leave requests
- Review pending applications
- Approve or reject requests
- Add comments/reasons for decision
- Process approvals in bulk

**Approval Workflow:**

```
Soldier Submits → Pending Review → Admin Reviews → Approved/Rejected → Soldier Notified
                                                         ↓
                                              Soldier Status Updated
                                          (if Approved → On Leave)
```

**What Happens During Review:**
1. View soldier's leave history
2. Check availability (soldiers may have quota)
3. Check operational needs (can soldier be spared?)
4. Review supporting documents
5. Make approval decision
6. Add comments if rejecting

**Approval Status Options:**
- **Approved** - Leave is granted, soldier will be marked "On Leave" during period
- **Rejected** - Leave denied, soldier remains available for duties
- **Pending** - Awaiting review
- **Cancelled** - Leave previously approved but now cancelled

---

### 4. **Bulk Leave Approval Operations**

**What it does:**
- Process multiple leave requests at once
- Save time when reviewing large batches
- Apply same decision to multiple applicants
- Bulk update status in one action

**Bulk Operations:**
- **Bulk Approve** - Approve multiple pending requests
- **Bulk Reject** - Reject multiple requests with single reason
- **Bulk Cancel** - Cancel previously approved leave
- **Bulk Status Update** - Change status of many leaves at once

**When to use:**
- Processing end-of-month leave requests
- Approving similar leave requests (e.g., all casual leave from same date)
- Rejecting leaves due to operational emergency
- Mass cancellation if mission changed

---

### 5. **Leave Status Tracking**

**What it does:**
The system automatically manages soldier status based on leave approval.

**Status Flow:**

```
Active (Default)
    ↓
On Leave (when approved leave starts)
    ↓
Active (when leave ends)
```

**Automatic Status Changes:**
- **When leave is APPROVED** → Soldier status changes to "On Leave" on start date
- **When leave ENDS** → Soldier status automatically returns to "Active"
- **When leave is REJECTED** → Soldier remains "Active", unchanged
- **When leave is CANCELLED** → Soldier returns to "Active" immediately

**How This Affects the System:**
- Soldiers on leave cannot be assigned to duties
- Duty rosters automatically exclude on-leave soldiers
- Attendance marks them as "On Leave" for those dates
- Leave status is visible in soldier's profile

---

### 6. **Leave History and Records**

**What it does:**
- Maintains complete record of all leave taken
- Tracks past, current, and future leave
- Provides audit trail for compliance
- Shows leave trends and patterns

**Information Recorded:**
- Leave type taken
- Dates of leave (start and end)
- Number of days
- Reason provided
- Approval status and approve/reject date
- Approver name
- Comments/notes
- Any attachments

**Benefits:**
- Historical reference for soldiers
- Compliance documentation
- Leave quota tracking (if tracked)
- Detection of leave abuse patterns
- Fair leave distribution verification

---

### 7. **Search and Filter Leave Applications**

**What it does:**
- Find specific leave applications quickly
- Filter by soldier, date, status, or type
- View pending, approved, or rejected leaves
- Search with multiple criteria

**Filter Options:**
- **Soldier Name/Number** - Find a specific person's leave
- **Date Range** - Leave between specific dates
- **Leave Type** - Only certain types (Annual, Sick, etc.)
- **Status** - Pending, Approved, Rejected
- **Approval Status** - By person who approved

**Search Features:**
- Real-time text search
- Multiple filter combinations
- Save filter preferences
- Paginated results

---

### 8. **Absence Type Configuration**

**What it does:**
Define what absence categories are used in attendance tracking. When soldiers are on approved leave, their attendance is marked with an absence type.

**Absence Types Include:**
- **On Leave** - Approved leave period
- **Present** - Attended all sessions
- **Absent** - Unauthorized absence
- **Medical** - Medical/health-related
- **On Duty** - Assigned to duty elsewhere
- **AWOL** - Absent Without Leave
- **Other Types** - As defined by your organization

**How They Connect:**
- Leave on Leave → Attendance marked as "On Leave"
- Soldier missing without leave → Attendance marked as "Absent" or "AWOL"
- Soldier assigned to duty → Attendance marked as "On Duty"

---

### 9. **Leave Application Update and Modification**

**What it does:**
- Soldiers or admins can modify pending leave applications
- Change dates, reason, or type before approval
- Cannot modify approved or rejected applications

**What Can Be Changed:**
- Start date
- End date
- Leave type (within same application)
- Reason for leave
- Attachments

**Limitations:**
- Cannot change approved/rejected leaves
- Changes to pending applications restart approval queue
- Date changes must not conflict with duties

---

### 10. **Leave Application Deletion**

**What it does:**
- Remove leave applications from system
- Typically only for pending leaves
- Approved/rejected applications kept for history

**When to use:**
- Candidate accidentally submitted duplicate
- Soldier withdrawn leave request
- Incorrect leave application submitted

**What Happens:**
- Application is permanently removed
- Never appears in history
- Other applications unaffected

---

## Conditions and Business Rules

### **Rules for Submitting Leave**

| Rule | Description |
|------|-------------|
| **Soldier Must Be Active** | Only active soldiers can request leave (not on leave already) |
| **Valid Date Range** | Start date must be before end date |
| **Leave Type Must Exist** | Cannot select leave type not defined in system |
| **Reason Required** | Must provide reason for leave |
| **Future or Current Leave** | Cannot request leave for past dates |
| **One Application Per Period** | Cannot overlap leave periods for same soldier |

---

### **Rules for Approving Leave**

| Rule | Description |
|------|-------------|
| **Must Have Permission** | Only administrators can approve leave |
| **Status Automatic Change** | Approving leave auto-sets soldier to "On Leave" |
| **Date Validation** | Approved leave dates must be valid |
| **Conflict Check** | Cannot approve leave if soldier has duty conflict |
| **Reason for Rejection** | Rejecting requires documented reason |
| **Approval is Trackable** | Approver name and date recorded |

---

### **Rules for Leave Status**

| Rule | Description |
|------|-------------|
| **On Leave → Active Auto-Revert** | Soldier automatically becomes "Active" when leave ends |
| **On Leave Blocks Duties** | Soldiers on leave cannot be assigned new duties |
| **Leave Dates Honored** | Absence marks show "On Leave" during leave period |
| **Cancellation Reverts Status** | Cancelling approved leave makes soldier "Active" again |
| **No Overlapping Leaves** | Soldier cannot have two overlapping leave periods |

---

### **Rules for Leave History**

| Rule | Description |
|------|-------------|
| **Permanent Records** | Leave applications are kept permanently for audit |
| **Cannot Delete Approved** | Approved/rejected applications cannot be deleted |
| **Can Reject Pending** | Pending applications can be deleted/rejected |
| **History Visible** | Soldiers can view their leave history anytime |
| **Audit Trail Maintained** | All approvals/rejections tracked with user/date |

---

## User Workflows

### **Workflow 1: Soldier Requesting Leave**

```
Step 1: Soldier Logs Into System
        ↓
Step 2: Navigate to Leave Management → Request Leave
        ↓
Step 3: Select Leave Type
        - Choose from available types (Annual, Sick, Casual, etc.)
        - System shows current status/quota if applicable
        ↓
Step 4: Enter Dates
        - Click start date on calendar
        - Click end date on calendar
        - System shows number of days
        ↓
Step 5: Provide Reason
        - Type reason for leave
        - Add details
        ↓
Step 6: Optional - Attach Documents
        - For sick leave: attach medical certificate
        - For compassionate: attach relevant documents
        ↓
Step 7: Review Submitted Details
        - Verify all information is correct
        ↓
Step 8: Submit Application
        - Click "Submit"
        - Confirmation message shown
        - Status: "Pending" (awaiting approval)
        ↓
Soldier Waits for Approval
        - Application sent to administrators
        - Soldier can check status anytime
        - Notification when decision made
```

---

### **Workflow 2: Administrator Reviewing Leave Requests**

```
Step 1: Admin Logs Into System
        ↓
Step 2: Navigate to Leave Management → Pending Approvals
        ↓
Step 3: Review Pending Requests
        - List of all applications awaiting review
        - Shows soldier name, type, dates, reason
        ↓
Step 4: Click Request to View Details
        - Soldier information
        - Leave reason
        - Attached documents (if any)
        - Soldier's leave history
        - Current duty assignments
        ↓
Step 5: Make Decision
        
        Option A: APPROVE
        ────────────────
        - Verify no duty conflicts
        - Check operational requirements
        - Click "Approve"
        - Confirm approval
        ↓
        Option B: REJECT
        ───────────────
        - Identify reason (operational need, policy violation, etc.)
        - Click "Reject"
        - Enter reason/comments
        - Confirm rejection
        ↓
Step 6: Decision Recorded
        - Status updated (Approved/Rejected)
        - Soldier notified
        - If approved:
          * Soldier status becomes "On Leave" on start date
          * Automatically removed from duty roster
          * Attendance marked "On Leave" for dates
        - If rejected:
          * Soldier remains "Active"
          * Can request different dates
```

---

### **Workflow 3: Bulk Approving Leave Applications**

```
Step 1: Admin Views Pending Leave List
        ↓
Step 2: Select Multiple Applications
        - Click checkboxes next to requests
        - Or select "Select All Pending"
        ↓
Step 3: Click "Bulk Actions" Menu
        ↓
Step 4: Choose Action
        - Approve All Selected
        - Reject All Selected
        ↓
Step 5: From Date to Date (if needed)
        - Specify date range if filtering
        - Confirm selection
        ↓
Step 6: System Processes
        - Approves all selected requests
        - Creates leave records
        - Updates soldier statuses to "On Leave"
        - Removes from duty rosters
        - Sends notifications
        ↓
Step 7: Confirmation Summary
        - Shows "X applications approved"
        - All actions completed
```

---

### **Workflow 4: Soldier On Leave**

```
When Soldier Goes On Approved Leave:
─────────────────────────────────

Step 1: Leave Start Date Arrives
        ↓
Step 2: System Automatically
        - Changes soldier status to "On Leave"
        - Marks attendance as "On Leave"
        - Removes from active duty roster
        ↓
During Leave Period:
        - Soldier is not visible in duty assignments
        - Cannot be assigned new duties
        - Attendance marked "On Leave"
        - Contact by emergency only
        ↓
Step 3: Leave End Date Arrives
        ↓
Step 4: System Automatically
        - Changes soldier status back to "Active"
        - Marks next day attendance as "Present" (if worked)
        - Can be assigned to duties again
        ↓
Soldier Returns to Work
        - Resume normal duties
        - Status restored
        - Available for assignment
```

---

### **Workflow 5: Cancelling Approved Leave**

```
Scenario: Operational emergency, need soldier back immediately

Step 1: Admin Accesses Leave Management
        ↓
Step 2: Find Approved Leave Application
        - Search by soldier name/date
        ↓
Step 3: Click on Application
        ↓
Step 4: Click "Cancel Leave" button
        ↓
Step 5: Enter Reason for Cancellation
        - "Operational requirement"
        - "Mission change"
        - etc.
        ↓
Step 6: Confirm Cancellation
        ↓
Step 7: System Updates:
        - Leave status becomes "Cancelled"
        - Soldier status immediately becomes "Active"
        - Can be assigned to duties immediately
        - Soldier notified of cancellation
```

---

### **Workflow 6: Modifying Pending Leave Application**

```
Scenario: Soldier wants to change dates for pending leave

From Soldier Side:
──────────────────
Step 1: Soldier views submitted application (status: Pending)
        ↓
Step 2: Click "Edit" button
        ↓
Step 3: Change Dates
        - Select new start date
        - Select new end date
        ↓
Step 4: Review Changes
        - Confirm new dates
        ↓
Step 5: Save Changes
        ↓
Step 6: Application Updated
        - Remains in "Pending" status
        - Admin reviews updated application
        - Same approval process follows

From Admin Side:
────────────────
- Can also modify pending applications
- Same process as above
```

---

## System Behavior & Automation

### **What Happens When Leave Is Approved?**

1. **Status Change** - Application marked as "Approved"
2. **Soldier Status Change** - Soldier set to "On Leave" starting on leave start date
3. **Duty Removal** - Soldier removed from any duties scheduled during leave
4. **Attendance Mark** - Days marked as "On Leave" in attendance
5. **Notification Sent** - Soldier notified of approval
6. **Record Created** - Leave entry added to soldier's history

---

### **What Happens When Leave Dates Arrive?**

1. **Start Date** - System automatically marks soldier "On Leave"
2. **During Leave** - Attendance shows "On Leave" for each day
3. **End Date** - System automatically marks soldier "Active" again
4. **Back to Duty** - Can be assigned to future duties
5. **Attendance Updates** - Automatically updated each day

---

### **What Happens When Leave Is Rejected?**

1. **Status** - Application marked as "Rejected"
2. **Soldier Status** - Remains "Active", unchanged
3. **Duty Impact** - No impact on duties (soldier remains available)
4. **Notification** - Soldier informed of rejection and reason
5. **History** - Kept in system for record/audit
6. **Next Step** - Soldier can submit new application for different dates

---

### **What Happens When Approved Leave Is Cancelled?**

1. **Status** - Leave marked as "Cancelled"
2. **Soldier Status** - Immediately changes to "Active"
3. **Duties** - Can be reassigned to duties immediately
4. **Attendance** - Cancelled leave period no longer marked "On Leave"
5. **Notification** - Soldier informed of cancellation and reason
6. **History** - Cancellation recorded with date/reason

---

## Leave Status Explained

```
PENDING → Awaiting admin review
             ↓
        ┌─────┴─────┐
        ↓           ↓
     APPROVED    REJECTED
        ↓           ↓
   On Leave    Remains Active
   (↓)         (Can request again)
   Auto-Active when ends
        
Can also be:
CANCELLED - Approved leave cancelled before it starts/during leave
```

---

## Important Dates & Automatic Changes

| Event | What Happens |
|-------|--------------|
| Leave submitted | Status: Pending |
| Leave approved | Status: Approved, Soldier marked "On Leave" starting next day |
| Leave starts | Attendance marked "On Leave", duties cannot assign |
| Leave ends (auto) | Soldier auto-reverts to "Active", available for duty |
| Leave rejected | Status: Rejected, soldier remains "Active" unchanged |
| Leave cancelled | Status: Cancelled, soldier immediately "Active" again |

---

## Quick Reference Guide

| Need to...? | Where to Go |
|-------------|-----------|
| Request leave | Leave Management → Request Leave |
| Check pending requests | Admin: Leave Management → Pending Approvals |
| View my leave history | My Profile → Leave History |
| Approve/Reject leave | Leave Management → Pending List → Select → Approve/Reject |
| Bulk approve | Pending List → Select Multiple → Bulk Actions → Approve |
| Modify pending application | Click application → Edit (if still pending) |
| Cancel approved leave | Approved Leave → Click → Cancel |
| Export leave records | Leave Management → Export |
| View soldier's leave status | Soldier Profile → Leave History |

---

## Business Rules Summary

### **Soldier Requesting Leave Must:**
- Be an active soldier
- Have a user account
- Select valid leave type
- Enter future or current dates
- Provide reason
- Not have overlapping leave

### **Admin Approving Leave Must:**
- Have admin permission
- Review soldier availability
- Check for duty conflicts
- Document reason if rejecting
- Maintain approval record
- Notify soldier of decision

### **Automated System Actions:**
- Auto-sets soldier "On Leave" when leave starts
- Auto-reverts soldier "Active" when leave ends
- Auto-marks attendance "On Leave" for duration
- Auto-removes from duty roster when approved
- Auto-updates status after cancellation

---

## Screenshots Placeholders

### [Screenshot 1: Leave Management Dashboard]
Shows pending requests, approved, rejected, and action buttons.

---

### [Screenshot 2: Leave Application Form]
Shows form where soldier provides leave details and dates.

---

### [Screenshot 3: Soldier's Leave Request - Pending]
Shows submitted leave with status "Pending" and confirmation message.

---

### [Screenshot 4: Admin Approval List]
Shows pending applications ready for admin review.

---

### [Screenshot 5: Leave Application Detail View]
Shows full details, soldier info, dates, reason, and approve/reject buttons.

---

### [Screenshot 6: Leave Approval Confirmation]
Shows successful approval with status change to "On Leave."

---

### [Screenshot 7: Leave Rejection with Reason]
Shows rejection reason capture dialog.

---

### [Screenshot 8: Bulk Actions Menu]
Shows checkboxes for multiple selection and bulk approve/reject.

---

### [Screenshot 9: Soldier's Leave History]
Shows past and upcoming leave records in timeline view.

---

### [Screenshot 10: Filter and Search Interface]
Shows leave filter options by date, soldier, type, and status.

---

## Common Use Cases

### **Use Case 1: Annual Leave Request**

1. Soldier plans vacation
2. Requests Annual Leave for specific dates
3. Admin reviews operational schedule
4. Approves leave
5. Soldier receives confirmation
6. On leave dates, automatically marked "On Leave"
7. Returns to work after dates end

---

### **Use Case 2: Sick Leave Request**

1. Soldier falls ill
2. Submits Sick Leave request with medical certificate
3. Admin quickly approves (medical evidence provided)
4. Soldier status becomes "On Leave"
5. Days marked as "On Leave" with medical reason
6. When recovered, medical status updated
7. Marked "Active" again when medical fits

---

### **Use Case 3: Emergency Leave Cancellation**

1. Approved leave already in effect
2. Operational emergency occurs
3. Admin cancels soldier's leave
4. Soldier immediately becomes "Active"
5. Assigned to critical duty
6. Previously "On Leave" status removed

---

### **Use Case 4: Batch Year-End Leave Processing**

1. Many soldiers submit leave for month-end
2. Admin opens Leave Management
3. Filters for pending annual leave
4. Selects all for December
5. Bulk approves all suitable requests
6. Soldiers notified automatically
7. All statuses updated at once

---

## Frequently Asked Questions

### **Q: Can I request leave for past dates?**
A: No, leave must be for current or future dates. Absences from past are handled differently (marked as AWOL or manually adjusted).

---

### **Q: What if I take sick leave without requesting first?**
A: Th can submit retroactive leave request, but it depends on your organization's policy and whether admin approves it.

---

### **Q: Can I cancel my own leave request?**
A: If still pending admin review, typically yes. If already approved, usually only admin can cancel.

---

### **Q: What happens if leave is rejected?**
A: You remain marked "Active". You can request different dates or follow up to understand why it was rejected.

---

### **Q: When exactly does my status change to "On Leave"?**
A: Status changes to "On Leave" on the first day of your approved leave period (start date).

---

### **Q: Do I automatically return to "Active" after leave ends?**
A: Yes, the system automatically changes your status back to "Active" the day after your leave ends.

---

### **Q: Can I modify my leave request after submitting?**
A: Only if it's still pending admin review. Once approved or rejected, it cannot be modified.

---

### **Q: Is my leave history visible to everyone?**
A: Typically, soldiers can only see their own history. Admins can see all leave records for audit purposes.

---

## Troubleshooting

### **Issue: Cannot submit leave request**
- Verify you have active status (not already on leave)
- Check if you have user account access
- Ensure start date is before end date
- Verify you've selected a leave type

---

### **Issue: Leave application stuck "Pending"**
- Admin may be reviewing - check back later
- Verify admin has access (check permissions)
- Contact admin directly for status

---

### **Issue: Soldier showing "On Leave" but no leave request exists**
- Manually created by admin for attendance reasons
- May have been imported from another system
- Check leave history to see details

---

### **Issue: Cannot cancel approved leave**
- May not have admin permission (soldiers cannot cancel approved)
- Contact admin to process cancellation
- Ensure leave hasn't already ended

---

### **Issue: Bulk approval showing errors**
- Some applications may have conflicts
- Approve individually to see specific issue
- Contact admin for assistance

---

## Best Practices

✓ **Do:**
- Submit leave requests with adequate advance notice
- Provide clear reason for leave
- Include medical certificates for sick leave
- Update leave status changes promptly
- Keep leave records organized for auditing
- Review historical leave patterns
- Maintain fair leave distribution

✗ **Don't:**
- Submit overlapping leave requests
- Submigrating urgent leave at last minute (when possible)
- Forget to update medical status when returning from sick leave
- Delete approved leave without good reason
- Ignore leave conflict notifications
- Assign soldiers on approved leave to duties

---

## Additional Resources

- [Settings Page Guide](SETTINGS_PAGE_GUIDE.md) - Configure leave types
- [Soldier Management Guide](SOLDIER_MANAGEMENT_GUIDE.md) - Manage soldier status
- [Duty Module Guide](DUTY_MODULE_GUIDE.md) - Duty conflict management
- [Database Schema](DATABASE_SCHEMA.md) - Technical database structure

---

## Support

For issues or questions:
1. Check the Troubleshooting section above
2. Review FAQ section
3. Contact your system administrator

