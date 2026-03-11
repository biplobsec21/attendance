# Military Training Module Documentation

## Module Overview

The **Military Training Module** is a comprehensive system for managing soldier training, professional development, and exercise participation. It enables:

- **Course Assignment** - Assign soldiers to military courses and track completion
- **Cadre Management** - Assign cadre (instructors) to soldiers and track mentorship
- **Exercise Area Tracking** - Record soldier participation in exercise areas
- **Training Status Monitoring** - Track ongoing, scheduled, and completed training
- **Automatic Status Updates** - System automatically manages training status lifecycle
- **Training Recommendations** - Link instruction recommendations to training assignments
- **Bulk Operations** - Manage multiple training assignments efficiently
- **Training History** - Complete record of all soldier training and development

This module ensures soldiers receive proper training, mentorship, and exercise participation for professional development and operational readiness.

---

## Key Features

### 1. **Course Assignment and Tracking**

**What it does:**
- Assign soldiers to military courses and training programs
- Track course enrollment dates and expected completion dates
- Monitor training progress and completion status
- Record course details and training requirements
- Track instructor/cadre assignments for courses

**Course Information:**
- Course name and identification
- Course duration (start and end dates)
- Training location/facility
- Required prerequisites
- Completion requirements
- Certification provided

**Soldier Assignment Details:**
- Assign individual soldiers to specific courses
- Set start date (enrollment date)
- Set end date (expected completion)
- Add notes or special requirements
- Link instruction recommendations
- Assign lead cadre/instructor

**How it works:**
1. Select course from available list
2. Choose soldiers to assign to course
3. Set training dates
4. Add any special instructions
5. Save assignment
6. System tracks progress automatically

**When to use:**
- New soldier onboarding training
- Mandatory annual training courses
- Specialized skill development
- Leadership development programs
- Technical certifications

**Course Status Flow:**
```
Scheduled → Active → Completed
   (Not yet started) (In progress) (Finished)
```

---

### 2. **Cadre (Instructor) Assignment**

**What it does:**
- Assign qualified instructors/cadre to soldiers for mentorship
- Track one-on-one mentorship relationships
- Monitor cadre workload and assignments
- Document training provided by each cadre member
- Record cadre recommendations

**Cadre Information:**
- Cadre name and rank
- Qualifications and certifications
- Available training topics
- Current assignments
- Mentorship capacity

**Assignment Details:**
- Assign cadre to specific soldier
- Set mentorship start date
- Set expected completion date
- Type of training/mentorship
- Special focus areas
- Progress notes

**Benefits:**
- Ensures soldiers get qualified instruction
- Builds mentorship relationships
- Tracks professional development
- Documents training received
- Creates mentorship records

**When to use:**
- New soldier onboarding mentorship
- Leadership coaching
- Technical skill transfer
- Professional development
- Performance improvement plans

---

### 3. **Exercise Area Management**

**What it does:**
- Track soldier participation in exercise areas/training locations
- Record training exercises completed
- Monitor exercise facility usage
- Track exercise participation history
- Link soldiers to specific exercise areas

**Exercise Area Information:**
- Exercise name and location
- Exercise type (Field exercise, Shooting range, Combat training, etc.)
- Exercise dates and duration
- Difficulty level
- Equipment requirements
- Safety considerations

**Participation Tracking:**
- Record which soldiers participated
- Date of participation
- Duration in exercise area
- Performance observations
- Completion status
- Notes and recommendations

**When to use:**
- Field exercises and operations
- Shooting range sessions
- Combat training exercises
- Fitness challenges
- Tactical training
- Equipment familiarization

---

### 4. **Training Status Management**

**What it does:**
The system automatically manages training status for each assignment.

**Training Status States:**

| Status | Meaning | Duration |
|--------|---------|----------|
| **Scheduled** | Training assigned, not yet started | Before start date |
| **Active** | Training in progress | Between start and end date |
| **Completed** | Training finished successfully | After end date |
| **Pending Review** | Awaiting final completion verification | After end date, before approval |

**Automatic Status Changes:**
- System checks dates daily
- Status automatically updates when dates pass
- Completion can be manually confirmed
- Status changes are logged with timestamps

**How Dates Control Status:**

```
Today = Jan 15
Course: Jan 20 - Feb 20

Jan 15-19: Status = "Scheduled" (before start)
Jan 20+:   Status = "Active" (courses started)
Feb 21+:   Status = "Completed" (after end date)
```

---

### 5. **Training Assignment Creation**

**What it does:**
- Create new training assignments for courses, cadre, and exercise areas
- Select soldiers and training type
- Set dates and requirements
- Add notes and recommendations
- Assign instructors

