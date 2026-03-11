# Duty Assignments - Advanced Technical Guide

## Overview

The **Duty Assignments Module** is the most complex part of the Manpower Management System. It handles the automatic and manual assignment of soldiers to duties while enforcing elaborate business rules, conflict detection, time overlap prevention, and fair rotation policies. This guide explains the advanced logic, algorithms, and decision-making processes.

---

## Core Concept: Smart Assignment Algorithm

The duty assignment system operates using a **multi-layer filtering and validation system** that ensures:

1. **Soldier Eligibility** - Correct ranks, active status, available capacity
2. **Schedule Conflicts** - No time overlaps, sufficient breaks between duties
3. **Exclusion Rules** - Soldiers with training, leave, or other commitments are excluded
4. **Fair Rotation** - Soldiers don't get same duty back-to-back
5. **Load Balancing** - Duties distributed fairly among eligible soldiers

---

## Part 1: The Exclusion List - Who CANNOT Be Assigned

### Overview of Exclusions

The system builds an **exclusion list** before any assignment happens. This list contains all soldiers who are unavailable for roster duties on a given date.

```
EXCLUDED SOLDIERS (Cannot get assigned):
├─ Soldiers on LEAVE (approved, active for date)
├─ Soldiers in ACTIVE CADRE assignments
├─ Soldiers in ACTIVE COURSES
├─ Soldiers in ACTIVE EXERCISE AREAS
├─ Soldiers in ACTIVE SERVICES/APPOINTMENTS
├─ Soldiers with FIXED DUTY assignments
├─ Soldiers in COMMAND (CMD) role
└─ Soldiers in ANNUAL TRAINING (ATT)
```

### Detailed Exclusion Rules

#### **1. Leave Exclusion**

**Criteria:**
- Leave Application status = "Approved"
- Leave start date ≤ Assignment date ≤ Leave end date
- Leave must be currently active (not past, not future)

**Example:**
```
Soldier: SGT Ahmed
Leave Application: 
  - Start: January 10, 2026
  - End: January 15, 2026
  - Status: Approved

Result: Ahmed EXCLUDED from Jan 10-15 (cannot be assigned roster duties)

Timeline:
Jan 9:  Include (before leave starts)
Jan 10: EXCLUDE (leave started)
Jan 14: EXCLUDE (still on leave)
Jan 15: EXCLUDE (last day of leave)
Jan 16: Include (after leave ends)
```

---

#### **2. Cadre Exclusion**

**Criteria:**
- SoldierCadre record exists with status = "active"
- Start date ≤ Assignment date
- End date is NULL OR end date ≥ Assignment date

**Example:**
```
Soldier: CPL Maria is a CADRE (Instructor)
SoldierCadre Record:
  - Status: active
  - Start: February 1, 2026
  - End: NULL (indefinite)

Result: Maria EXCLUDED from Feb 1 onwards (exclusion continues until end_date)

Timeline:
Jan 31: Include (cadre hasn't started)
Feb 1:  EXCLUDE (cadre assignment active)
Dec 31: EXCLUDE (still active, no end date)
```

---

#### **3. Course Exclusion**

**Criteria:**
- SoldierCourse record with status = "active"
- Start date ≤ Assignment date
- End date is NULL OR end date ≥ Assignment date

**Example:**
```
Soldier: LCP Amir in training course
SoldierCourse:
  - Status: active
  - Start: March 1, 2026
  - End: March 31, 2026 (specific end date)

Result: Amir EXCLUDED from March 1-31

Timeline:
Feb 28: Include
Mar 1:  EXCLUDE (course started)
Mar 15: EXCLUDE (course in progress)
Mar 31: EXCLUDE (last day of course)
Apr 1:  Include (course completed)
```

---

#### **4. Exercise Area Exclusion**

**Criteria:**
- SoldierExArea record with status = "active"
- Start date ≤ Assignment date
- End date is NULL OR end date ≥ Assignment date

**Example:**
```
Soldier: PTE Rashid in exercise area
SoldierExArea:
  - Status: active
  - Start: April 5, 2026
  - End: April 10, 2026

Result: Rashid EXCLUDED from April 5-10
```

---

#### **5. Service/Appointment Exclusion**

**Criteria:**
- SoldierServices record with status = "active"
- Appointments from date ≤ Assignment date
- Appointments to date is NULL OR ≥ Assignment date

**Example:**
```
Soldier: SGT Hassan appointed to special service
SoldierServices:
  - Status: active
  - From Date: May 1, 2026
  - To Date: NULL (ongoing)

Result: Hassan EXCLUDED from May 1 onwards
```

---

#### **6. Fixed Duty Exclusion**

**Criteria:**
- DutyRank entry has duty_type = "fixed"
- Soldier ID is assigned to that duty rank
- Duty status = "Active"

