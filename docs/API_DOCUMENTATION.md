# API Documentation

## Overview

The Manpower Management System provides a comprehensive REST API for programmatic access to all system functions. The API uses JSON for request and response formats and implements token-based authentication via Laravel Sanctum.

**Base URL**: `https://yourdomain.com/api`  
**Authentication**: Bearer tokens (Sanctum)  
**Response Format**: JSON  
**API Version**: 1.0

---

## Table of Contents

1. [Authentication](#authentication)
2. [Error Handling](#error-handling)
3. [Common Patterns](#common-patterns)
4. [Soldiers Endpoints](#soldiers-endpoints)
5. [Duties Endpoints](#duties-endpoints)
6. [Duty Assignments Endpoints](#duty-assignments-endpoints)
7. [Leave Endpoints](#leave-endpoints)
8. [Settings Endpoints](#settings-endpoints)
9. [User Endpoints](#user-endpoints)
10. [Rate Limiting](#rate-limiting)
11. [Pagination](#pagination)
12. [Filtering & Searching](#filtering--searching)

---

## Authentication

### Obtaining API Token

#### Step 1: Login
```http
POST /login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Response**:
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "email_verified_at": "2024-03-01T10:00:00Z",
    "created_at": "2024-01-15T08:30:00Z"
  },
  "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYzAbCdEfGhIjKlMnOp"
}
```

#### Step 2: Use Token in Requests
Include the token in the `Authorization` header:

```bash
curl -H "Authorization: Bearer {token}" \
     https://yourdomain.com/api/soldiers
```

### Token Management

#### Create Multiple Tokens
```php
// Each device/client can have separate token
$token = auth()->user()->createToken('mobile-app')->plainTextToken;
$token = auth()->user()->createToken('web-app')->plainTextToken;
$token = auth()->user()->createToken('desktop-app')->plainTextToken;
```

#### Revoke Token
```http
POST /api/logout
Authorization: Bearer {token}
```

#### List All Tokens
```bash
curl -H "Authorization: Bearer {token}" \
     https://yourdomain.com/api/tokens
```

---

## Error Handling

### Standard Error Response

All errors follow this format:

```json
{
  "message": "Error description",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 201 | Created - Resource created |
| 204 | No Content - Success with no response body |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Missing/invalid token |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation failed |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Server Error - Internal error |

### Example Error Responses

#### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."],
    "email": ["The email must be a valid email address."]
  }
}
```

#### Authentication Error (401)
```json
{
  "message": "Unauthenticated."
}
```

#### Authorization Error (403)
```json
{
  "message": "This action is unauthorized."
}
```

#### Not Found Error (404)
```json
{
  "message": "Resource not found."
}
```

---

## Common Patterns

### Response Structure

Standard successful response:
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    // ... other fields
  }
}
```

Collection response:
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe"
    },
    {
      "id": 2,
      "name": "Jane Smith"
    }
  ],
  "pagination": {
    "total": 100,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7
  }
}
```

### Date Format

All dates and timestamps use ISO 8601 format:
```
2024-03-15T14:30:00Z
```

### Nullable Fields

Fields that can be null are indicated with `null` in examples:
```json
{
  "id": 1,
  "middle_name": null,  // Can be null
  "phone": "555-1234"
}
```

---

## Soldiers Endpoints

### List Soldiers

**GET** `/api/soldiers`

Returns paginated list of all soldiers.

#### Query Parameters
```
page=1                    # Page number (default: 1)
per_page=15              # Records per page (default: 15, max: 100)
sort=name                # Sort field (name, rank_id, company_id)
search=john              # Search by name or NRIC
rank_id=5                # Filter by rank
company_id=3             # Filter by company
status=active            # Filter by status (active, inactive)
```

#### Example Request
```bash
curl -H "Authorization: Bearer {token}" \
     "https://yourdomain.com/api/soldiers?page=1&per_page=15&rank_id=5&status=active"
```

#### Response
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "nric": "123-45-6789",
      "dob": "1990-05-15",
      "phone": "555-1234",
      "rank": {
        "id": 5,
        "name": "Lieutenant"
      },
      "company": {
        "id": 3,
        "name": "A Company"
      },
      "medical_category": {
        "id": 1,
        "name": "Medical Fit-A"
      },
      "status": "active",
      "created_at": "2024-01-15T08:30:00Z"
    }
  ],
  "pagination": {
    "total": 50,
    "per_page": 15,
    "current_page": 1,
    "last_page": 4,
    "links": {
      "first": "https://yourdomain.com/api/soldiers?page=1",
      "last": "https://yourdomain.com/api/soldiers?page=4",
      "next": "https://yourdomain.com/api/soldiers?page=2"
    }
  }
}
```

### Get Soldier Details

**GET** `/api/soldiers/{id}`

Returns complete soldier profile with all relationships.

#### Parameters
```
id (required)  # Soldier ID
```

#### Response
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "nric": "123-45-6789",
    "dob": "1990-05-15",
    "phone": "555-1234",
    "address": "123 Main St",
    "rank": {
      "id": 5,
      "name": "Lieutenant"
    },
    "company": {
      "id": 3,
      "name": "A Company"
    },
    "appointment": {
      "id": 2,
      "name": "Section Commander"
    },
    "medical_category": {
      "id": 1,
      "name": "Medical Fit-A"
    },
    "education": [
      {
        "id": 1,
        "name": "Bachelor of Science",
        "institution": "University Name"
      }
    ],
    "skills": [
      {
        "id": 5,
        "name": "Marksmanship"
      }
    ],
    "courses": [
      {
        "id": 2,
        "name": "Leadership Course",
        "completed_at": "2024-02-01"
      }
    ],
    "duties": [
      {
        "id": 1,
        "name": "Guard Duty",
        "assignment_date": "2024-03-15"
      }
    ],
    "status": "active",
    "created_at": "2024-01-15T08:30:00Z",
    "updated_at": "2024-03-10T14:20:00Z"
  }
}
```

### Create Soldier

**POST** `/api/soldiers`

Create a new soldier record. Use 4-step form via web UI preferably.

#### Request Body
```json
{
  "name": "Jane Smith",
  "nric": "987-65-4321",
  "dob": "1995-08-20",
  "phone": "555-5678",
  "address": "456 Oak Ave",
  "rank_id": 6,
  "company_id": 2,
  "appointment_id": 1,
  "medical_category_id": 1,
  "status": "active"
}
```

#### Response (201 Created)
```json
{
  "data": {
    "id": 2,
    "name": "Jane Smith",
    "nric": "987-65-4321",
    // ... other fields
    "created_at": "2024-03-15T14:30:00Z"
  }
}
```

### Update Soldier

**PUT/PATCH** `/api/soldiers/{id}`

Update an existing soldier record.

#### Request Body (partial update)
```json
{
  "phone": "555-9999",
  "medical_category_id": 2
}
```

#### Response
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "phone": "555-9999",
    "medical_category": {
      "id": 2,
      "name": "Medical Fit-B"
    },
    // ... other fields
    "updated_at": "2024-03-15T14:30:00Z"
  }
}
```

### Delete Soldier

**DELETE** `/api/soldiers/{id}`

Delete a soldier record. This is permanent.

#### Response (204 No Content)
```
(empty response)
```

### Soldier Search

**GET** `/api/soldiers/search`

Search soldiers by multiple criteria.

#### Query Parameters
```
query=john              # Search in name, NRIC, email
rank_id=5              # Filter by rank
company_id=3           # Filter by company
medical_id=1           # Filter by medical category
status=active          # Filter by status
```

#### Example
```bash
curl -H "Authorization: Bearer {token}" \
     "https://yourdomain.com/api/soldiers/search?query=john&rank_id=5"
```

---

## Duties Endpoints

### List Duties

**GET** `/api/duties`

Returns all duties with pagination.

#### Query Parameters
```
page=1
per_page=15
status=active
sort=name
```

#### Response
```json
{
  "data": [
    {
      "id": 1,
      "name": "Guard Duty",
      "description": "Main gate guard",
      "required_count": 5,
      "status": "active",
      "requirements": {
        "ranks": [5, 6],
        "skills": [1, 2]
      },
      "created_at": "2024-01-10T09:00:00Z"
    }
  ],
  "pagination": { }
}
```

### Get Duty Details

**GET** `/api/duties/{id}`

Returns complete duty information.

#### Response
```json
{
  "data": {
    "id": 1,
    "name": "Guard Duty",
    "description": "Main gate guard",
    "required_count": 5,
    "ranks": [
      {
        "id": 5,
        "name": "Lieutenant"
      },
      {
        "id": 6,
        "name": "Captain"
      }
    ],
    "skills": [
      {
        "id": 1,
        "name": "Marksmanship"
      }
    ],
    "assignments": [
      {
        "soldier_id": 1,
        "soldier_name": "John Doe",
        "assignment_date": "2024-03-15"
      }
    ],
    "status": "active",
    "created_at": "2024-01-10T09:00:00Z"
  }
}
```

### Create Duty

**POST** `/api/duties`

Create a new duty.

#### Request Body
```json
{
  "name": "Training Exercise",
  "description": "Field training exercise",
  "required_count": 10,
  "rank_ids": [5, 6, 7],
  "skill_ids": [1, 2],
  "status": "active"
}
```

### Update Duty

**PUT/PATCH** `/api/duties/{id}`

Update duty details.

### Delete Duty

**DELETE** `/api/duties/{id}`

Delete duty.

---

## Duty Assignments Endpoints

### Assign Soldier to Duty

**POST** `/duty-assignments/assign-soldier`

Assign a soldier to a duty.

#### Request Body
```json
{
  "duty_id": 1,
  "soldier_id": 5,
  "date": "2024-03-20"
}
```

#### Response
```json
{
  "success": true,
  "message": "Soldier assigned successfully",
  "data": {
    "id": 15,
    "duty_id": 1,
    "soldier_id": 5,
    "assignment_date": "2024-03-20",
    "status": "assigned"
  }
}
```

### Bulk Assign Soldiers

**POST** `/duty-assignments/assign-range`

Assign multiple soldiers to duties for a date range.

#### Request Body
```json
{
  "duty_id": 1,
  "soldier_ids": [1, 2, 3, 4, 5],
  "start_date": "2024-03-20",
  "end_date": "2024-03-25"
}
```

### Reassign Soldier

**POST** `/duty-assignments/reassign`

Change soldier assignment to different duty.

#### Request Body
```json
{
  "assignment_id": 15,
  "new_soldier_id": 8,
  "date": "2024-03-20"
}
```

### Cancel Assignment

**POST** `/duty-assignments/cancel`

Cancel a duty assignment.

#### Request Body
```json
{
  "assignment_id": 15
}
```

### Get Duty Statistics

**GET** `/duty-assignments/statistics`

Get statistics about duty assignments.

#### Query Parameters
```
start_date=2024-03-01
end_date=2024-03-31
duty_id=1
```

#### Response
```json
{
  "data": {
    "total_assignments": 150,
    "completed_assignments": 145,
    "pending_assignments": 5,
    "cancelled_assignments": 2,
    "average_soldiers_per_duty": 5,
    "completion_rate": 96.7,
    "by_duty": {
      "1": {
        "total": 50,
        "completed": 48,
        "pending": 2
      }
    }
  }
}
```

### Get Available Soldiers

**GET** `/duty-assignments/available-soldiers`

Get soldiers available for assignment on a specific date.

#### Query Parameters
```
date=2024-03-20       # Required
duty_id=1             # Optional - filter by duty requirements
rank_id=5             # Optional - filter by rank
```

#### Response
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "rank": "Lieutenant",
      "company": "A Company",
      "medical_fit": true,
      "has_skills": true
    }
  ]
}
```

---

## Leave Endpoints

### Submit Leave Application

**POST** `/leave/submit`

Submit a new leave application.

#### Request Body
```json
{
  "leave_type_id": 1,
  "start_date": "2024-04-01",
  "end_date": "2024-04-05",
  "reason": "Personal reasons"
}
```

#### Response
```json
{
  "success": true,
  "data": {
    "id": 25,
    "leave_type": "Annual Leave",
    "start_date": "2024-04-01",
    "end_date": "2024-04-05",
    "days": 5,
    "status": "pending",
    "created_at": "2024-03-15T14:30:00Z"
  }
}
```

### Get Leave Applications

**GET** `/api/leave`

Get user's leave applications.

#### Query Parameters
```
status=pending     # Filter by status
sort=created_at   # Sort field
```

### Approve Leave

**POST** `/leave/approval/{id}`

Approve a leave application (manager only).

#### Request Body
```json
{
  "action": "approve",
  "remarks": "Approved"
}
```

### Reject Leave

**POST** `/leave/approval/{id}`

Reject a leave application.

#### Request Body
```json
{
  "action": "reject",
  "remarks": "Staff required at that time"
}
```

---

## Settings Endpoints

### Get System Settings

**GET** `/api/settings`

Get all system configuration settings.

#### Response
```json
{
  "data": {
    "app_name": "Manpower Management System",
    "leave_year_start": "01-01",
    "working_days": 5,
    "default_leave_quota": 30,
    "max_consecutive_leave": 14
  }
}
```

### Update Settings

**PUT** `/api/settings`

Update system settings (admin only).

#### Request Body
```json
{
  "default_leave_quota": 35,
  "max_consecutive_leave": 21
}
```

### Get Ranks

**GET** `/api/ranks`

Get all military ranks.

#### Response
```json
{
  "data": [
    {
      "id": 1,
      "name": "General"
    },
    {
      "id": 2,
      "name": "Colonel"
    }
  ]
}
```

### Get Companies

**GET** `/api/companies`

Get all companies.

### Get Leave Types

**GET** `/api/leave-types`

Get all leave types.

### Get Medical Categories

**GET** `/api/medical-categories`

Get all medical categories.

### Get Skills

**GET** `/api/skills`

Get all skills with categories.

---

## User Endpoints

### Get Current User

**GET** `/api/user`

Get authenticated user's information.

#### Response
```json
{
  "data": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "roles": ["admin"],
    "permissions": ["create_soldier", "edit_soldier", "delete_soldier"]
  }
}
```

### Create User (Admin)

**POST** `/api/users`

Create new system user (admin only).

#### Request Body
```json
{
  "name": "New User",
  "email": "user@example.com",
  "password": "secure_password",
  "role_id": 2
}
```

### List Users (Admin)

**GET** `/api/users`

List all system users (admin only).

### Update User

**PUT** `/api/users/{id}`

Update user information.

### Delete User (Admin)

**DELETE** `/api/users/{id}`

Delete user account.

---

## Rate Limiting

The API implements rate limiting to prevent abuse.

### Limits
- **Authenticated Users**: 60 requests per minute
- **Unauthenticated**: 10 requests per minute
- **Bulk Operations**: 5 requests per minute

### Rate Limit Headers

All responses include rate limit information:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1710515400
```

### Rate Limit Exceeded

When limit is exceeded (429 response):

```json
{
  "message": "Too many requests. Please try again in 60 seconds.",
  "retry_after": 60
}
```

---

## Pagination

### Standard Pagination

All list endpoints support pagination:

#### Query Parameters
```
page=1           # Current page (default: 1)
per_page=15      # Records per page (default: 15, max: 100)
```

#### Pagination Response
```json
{
  "data": [ ... ],
  "pagination": {
    "total": 250,
    "per_page": 15,
    "current_page": 1,
    "last_page": 17,
    "from": 1,
    "to": 15,
    "links": {
      "first": "https://yourdomain.com/api/soldiers?page=1",
      "last": "https://yourdomain.com/api/soldiers?page=17",
      "prev": null,
      "next": "https://yourdomain.com/api/soldiers?page=2"
    }
  }
}
```

### Cursor Pagination (Optional)

For better performance with large datasets:

```bash
curl "https://yourdomain.com/api/soldiers?cursor=nextpage&per_page=50"
```

---

## Filtering & Searching

### Query Operators

Supported filtering operators:

```
name=john              # Exact match
name:like=john%       # LIKE operator
age:gte=25            # Greater than or equal
age:lte=65            # Less than or equal
status:in=active,pending  # IN operator
created_at:after=2024-01-01  # Date comparison
```

### Search Example

```bash
curl "https://yourdomain.com/api/soldiers?name:like=john%&rank_id=5&status=active&sort=-created_at"
```

### Sorting

Specify sort order with field name:

```
sort=name             # Ascending
sort=-created_at      # Descending (prefix with -)
```

---

## Batch Operations

### Batch Create

**POST** `/api/soldiers/batch`

Create multiple soldiers in one request.

#### Request Body
```json
{
  "soldiers": [
    {
      "name": "John Doe",
      "nric": "123-45-6789",
      "rank_id": 5,
      "company_id": 1
    },
    {
      "name": "Jane Smith",
      "nric": "987-65-4321",
      "rank_id": 6,
      "company_id": 2
    }
  ]
}
```

#### Response
```json
{
  "data": {
    "created": 2,
    "failed": 0,
    "results": [ ... ]
  }
}
```

---

## Webhooks (Optional)

Subscribe to events:

### Subscribe to Event

**POST** `/api/webhooks`

```json
{
  "event": "soldier.created",
  "url": "https://yourapp.com/webhook"
}
```

### Events Available
- `soldier.created`
- `soldier.updated`
- `soldier.deleted`
- `duty.assigned`
- `duty.completed`
- `leave.approved`
- `leave.rejected`

---

## Code Examples

### JavaScript/Axios

```javascript
// Get soldiers
axios.get('/api/soldiers', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
}).then(response => {
  console.log(response.data);
});

// Create soldier
axios.post('/api/soldiers', {
  name: 'John Doe',
  nric: '123-45-6789',
  rank_id: 5
}, {
  headers: {
    'Authorization': `Bearer ${token}`
  }
}).then(response => {
  console.log('Created:', response.data);
});
```

### Python/Requests

```python
import requests

token = 'your-token-here'
headers = {'Authorization': f'Bearer {token}'}

# Get soldiers
response = requests.get('https://yourdomain.com/api/soldiers', headers=headers)
soldiers = response.json()

# Create soldier
soldier_data = {
    'name': 'John Doe',
    'nric': '123-45-6789',
    'rank_id': 5,
    'company_id': 1
}
response = requests.post('https://yourdomain.com/api/soldiers', 
                        json=soldier_data, headers=headers)
print(response.json())
```

### cURL

```bash
# Login
curl -X POST https://yourdomain.com/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'

# Get soldiers with token
curl -H "Authorization: Bearer {token}" \
     https://yourdomain.com/api/soldiers

# Create soldier
curl -X POST https://yourdomain.com/api/soldiers \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "nric": "123-45-6789",
    "rank_id": 5,
    "company_id": 1
  }'
```

---

## Troubleshooting

### 401 Unauthorized

- Token is missing or invalid
- Token has expired
- Solution: Obtain a new token via login

### 403 Forbidden

- User doesn't have permission for this action
- Solution: Assign correct role/permissions

### 422 Unprocessable Entity

- Validation failed
- Check error response for detailed validation messages
- Ensure all required fields are provided

### 429 Too Many Requests

- Rate limit exceeded
- Wait for `Retry-After` duration before retrying

---

**Last Updated**: March 2026  
**API Version**: 1.0  
**Status**: Production Ready