**Assignment Creation Workflow:**

```
Step 1: Choose Training Type
        ├─ Course Assignment
        ├─ Cadre Mentorship
        └─ Exercise Area

Step 2: Select Training
        └─ Choose from available courses/cadre/areas

Step 3: Select Soldiers
        └─ Choose one or multiple soldiers

Step 4: Set Dates
        ├─ Start date (when training begins)
        └─ End date (expected completion)

Step 5: Add Details
        ├─ Note/special requirements
        ├─ Link recommendations
        └─ Assign instructors

Step 6: Save Assignment
        └─ System creates record & tracks status
```

**When to use:**
- Enrolling soldiers in courses
- Assigning mentors for development
- Recording exercise participation
- Scheduling training activities
- Setting training requirements

---

### 6. **Training Completion and Verification**

**What it does:**
- Manually mark training as completed
- Add completion notes and observations
- Verify training achievement
- Award certifications or credit
- Generate completion records

**Completion Process:**

```
Step 1: Find Active Training Assignment
        ├─ Course in progress
        ├─ Cadre assignment active
        └─ Exercise area participation

Step 2: Review Training Progress
        └─ Verify all requirements met

Step 3: Mark as Completed
        ├─ Add final notes
        ├─ Document lessons learned
        └─ Record any certifications earned

Step 4: System Records
        ├─ Changes status to "Completed"
        ├─ Timestamp recorded
        └─ Entry moved to history
```

**What Completion Does:**
- Marks training as successfully finished
- Creates permanent record for soldier
- Updates soldier's qualifications
- Enables new training assignments
- Generates training certificates (if applicable)

---

### 7. **Instruction Recommendations**

**What it does:**
Link instruction recommendations to training assignments. These might be requirements or special instructions for the training.

**Recommendation Types:**
- Physical fitness requirement
- Safety certification
- Equipment proficiency
- Leadership development
- Technical skills
- Behavioral coaching
- Performance improvement

**How to Use:**
1. Create recommendation in Settings
2. Link to training assignment
3. System tracks if recommendation is fulfilled
4. Completion records recommendation achievement

---

### 8. **Bulk Training Operations**

**What it does:**
- Manage multiple training assignments at once
- Complete many trainings simultaneously
- Apply same training to multiple soldiers
- Save time with batch operations

**Bulk Operations Available:**
- **Bulk Complete Courses** - Mark multiple courses complete
- **Bulk Complete Cadre** - Mark multiple cadre assignments complete
- **Bulk Complete Exercise Areas** - Mark multiple exercise areas completed
- **Bulk Assign Training** - Assign same training to multiple soldiers

**When to use:**
- End-of-program batch completions
- Course completion ceremonies
- Exercise completion after major event
- Quarterly certification updates
- Mass graduation processing

---

### 9. **Training History and Records**

**What it does:**
- Maintain complete history of all training received
- Track training progression over time
- Document professional development path
- Create audit trail for compliance
- Show soldier training timeline

**Information Tracked:**
- Course/cadre/exercise completed
- Dates of training
- Duration of training
- Certificates or credentials earned
- Instructor/cadre assigned
- Performance notes
- Completion status

**Benefits:**
- Know soldier's complete training background
- Identify training gaps
- Plan future development
- Verify compliance with requirements
- Reference for promotions/assignments

---

### 10. **Search and Filter Training**

**What it does:**
- Search for specific training assignments
- Filter by soldier, course, cadre, or date
- View current or historical training
- Sort by various criteria
- Export training data

**Filter Options:**
- **By Soldier** - All training for specific person
- **By Course/Cadre/Area** - All participants
- **By Status** - Scheduled, Active, Completed
- **By Date** - Training in specific period
- **By Instructor** - All courses from cadre

**Search Features:**
- Real-time search by name
- Multiple filter combinations
- Paginated results
- Export filtered data
- Save common searches

---

### 11. **Training Editing and Modification**

**What it does:**
- Edit active training assignments
- Change dates if needed
- Update notes or requirements
- Modify cadre/instructor assignments
- Adjust training details

**What Can Be Changed:**
- Start date
- End date
- Training location
- Notes and requirements
- Assigned cadre/instructor
- Recommendations

**Limitations:**
- Cannot edit completed training (history preserved)
- Date changes must be valid
- Completed trainings are locked for audit

---

### 12. **Training History Deletion**

**What it does:**
- Remove training assignments from system
- Typically only for pending/scheduled training
- Completed training kept for history