**Important:** Fixed duties take priority over roster duties!

**Example:**
```
Duty: Gate Guard (Fixed)
DutyRank (Fixed):
  - Rank: Corporal
  - Soldier: CPL James
  - Duty Type: fixed

Result: James EXCLUDED from roster duty assignment
(He's permanently assigned to Gate Guard)

Implication:
- Every day: James assigned to Gate Guard
- Cannot be assigned to any other roster duty
- Assignment happens automatically (not part of auto process)
```

---

#### **7. CMD and ATT Exclusions**

**Criteria:**
- CMD (Command role): Soldier has active CMD record with no end date or end date ≥ assignment date
- ATT (Annual Training): Soldier in active training assignment

**Example:**
```
CMD Example:
Soldier: Major Ahmed as Training Officer
CMD Record:
  - Start: Jan 1, 2026
  - End: NULL

Result: Major EXCLUDED indefinitely (command duties take priority)

ATT Example:
Soldier: SGT Rashid in Annual Training
ATT Record:
  - Start: June 1, 2026
  - End: June 30, 2026

Result: Rashid EXCLUDED June 1-30
```

---

### Exclusion List Caching

The system **caches exclusion lists** to avoid repeated database queries:

```php
// Pseudocode
if (exclusionListExists[date]) {
    return cachedExclusionList
}

// Build list (expensive operation)
excludedList = [
    leaves + cadres + courses + exercise + services + fixed + cmd + att
]

// Cache for future use
exclusionCache[date] = excludedList

return excludedList
```

**Performance Impact:**
- First query for Jan 5: Query database (slow)
- Second query for Jan 5: Use cache (fast)
- Query for Jan 6: New cache entry (slow for first time)

---

## Part 2: Conflict Detection - Time Overlapping Logic

### Overview of Time Conflicts

Even if a soldier is not in the exclusion list, they cannot be assigned if they have **time conflicts** with existing duties.

```
TIME CONFLICT SCENARIOS:
├─ Direct overlap (same time period)
├─ Insufficient break between duties (less than 60 minutes)
├─ 24-hour duty conflict
└─ Multi-day duty overlap
```

### The Minimum Break Rule

**Rule:** Between any two duties, there must be at least **60 minutes of break time**.

**Calculation:**
```
Duty 1: 08:00 - 16:00 (8 hour duty)
Duty 2: 16:30 - 20:00 (4 hour duty)

Break = 16:30 - 16:00 = 30 minutes
Required = 60 minutes
Result: ❌ CONFLICT (30 < 60)

Solution: Duty 2 must start at 17:00 or later
Duty 2: 17:00 - 20:00
Break = 17:00 - 16:00 = 60 minutes
Result: ✅ ALLOWED (60 = 60)
```

### 24-Hour Duty Special Case

**What is a 24-hour duty?**

A duty where start time = end time is treated as a 24-hour duty:

```
24-Hour Duty Format:
- Start Time: 08:00
- End Time: 08:00
- System sees: Same start and end = 24-hour assignment

Interpretation:
- Soldier covers full 24 hours
- No part of day is free
- Blocks entire day from other assignments
```

**Conflict Rules for 24-Hour Duties:**

```
Table: 24-Hour Duty Conflicts

Scenario                          | Result
----------------------------------|--------
24-hour + any other duty same day | CONFLICT
24-hour + regular duty same day   | CONFLICT
24-hour + another 24-hour same day| CONFLICT
2x 24-hour on consecutive days    | ALLOWED (different days)
24-hour + 24-hour same day        | CONFLICT
```

**Example:**
```
Soldier: PTE Ali
Jan 5: Assigned 24-hour duty (08:00-08:00)
Jan 5: Request to assign regular duty (14:00-18:00)

Result: ❌ CONFLICT
Reason: 24-hour duty covers entire day, no time available

---

Jan 5: Assigned 24-hour duty
Jan 6: Assigned different regular duty (10:00-14:00)

Result: ✅ ALLOWED
Reason: Different dates, no overlap
```

---

### Time Overlap Detection Algorithm

**Scenario 1: Direct Time Overlap**

```
Check if time periods overlap:

New Duty:    ▓▓▓▓▓▓
             10:00  14:00

Existing:    ▓▓▓▓▓▓▓▓
             12:00  16:00

Overlap:     ▓▓▓▓
             12:00  14:00

Result: ❌ CONFLICT (2-hour overlap from 12:00-14:00)
```

**Logic:**
```
Duty A: 10:00 - 14:00
Duty B: 12:00 - 16:00

A starts before B (10:00 < 12:00) ✓
B starts before A ends (12:00 < 14:00) ✓
=> OVERLAP EXISTS
```

---

**Scenario 2: Time Conflict with Insufficient Break**

```
Existing Duty Ends:  16:00
Break:               ▓░░░░ (30 minutes)
New Duty Starts:     16:30

Result: ❌ CONFLICT (break < 60 minutes required)

---

Existing Duty Ends:  16:00
Break:               ▓▓▓▓▓▓ (60 minutes)
New Duty Starts:     17:00

Result: ✅ ALLOWED (break exactly 60 minutes)
```

---

**Scenario 3: Overnight/Next-Day Duties**

```
Scenario A: Overnight Duty
Duty A (Jan 10): 22:00 - 06:00 (spans into Jan 11 morning)
Duty B (Jan 11): 10:00 - 14:00

Result: ✅ ALLOWED (no overlap, sufficient break from 06:00 to 10:00)

---

Scenario B: Back-to-Back Overnight
Duty A (Jan 10): 22:00 - 06:00
Duty B (Jan 11): 00:00 - 06:00 (overlaps)

Result: ❌ CONFLICT (22:00-06:00 overlaps with 00:00-06:00)
        Both overlap from 00:00-06:00
```

---

### Overtime Duty Handling

**What if end time < start time?**

This indicates an overnight/next-day duty:

```
Format: 22:00 - 06:00 means:
- Start: 22:00 (10 PM)
- End: 06:00 (6 AM next day)
- Duration: 8 hours across two calendar days

System Automatically Adds One Day to End Time:
- Start: 22:00 (day 1)
- End: 06:00 + 1 day (day 2)
- Now can properly compare times
```

---

### Multi-Day Duty Conflicts

**What is a multi-day duty?**

A duty that spans multiple calendar days:

```
Multi-Day Duty Example:
Field Exercise: 3 days
- Start: Jan 10, 08:00
- End: Jan 12, 17:00
- Duration: 3 days

This means soldier is busy:
- Jan 10: 08:00 - 17:00 (duty start time to end time)
- Jan 11: 08:00 - 17:00 (full day, same times)
- Jan 12: 08:00 - 17:00 (duty start time to end time)
```

**Conflict Detection for Multi-Day:**

```
Check Each Day of Multi-Day Duty

Multi-Day Duty:
- Start Date: Jan 10, Time: 08:00
- End Date: Jan 12, Time: 17:00
- Duration: 3 days

Soldier's Other Assignments:
- Jan 10, 14:00-18:00: Regular duty
- Jan 11, 10:00-12:00: Different duty
- Jan 13, 09:00-12:00: Another duty

Analysis:
- Jan 10, 14:00-18:00: OVERLAP with 08:00-17:00 ❌
- Jan 11, 10:00-12:00: OVERLAP with 08:00-17:00 ❌
- Jan 13, 09:00-12:00: No overlap (different day) ✅

Result: Cannot assign multi-day duty due to conflicts on Jan 10 & 11
```

---

### Overnight Duty Edge Case

**When multi-day duty extends into next day:**

```
Multi-Day Duty with Overnight:
- Start: Jan 10, 20:00
- End: Jan 12, 06:00 (early morning)
- Duration: 3 days

Soldier's existing duty:
- Jan 12, 08:00-12:00

Analysis:
- 20:00 (Jan 10) to 06:00 (Jan 12): Covers all of Jan 11
- Ends 06:00 (Jan 12)
- New duty starts 08:00 (Jan 12)
- Break: 06:00 to 08:00 = 2 hours ✓

Result: ✅ ALLOWED (sufficient break)
```

---

## Part 3: Multi-Layer Filtering Process

### The Complete Filtering Pipeline

When assigning a soldier to a duty, they go through multiple sequential filters:

```
Soldier Candidate
    ↓
[Filter 1] Rank Match
    ↓ (✓ Passed)
[Filter 2] Active Status
    ↓ (✓ Passed)
[Filter 3] Exclusion List
    ↓ (✓ Passed)
[Filter 4] 24-Hour Duty Conflict
    ↓ (✓ Passed)
[Filter 5] Multi-Day Conflict
    ↓ (✓ Passed)
[Filter 6] Fair Rotation
    ↓ (✓ Passed)
[Filter 7] Time Overlap
    ↓ (✓ Passed)
[Filter 8] Maximum Duties Per Day
    ↓ (✓ Passed)
Soldier ELIGIBLE ✅
```

### Individual Filters Explained

#### **Filter 1: Rank Match**

**Checks:** Does soldier's rank match duty requirements?

```
Duty Requirement:
- Rank: Captain
- Rank: Colonel
- Rank: Major

Soldier: LCP (Lance Corporal)

Result: ❌ FAIL (Rank not in list)

---

Soldier: Captain

Result: ✅ PASS
```

---

#### **Filter 2: Active Status**

**Checks:** Is soldier's status Active?