**When to use:**
- Incorrect training assignment entered
- Soldier withdrawn from training
- Duplicate entry created
- Training cancelled

**What Happens:**
- Assignment permanently removed
- Never appears in history
- Other records unaffected

---

## Conditions and Business Rules

### **Rules for Course Assignment**

| Rule | Description |
|------|-------------|
| **Soldier Must Be Active** | Only active soldiers can be assigned to training |
| **Valid Date Range** | Start date must be before end date |
| **Course Must Exist** | Cannot assign courses not defined in Settings |
| **Unique Assignment** | Soldier cannot have overlapping courses |
| **Dates Required** | Both start and end dates required |
| **One at a Time** | Generally one course per soldier per time period |

---

### **Rules for Cadre Assignment**

| Rule | Description |
|------|-------------|
| **Cadre Must Be Active** | Only active cadre can be assigned as instructors |
| **Soldier Must Be Active** | Only active soldiers can receive mentorship |
| **Mentorship Period** | Start and end dates establish mentorship period |
| **Qualification Match** | Cadre should match training topic if possible |
| **One Lead Cadre** | Each assignment typically has one lead instructor |

---

### **Rules for Exercise Area**

| Rule | Description |
|------|-------------|
| **Exercise Must Exist** | Cannot record participation in non-existent areas |
| **Participation Date** | Must record actual exercise date |
| **Soldier Capacity** | Exercises have maximum participant limits |
| **Safety Requirements** | Must document safety compliance |
| **Supervisor Present** | Exercise areas must have assigned supervisor |

---

### **Rules for Status Management**

| Rule | Description |
|------|-------------|
| **Auto Status Update** | System updates status based on dates daily |
| **Scheduled → Active** | Automatically changes on start date |
| **Active → Completed** | Automatically changes on end date |
| **Manual Completion** | Can manually complete before end date |
| **Completed is Final** | Cannot revert to active once completed |

---

## User Workflows

### **Workflow 1: Enroll Soldier in Course**

```
Step 1: Navigate to Training Module
        ↓
Step 2: Click "Create New Training" or "Assign Training"
        ↓
Step 3: Choose Training Type
        - Select "Course"
        ↓
Step 4: Select Course
        - Choose course from list
        - View course details if needed
        ↓
Step 5: Select Soldiers
        - Choose one or multiple soldiers
        - System shows available soldiers
        ↓
Step 6: Set Training Dates
        - Click start date (when course begins)
        - Click end date (expected completion)
        - System shows duration
        ↓
Step 7: Add Details (Optional)
        - Add special instructions
        - Link training recommendations
        - Assign lead cadre/instructor
        ↓
Step 8: Save Assignment
        - System creates enrollment
        - Status set to "Scheduled"
        - Confirmation displayed
        ↓
Step 9: Training Begins
        - On start date, status auto-updates to "Active"
        - Soldier is now in active training
        - Progress can be tracked
```

---

### **Workflow 2: Assign Cadre for Mentorship**

```
Step 1: Go to Training Module
        ↓
Step 2: Click "Create New Training" → "Cadre Assignment"
        ↓
Step 3: Select Cadre/Instructor
        - Choose qualified cadre from list
        - View qualifications and availability
        ↓
Step 4: Select Mentee Soldier(s)
        - Choose soldier(s) receiving mentorship
        - Can select single or multiple soldiers
        ↓
Step 5: Set Mentorship Period
        - Start date (mentorship begins)
        - End date (expected completion)
        ↓
Step 6: Add Mentorship Details
        - Type of instruction (Leadership, Technical, etc.)
        - Focus areas
        - Special requirements
        ↓
Step 7: Save Assignment
        - Mentorship relationship created
        - Status: "Scheduled"
        ↓
Step 8: Mentorship Period
        - On start date: Auto-changes to "Active"
        - Cadre works with soldier during period
        - Progress documented
```

---

### **Workflow 3: Record Exercise Participation**

```
Step 1: Navigate to Training Module
        ↓
Step 2: Click "Create New Training" → "Exercise Area"
        ↓
Step 3: Select Exercise Area
        - Choose location/exercise type
        ↓
Step 4: Select Participating Soldiers
        - Check multiple soldiers
        - Show who participated
        ↓
Step 5: Set Exercise Dates
        - Start of exercise
        - End of exercise
        ↓
Step 6: Add Exercise Details
        - Performance observations
        - Safety notes
        - Lessons learned
        ↓
Step 7: Save Exercise Record
        - Participation recorded
        - Status: "Scheduled" (before date) or "Active"
        ↓
Step 8: After Exercise
        - Auto-completes on end date
        - Record becomes history
```