```
Soldier Status Values:
- Active ✅ PASS
- Inactive ❌ FAIL
- On Leave ❌ FAIL (actually checked separately)
- Discharged ❌ FAIL
```

---

#### **Filter 3: Exclusion List**

**Checks:** Is soldier in the pre-built exclusion list?

```
If soldier_id in exclusionList:
    FAIL ❌
Else:
    PASS ✅
```

---

#### **Filter 4: 24-Hour Duty Conflict**

**Checks:** Does new duty conflict with existing 24-hour duty?

```
Case 1: NEW duty is 24-hour
Question: Does soldier have ANY duty on this date?
If YES: FAIL ❌
If NO: PASS ✅

---

Case 2: NEW duty is regular (not 24-hour)
Question: Does soldier have a 24-hour duty on this date?
If YES: FAIL ❌ (can't add regular to 24-hour)
If NO: PASS ✅
```

---

#### **Filter 5: Multi-Day Duty Conflict**

**Checks:** For each day of multi-day duty, check for conflicts with overnight duties

```
Multi-Day Duty: Jan 10-12 (08:00-17:00)

Check Jan 9: Do any of soldier's duties from Jan 9 
             extend into Jan 10?
             (Overnight duties)
Result: ❌ FAIL if yes

Check Jan 10: Regular time conflict check
Check Jan 11: Regular time conflict check
Check Jan 12: Regular time conflict check

If any day has conflict:
    FAIL ❌
Else:
    PASS ✅
```

---

#### **Filter 6: Fair Rotation**

**Checks:** Did soldier just complete this duty?

```
Question: What was this soldier's LAST assignment 
          to this SAME duty?

If found:
    lastDutyDate = Jan 5
    durationDays = 3
    lastDutyEndDate = Jan 5 + 3 - 1 = Jan 7
    
    If newDate = Jan 7 + 1 = Jan 8 (immediately after):
        FAIL ❌ (no back-to-back same duty)
    Else if newDate = Jan 9 or later:
        PASS ✅ (sufficient gap)

If not found:
    PASS ✅ (first time or long ago)
```

---

#### **Filter 7: Time Overlap with Existing Duties**

**Checks:** Does new duty overlap with existing duties on same date(s)?

```
For each day in new duty duration:
    Get all existing duties for that day
    
    For each existing duty:
        Check overlap using algorithm from Part 2
        If overlap found:
            FAIL ❌
            
If all checks pass:
    PASS ✅
```

---

#### **Filter 8: Maximum Duties Per Day**

**Checks:** Has soldier already reached limit of duties per day?

```
Max duties per day = 2

Count existing duties for this date
    
If count >= 2:
    FAIL ❌
Else if count = 0 or 1:
    PASS ✅
```

---

### Filter Results Logging

System logs detailed statistics after filtering:

```
Filtering Results for Duty Assignment

Initial Candidates:        50 soldiers
After Rank Match:          12 soldiers (38 filtered)
After Active Status:       12 soldiers (0 filtered)
After Exclusion List:      8 soldiers (4 filtered by leave/cadre/etc)
After 24-Hour Conflict:    8 soldiers (0 filtered)
After Multi-Day Conflict:  7 soldiers (1 filtered)
After Fair Rotation:       5 soldiers (2 filtered)
After Time Overlap:        5 soldiers (0 filtered)
After Max Duties:          5 soldiers (0 filtered)

ELIGIBLE SOLDIERS: 5
TOTAL FILTERED OUT: 45
FILTER EFFICIENCY: 90%
```

---

## Part 4: Group Duties vs Individual Rank Duties

### Group Duty Concept: "1 Captain OR 1 Colonel"

**What is a Group Duty?**

A group duty allows selecting ONE suitable soldier from a GROUP of ranks:

```
Example: Commander Duty
"Need 1 person who is either:
  - Captain, OR
  - Colonel, OR  
  - Major"

Typical Setup:
├─ Group ID: 5
├─ Rank 1: Captain (group_id = 5)
├─ Rank 2: Colonel (group_id = 5)
└─ Rank 3: Major (group_id = 5)

Assignment: Pick ANY ONE soldier from any of these 3 ranks
```

---

### Assignment Logic for Group Duties

```
Step 1: Identify all ranks in group
        ranks_in_group = [Captain, Colonel, Major]

Step 2: For each rank, find eligible soldiers
        
For Captain:
    - Filter by rank = Captain
    - Apply all 8 filters
    - Result: [SGT Ahmed, CPT Maria, CPT Rashid]

For Colonel:
    - Filter by rank = Colonel
    - Apply all 8 filters
    - Result: [COL Hassan]

For Major:
    - Filter by rank = Major
    - Apply all 8 filters
    - Result: [MAJ Ali, MAJ Fatima]

Step 3: Combine all eligible soldiers
        All eligible = [Ahmed, Maria, Rashid, Hassan, Ali, Fatima]

Step 4: Sort by fair rotation and current duty count
        (Load balance + avoid repetition)

Step 5: Assign first soldier from sorted list
        Assignment: Ahmed
        
Step 6: Mark group as processed
        (Don't check other ranks in group for this duty)
```

---

### Individual Rank Duty

**What is Individual Rank Duty?**

A duty that requires a specific rank (no group):

```
Example: Parade Ground Marshal
Requirement: 1 Sergeant (exactly Sergeant, not other ranks)

DutyRank Entry:
- Rank: Sergeant
- Group ID: NULL (not part of group)
- Duty Type: roster
- Manpower: 1
```

**Assignment:**
```
Step 1: Find all Sergeants

Step 2: Apply all 8 filters

Step 3: Assign suitable soldiers to fill manpower requirement
        If need 3 Sergeants:
            Assign 1st eligible Sergeant
            Assign 2nd eligible Sergeant
            Assign 3rd eligible Sergeant
```

---

## Part 5: Reassignment Logic

### Why Reassign?

```
Scenario:
You assigned SGT Ahmed to duty A for Jan 5
Later you realize:
  - Duty B is more critical
  - Ahmed is perfect for Duty B
  - Ahmed doesn't have to do Duty A

Solution: Reassign from A to B
```

### Reassignment Process

```
Step 1: Validate original assignment exists
        Soldier: Ahmed
        From Duty: A
        Date: Jan 5
        
        Check: Is Ahmed assigned to Duty A on Jan 5?
        Result: YES ✓

Step 2: Check if new duty is eligible
        To Duty: B
        
        Run canAssignSoldierToDuty(Ahmed, B, Jan 5)
        Check passes all 8 filters?
        Result: YES ✓

Step 3: Delete old assignment
        SoldierDuty: where soldier_id=Ahmed, duty_id=A, date=Jan5
        Delete

Step 4: Create new assignment
        SoldierDuty: Insert new record for Ahmed, Duty B, Jan 5

Step 5: Log and confirm
        Reassignment complete
```

---

## Part 6: Practical Examples

### Example 1: Time Overlap Detection

```
Scenario: Assigning soldier to duty with time conflict

Date: January 10, 2026
Soldier: SGT Ahmed
New Duty Assignment: Traffic Control (10:00 - 14:00)

Ahmed's Existing Duties on Jan 10:
├─ Duty 1 (morning): 08:00 - 10:00 (2 hours)
├─ Duty 2 (afternoon): 14:00 - 18:00 (4 hours)
└─ Duty 3 (evening): 19:00 - 23:00 (4 hours)

Analysis:

Duty 1: 08:00 - 10:00
New:    10:00 - 14:00
Overlap: 10:00 is exact boundary (no overlap)
Break: 0 minutes ❌ INSUFFICIENT
Required break: 60 minutes
Status: CONFLICT (insufficient break between duties)

Result: ❌ CANNOT ASSIGN Traffic Control
Reason: Only 0-minute gap between Duty 1 end and Traffic Control start

---

Solution: Change Traffic Control time to 11:00 - 15:00
Then:
Break after Duty 1: 08:00 - 11:00 = 1 hour ✓ (exceeds 60 min)
Overlap with Duty 2: 14:00 starts right when Traffic Control ends
Break before Duty 2: 0 minutes ❌ INSUFFICIENT

Solution 2: Change Traffic Control to 11:00 - 13:00
Then:
Gap after Duty 1: 1 hour ✓
Gap before Duty 2: 1 hour ✓
No overlap
Result: ✅ CAN ASSIGN
```

---

### Example 2: Multi-Day Duty Conflict

```
Scenario: Assigning 3-day field exercise

Soldier: PTE Rashid
New Duty: Field Exercise (Duration: 3 days)
Dates: Jan 15-17, Time: 08:00 - 17:00

Rashid's Existing Duties:
├─ Jan 14: 10:00-14:00 (regular duty) - different day, OK
├─ Jan 15: 14:00-18:00 (regular duty) - SAME START DAY
├─ Jan 16: 09:00-12:00 (regular duty) - MIDDLE DAY
├─ Jan 17: 16:00-20:00 (regular duty) - SAME END DAY
└─ Jan 18: 10:00-14:00 (regular duty) - different day, OK

Analysis:

Jan 15:
Exercise: 08:00-17:00
Existing: 14:00-18:00
Overlap: 14:00-17:00 (3 hours)
Status: ❌ CONFLICT

Jan 16:
Exercise: 08:00-17:00
Existing: 09:00-12:00
Overlap: 09:00-12:00 (3 hours)
Status: ❌ CONFLICT

Jan 17:
Exercise: 08:00-17:00
Existing: 16:00-20:00
Overlap: 16:00-17:00 (1 hour)
Status: ❌ CONFLICT

Result: ❌ CANNOT ASSIGN
Reason: Conflicts on all three days of exercise
```