---

### **Workflow 4: Mark Training Complete**

```
Step 1: Find Active Training
        ↓
Step 2: Go to Active/Current Training List
        ↓
Step 3: Find Training Assignment
        - Course, Cadre, or Exercise Area
        ↓
Step 4: Click "Mark Complete" or "Complete"
        
        Option A: Auto-Complete (if past end date)
        ──────────────────────────────────────
        - System shows "End date passed"
        - Click "Complete Training"
        - Asks for final notes
        
        Option B: Early Completion
        ────────────────────────
        - Click "Complete Early"
        - Provide completion reason
        - Add final assessment
        ↓
Step 5: Add Completion Information
        - Final notes/observations
        - Did soldier achieve goals? (Yes/No)
        - Any certifications earned?
        - Recommendations for next training
        ↓
Step 6: Finalize Completion
        - Review all information
        - Click "Confirm Completion"
        - System records:
          * Changes status to "Completed"
          * Records current date/time
          * Moves to history/past training
          * Updates soldier's qualifications
        ↓
Step 7: Confirmation
        - "Training completed successfully"
        - Sent to history
```

---

### **Workflow 5: Bulk Complete Multiple Trainings**

```
Step 1: Go to Active Training List
        ↓
Step 2: Select Multiple Trainings
        - Check boxes next to courses/cadre
        - Click "Select All" if needed
        ↓
Step 3: Click "Bulk Actions" Menu
        ↓
Step 4: Choose "Complete All Selected"
        ↓
Step 5: Batch Complete Settings
        - All selected trainings complete
        - Same completion date applied
        - Can add same notes to all
        ↓
Step 6: Confirm Bulk Action
        - Review selected trainings
        - Click "Complete All"
        - System processes batch
        ↓
Step 7: Results Summary
        - "X trainings completed"
        - All statuses updated
        - Moved to history
```

---

### **Workflow 6: View Training History**

```
Step 1: Navigate to Training Module
        ↓
Step 2: Click on Soldier Name/Record
        ↓
Step 3: View Their Training History
        - Current active trainings
        - Scheduled trainings
        - Past completed trainings
        - Timeline view
        ↓
Step 4: Review Training Details
        - Course/cadre/exercise name
        - Dates of training
        - Duration and status
        - Completion notes
        - Certifications earned
        ↓
Step 5: Optional - Export History
        - Download as PDF
        - Export to Excel
        - Print record
```

---

## System Behavior & Automation

### **What Happens When Training Is Assigned?**

1. **Assignment Created** with start/end dates
2. **Status Set to "Scheduled"** (before start date)
3. **Soldier Marked** as enrolled in training
4. **System Tracks** assignment in current trainings
5. **Records Created** for audit trail

---

### **What Happens When Start Date Arrives?**

1. **System Checks** all scheduled trainings daily
2. **Status Auto-Updates** to "Active"
3. **Training Begins** - Soldier now in active period
4. **Progress Tracking** becomes available
5. **Visible in Active** list (not scheduled list)

---

### **What Happens When End Date Arrives?**

1. **Status Auto-Updates** to "Completed"
2. **Training Period Ends** - No longer active
3. **Can Be Manually Verified** by instructor
4. **Moved to History** - Past training records
5. **Soldier Becomes Available** for new training

---

### **What Happens When Training Is Manually Completed?**

1. **Status Changed to "Completed"** immediately
2. **Timestamp Recorded** - Completion date/time
3. **Notes Captured** - Final observations
4. **Certificates Recorded** - If earned
5. **Moved to Past Training** - Historical record created

---

## Quick Reference Guide

| Need to...? | What to Do |
|-------------|-----------|
| Enroll soldier in course | Create New → Course → Select soldiers → Set dates |
| Assign cadre mentor | Create New → Cadre → Select mentor & student |
| Record exercise participation | Create New → Exercise Area → Select participants |
| Complete training early | Find training → Click Complete → Add notes |
| Mark multiple complete | Select multiple → Bulk Actions → Complete All |
| View training history | Click soldier name → View History tab |
| Edit active training | Find training → Click Edit → Modify details |
| Change training dates | Find training → Edit → Update dates |
| Delete training (pending) | Find training → Delete button |
| Export training records | Select trainings → Export button |

---

## Status Indicators

```
🔵 SCHEDULED   - Not yet started (before start date)
🟢 ACTIVE      - Currently in progress
✅ COMPLETED   - Finished and recorded
⏳ PENDING     - Awaiting completion verification
❌ CANCELLED   - Entry removed from active tracking
```