---

### Example 3: Fair Rotation Check

```
Scenario: Avoiding back-to-back same duty

Soldier: CPL Maria
Duty: Gate Guard
Assignment Request: Jan 18

Maria's History:
├─ Jan 1-3: Gate Guard (3 days, ends Jan 3)
├─ Jan 4-6: Off (no assignment)
├─ Jan 7-9: Gate Guard (3 days, ends Jan 9)
├─ Jan 10-16: Off (no assignment)
└─ Jan 17: ?

Check for Assignment on Jan 18:

Last Task of Gate Guard completed: Jan 9
Duration: 3 days
Last Duty End Date: Jan 9 + 3 - 1 = Jan 9
Day after Last Duty: Jan 9 + 1 = Jan 10
Assignment Request Date: Jan 18

Is Jan 18 immediately after last duty end (Jan 9)?
Jan 18 ≠ Jan 10
Answer: NO

Days since last duty: Jan 18 - Jan 9 = 9 days

Result: ✅ CAN ASSIGN
Reason: Sufficient gap since last assignment (9 days > 1 day minimum)

---

Now if request was for Jan 10:
Is Jan 10 immediately after last duty end (Jan 9)?
Jan 10 = Jan 9 + 1
Answer: YES

Result: ❌ CANNOT ASSIGN
Reason: Back-to-back same duty not allowed (Fair Rotation enforcement)
```

---

### Example 4: Group Duty Selection

```
Scenario: Assigning group duty with multiple rank options

Duty: Command Duty
Requirement: 1 Commander (Captain OR Colonel OR Major)
Date: January 20, 2026

Available Soldiers:

Captains:
├─ CPT Ahmed: ✓ All filters pass, Current duties: 1
├─ CPT Aisha: ✓ All filters pass, Current duties: 2 (MAX REACHED) ❌
└─ CPT Omar: ✗ On leave ❌

Colonels:
├─ COL Hassan: ✓ All filters pass, Current duties: 0
└─ COL Fatima: ✗ In course ❌

Majors:
├─ MAJ Ali: ✓ All filters pass, Current duties: 1
└─ MAJ Sara: ✓ All filters pass, Current duties: 1

Eligible Soldiers:
├─ CPT Ahmed (1 duty, Captain)
├─ COL Hassan (0 duties, Colonel)
├─ MAJ Ali (1 duty, Major)
└─ MAJ Sara (1 duty, Major)

Sorting by:
1. Fewest current duties (load balance)
2. Last assignment date (fair rotation)

Sorted:
1. COL Hassan (0 duties) ← FIRST
2. CPT Ahmed (1 duty)
3. MAJ Ali (1 duty)
4. MAJ Sara (1 duty)

Assignment: COL Hassan ✓

Result: Command Duty → COL Hassan
Reason: Best load balance (0 duties)
```

---

### Example 5: Exclusion List in Action

```
Scenario: Building exclusion list for January 10

Soldiers Currently in System: 100

Step 1: Leave
On approved leave Jan 10:
├─ SGT Ahmed (leave: Jan 5-15)
├─ CPL Maria (leave: Jan 10-12)
└─ PTE Rashid (leave: Jan 8-20)
Total Excluded: 3

Step 2: Cadre (instructors)
Active cadre assignments covering Jan 10:
├─ CPT Hassan (active cadre, no end date)
├─ LCP Amir (active cadre until Dec 31, 2025) - END PASSED ✓
└─ SGT Habib (active cadre, end date Jan 15)
Total Excluded: 2 (Hassan, Habib) - Note: Amir NOT excluded (ended Dec 31)

Current Total: 3 + 2 = 5 Excluded

Step 3: Courses
In active courses covering Jan 10:
├─ COL Ahmed (course: Jan 1-15)
├─ MAJ Ali (course: Jan 5-10) - INCLUDES Jan 10 ✓
└─ CPT Aisha (course: Jan 12-30) - STARTS Jan 12, NOT covering Jan 10 ✗
Total New Excluded: 2

Current Total: 5 + 2 = 7 Excluded

Step 4: Exercise Areas
In exercise areas covering Jan 10:
├─ SGT Karim (exercise: Jan 9-11, includes Jan 10)
└─ LCP Fadi (exercise: Jan 10-12)
Total New Excluded: 2

Current Total: 7 + 2 = 9 Excluded

Step 5: Services/Appointments
In active service covering Jan 10:
└─ MAJ Noor (service from: Jan 1, to: NULL - ongoing)
Total New Excluded: 1

Current Total: 9 + 1 = 10 Excluded

Step 6: Fixed Duties
Assigned to fixed duties:
├─ SGT Guard (Gate Guard - fixed)
├─ CPL Driver (Transport - fixed)
└─ PTE Cook (Kitchen - fixed)
Total New Excluded: 3

Current Total: 10 + 3 = 13 Excluded

Step 7: CMD (Command)
In command roles:
└─ COL Commander (CMD from Jan 1, no end date)
Total New Excluded: 1

Current Total: 13 + 1 = 14 Excluded

Step 8: ATT (Annual Training)
In annual training:
├─ MAJ Trainer (ATT: Jan 2-31)
└─ CPT Student (ATT: Jan 15-24)
Total New Excluded: 1 (only MAJ Trainer - CPT Student starts Jan 15, not covering full Jan 10)

Final Exclusion List:
Total Excluded: 15 soldiers
Available for rotation: 100 - 15 = 85 soldiers

Excluded Detailed:
├─ Leave: SGT Ahmed, CPL Maria, PTE Rashid (3)
├─ Cadre: CPT Hassan, SGT Habib (2)
├─ Courses: COL Ahmed, MAJ Ali (2)
├─ Exercise: SGT Karim, LCP Fadi (2)
├─ Services: MAJ Noor (1)
├─ Fixed Duties: SGT Guard, CPL Driver, PTE Cook (3)
├─ CMD: COL Commander (1)
└─ ATT: MAJ Trainer (1)
```

---

## Part 7: Troubleshooting Complex Scenarios

### Scenario A: "Why won't soldier get assigned?"

```
Issue: PTE Ali should be assigned but keeps getting filtered

Debug Steps:

Step 1: Check Exclusion List
Query: Is Ali in exclusion list for this date?
If YES:
  - On leave? → Check LeaveApplication (approved status, date range)
  - In cadre? → Check SoldierCadre (active status, date range)
  - In course? → Check SoldierCourse (active status, date range)
  - In exercise? → Check SoldierExArea (active status, date range)
  - In service? → Check SoldierServices (active status, date range)
  - Fixed duty? → Check DutyRank (duty_type = fixed, soldier_id assigned)
  - In CMD? → Check soldier relationships
  - In ATT? → Check ATT records
Result: [Specific reason found]

Step 2: Check Rank Match
Query: Does Ali have required rank for duty?
Ali's rank: Corporal
Duty Requirements: Captain, Major
Result: Rank mismatch ✓ (this is the problem)

Step 3: Check Time Conflicts
(If rank matches, continue)
Query: When was Ali's last duty on this date?
If YES: Check overlap using time overlap algorithm
Result: [Conflict found or not]

Step 4: Check Fair Rotation
Query: When was Ali's last assignment to this same duty?
If recently: Is it immediately back-to-back?
Result: [Fair rotation violation or OK]

Step 5: Check Max Duties
Query: How many duties already assigned to Ali today?
Result: Max reached or still available

Take Action:
- If rank mismatch: Use different rank soldiers
- If time conflict: Resolve conflict first
- If fair rotation issue: Wait longer or different duty
- If max reached: Reduce other duties
```

---

### Scenario B: "Why is soldier getting conflicting duties?"

```
Issue: Soldier assigned to two overlapping duties

Debug:

Question: When were they assigned?
- Case A: Auto-assigned same run
  Unlikely: System checks for conflicts during assignment
- Case B: Assigned separately
  Likely: First assignment OK, second assignment missed conflict

Check:
1. First duty: 10:00-14:00 on Jan 5
   Assigned: Yes, no conflicts at that time

2. Second duty: 12:00-16:00 on Jan 5
   Check conflicts?
   System should have detected overlap:
   10:00-14:00 vs 12:00-16:00 = 2-hour overlap
   
   Why wasn't it caught?
   - Manual override used? (force_assignment flag)
   - Time stored incorrectly? (check database format)
   - Cache not updated? (exclusion list cached before 2nd assignment)

Solution: Cancel second duty and reassign properly
```

---

### Scenario C: "Why are some duties unfulfilled?"

```
Issue: Some duties don't get filled

Analyze:

Duty: Parade Ground Marshal
Requirement: 3 Corporals
Result: Only 2 assigned

Possible Reasons:

1. Not Enough Eligible Soldiers
   Total Corporals: 10
   In exclusion: 5 (leave, course, etc.)
   Available: 5
   Already assigned today: 2 (maximum duties reached)
   Truly eligible: 3 remaining
   Result: Exactly 3 available ✓

   But only 2 assigned?
   
2. Check Filters Applied
   For 3rd soldier:
   - Rank match? YES
   - Active? YES
   - Exclusion list? YES (was just included in exclusion)
   - 24-hour conflict? NO
   - Multi-day conflict? NO
   - Fair rotation? MAYBE (just did this duty yesterday?)
   - Time overlap? NO
   - Max duties? YES (already assigned 2 today)
   
   Result: FILTERED OUT BY MAX DUTIES (limit = 2)

3. Action
   Option A: Remove 3rd soldier from another duty
   Option B: Increase max duties per day limit (change from 2 to 3)
   Option C: Leave unfulfilled (accept 2 of 3 requirement)
```