---

## Screenshots Placeholders

### [Screenshot 1: Training Module Dashboard]
Shows current, scheduled, and past training with statistics.

---

### [Screenshot 2: Create New Training Modal]
Shows options to choose training type (Course, Cadre, Exercise Area).

---

### [Screenshot 3: Course Assignment Form]
Shows form to select course, soldiers, dates, and details.

---

### [Screenshot 4: Active Training List]
Shows currently active trainings with soldier names and status.

---

### [Screenshot 5: Mark Complete Modal]
Shows completion form with notes and certification fields.

---

### [Screenshot 6: Training History Timeline]
Shows soldier's training journey with all completed trainings.

---

### [Screenshot 7: Bulk Operations Menu]
Shows checkboxes for multiple selection and bulk actions.

---

### [Screenshot 8: Edit Training Form]
Shows form to modify active training details.

---

### [Screenshot 9: Cadre Assignment Dashboard]
Shows mentorship relationships and active cadre assignments.

---

### [Screenshot 10: Exercise Area Participation]
Shows soldiers and exercises with dates and status.

---

## Common Use Cases

### **Use Case 1: New Soldier Onboarding**

1. Enroll in "New Soldier Orientation" course
2. Assign experienced cadre as mentor
3. Schedule exercises for practical training
4. Complete all three when period ends
5. Records show complete onboarding

---

### **Use Case 2: Professional Development Program**

1. Enroll soldiers in leadership course
2. Assign senior cadre for mentorship
3. Schedule field exercises for practice
4. Evaluation and completion
5. Document for promotion consideration

---

### **Use Case 3: Quarterly Fitness Exercise**

1. Schedule exercise area participation
2. enroll all required soldiers
3. Record participation dates
4. Mark complete after exercise
5. Generate participation report

---

### **Use Case 4: Certification Program**

1. Enroll soldiers in special skills course
2. Assign qualified cadre instructor
3. Track progress over multiple weeks
4. Complete with certification earned
5. Soldier now qualified for specific duty

---

## Best Practices

✓ **Do:**
- Assign trainings well in advance
- Set realistic end dates
- Use recommendations meaningfully
- Document training completion promptly
- Keep training history for audit
- Monitor training progress
- Complete training on time

✗ **Don't:**
- Assign future training beyond reasonable date
- Leave training incomplete indefinitely
- Ignore automatic status changes
- Delete completed training records
- Edit past completed trainings
- Assign unqualified cadre
- Skip documentation

---

## Frequently Asked Questions

### **Q: Can a soldier take multiple courses at once?**
A: Generally one at a time to avoid conflicts, but system allows multiple if dates don't overlap.

---

### **Q: What happens if course dates pass but training isn't complete?**
A: Status automatically becomes "Completed" on end date. Manually update if needed to adjust completion.

---

### **Q: Can I edit a training assignment after it starts?**
A: Yes, you can edit dates, notes, and details while active. Cannot edit completed trainings.

---

### **Q: How do recommendations work?**
A: Link recommendations to training (e.g., "Physical Fitness Required"). System tracks if recommendation is fulfilled.

---

### **Q: Can deleted training be recovered?**
A: No, deletion is permanent. Better to mark complete and keep in history instead.

---

### **Q: How often does the system update training status?**
A: Status updates are checked daily. Automatic status changes happen overnight.

---

### **Q: Can multiple cadre be assigned to one training?**
A: Typically one lead cadre per assignment, but notes can reference supporting cadre.

---

## Troubleshooting

### **Issue: Training status not updating**
- Verify dates are set correctly
- System updates daily (check tomorrow if just created)
- Manually complete if needed

---

### **Issue: Cannot assign soldier to training**
- Verify soldier is active (not deleted/inactive)
- Check if soldier already in training during same period
- Confirm training exists in system

---

### **Issue: Cannot find past training**
- Go to "Past Training" or "History" section
- Use filters to narrow list
- May need to search by date range

---

### **Issue: Bulk complete showing errors**
- Some trainings might have conflicts
- Complete individually to see specific issue
- Contact administrator if persistent

---

## Additional Resources

- [Settings Page Guide](SETTINGS_PAGE_GUIDE.md) - Create courses, cadre, exercise areas
- [Soldier Management Guide](SOLDIER_MANAGEMENT_GUIDE.md) - Manage soldier qualifications
- [Reports and Analytics Guide](REPORTS_AND_ANALYTICS_GUIDE.md) - Training reports

---

## Support

For issues or questions:
1. Check FAQ section above
2. Review step-by-step workflows
3. Contact your system administrator