---

## Part 8: Configuration and Tuning

### Adjustable Parameters

```php
protected $minBreakMinutes = 60;        // Minimum gap between duties
protected $maxDutiesPerDay = 2;         // Maximum duties per 24-hour period
```

### When to Adjust

**Increase minBreakMinutes:**
- Soldiers need more recovery time
- Duties are physically demanding
- Want fewer duties per soldier

**Decrease minBreakMinutes:**
- Need more duty coverage
- Duties are administrative (not taxing)
- More soldiers willing to work

**Increase maxDutiesPerDay:**
- Shortage of available soldiers
- Need more coverage
- Can accept soldier fatigue

**Decrease maxDutiesPerDay:**
- Soldiers complaining about workload
- Quality of work declining
- Fatigue issues

---

## Part 9: Real-World Complex Scenario

### Complete Assignment Walkthrough

```
Scenario: Assigning duties for January 25, 2026

System Parameters:
- Min Break: 60 minutes
- Max Duties/Day: 2
- Current Time: 5 AM (assignment happens in background)

Step 1: Get Active Roster Duties
Duties to fill:
├─ Gate Guard (08:00-16:00): 2 Sergeants needed
├─ Parade (08:00-10:00): 1 Captain needed
├─ Control Room (18:00-02:00): 1 Corporal needed
├─ Traffic (10:00-14:00): 2 Lance Corporals needed
└─ Guard Overnight (22:00-06:00): 1 Sergeant needed

Step 2: Build Exclusion List
Total soldiers: 150
After inclusion queries:
├─ Leave: 18 soldiers
├─ Cadre: 12 soldiers
├─ Courses: 8 soldiers
├─ Exercise: 5 soldiers
├─ Services: 3 soldiers
├─ Fixed: 4 soldiers
└─ Total Excluded: 50 soldiers
Available: 100 soldiers

Step 3: Assign Gate Guard (need 2 Sergeants)
Sergeants available: 20
After filters:
├─ Not on duty that day: 18
├─ Not 24-hour duty: 18
├─ No multi-day conflict: 18
├─ Fair rotation OK: 15
├─ Time OK: 15
├─ Max duties OK: 15

Sort by current duties (0), then fair rotation
Top 2 selected:
├─ SGT Ahmed (0 duties) ✓
└─ SGT Aisha (0 duties) ✓

Step 4: Assign Parade (need 1 Captain)
Captains available: 12
After filters: 10 eligible

Sort by load balance, select:
├─ CPT Hassan (0 duties) ✓

Step 5: Assign Control Room (need 1 Corporal)
Corporals available: 25
After filters: 20 eligible

Sort, select:
├─ CPL Maria (0 duties) ✓

Step 6: Assign Traffic (need 2 Lance Corporals)
LCPs available: 30
After filters: 25 eligible

Sort, select:
├─ LCP Ahmed (0 duties) ✓
├─ LCP Rashid (0 duties) ✓

Step 7: Assign Guard Overnight (need 1 Sergeant)
Sergeants available: 20
After filters: 18 (Ahmed and Aisha now have 1 duty each)
Remaining with 0 duties: 16
After all filters: 14 eligible

BUT: Ahmed: 08:00-16:00
     Overnight: 22:00-06:00
     Break: 16:00-22:00 = 6 hours ✓ (exceeds 60 min)
     Can assign Ahmed ✓

But system picks fresh soldier with 0 duties:
├─ SGT Omar (0 duties) ✓

FINAL ASSIGNMENTS (Jan 25):
├─ Gate Guard 08:00-16:00: SGT Ahmed, SGT Aisha
├─ Parade 08:00-10:00: CPT Hassan
├─ Control Room 18:00-02:00: CPL Maria
├─ Traffic 10:00-14:00: LCP Ahmed, LCP Rashid
└─ Overnight 22:00-06:00: SGT Omar

All duties filled ✓
```

---

## Summary

The duty assignment system is sophisticated because:

1. **Multi-layer Filtering** - 8 sequential checks ensure no conflicts
2. **Time Overlap Prevention** - Complex time calculations with overnight duty handling
3. **Fair Rotation** - Prevents soldier burnout and promotes equity
4. **Load Balancing** - Distributes work fairly
5. **Exclusion Intelligence** - Respects soldier commitments (leave, training, existing duties)
6. **Group Duty Logic** - Flexible satisfaction of requirements

Each assignment decision is carefully logged and validated.

