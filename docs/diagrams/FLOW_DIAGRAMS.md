# SMART TOURISM ADMIN PANEL - FLOW DIAGRAMS

## 1. AUTHENTICATION FLOW

```
┌─────────────────────────────────────────────────────────────────────┐
│                     ADMIN LOGIN PROCESS                             │
└─────────────────────────────────────────────────────────────────────┘

START
  ↓
UNAUTHENTICATED?
  ├─ YES → SHOW LOGIN FORM
  │         User enters email & password
  │         Optional: Remember Me checkbox
  │         ↓
  │         SUBMIT LOGIN FORM (POST /admin/login)
  │         ↓
  │         VALIDATE INPUT
  │         ├─ Invalid → SHOW ERRORS, Return to form
  │         └─ Valid → Continue
  │         ↓
  │         CHECK EMAIL EXISTS
  │         ├─ NO → LOG FAILED ATTEMPT, Show error
  │         └─ YES → Continue
  │         ↓
  │         CHECK PASSWORD MATCHES (Hash::check)
  │         ├─ NO → LOG FAILED ATTEMPT, Rate limit, Show error
  │         └─ YES → Continue
  │         ↓
  │         CHECK IS_ACTIVE = TRUE
  │         ├─ NO → Show "Account disabled"
  │         └─ YES → Continue
  │         ↓
  │         UPDATE last_login_at = NOW()
  │         ↓
  │         CREATE SESSION (auth('admin')->login)
  │         ↓
  │         IF REMEMBER_ME → CREATE PERSISTENT COOKIE (30 days)
  │         ↓
  │         REDIRECT TO /admin/dashboard
  │         ↓
  │         SHOW "Welcome back, [Name]" Toast
  │
  └─ NO → REDIRECT TO /admin/dashboard
         ↓
         LOAD DASHBOARD
         DISPLAY NAVIGATION SIDEBAR
         SHOW ROLE-SPECIFIC CONTENT
         
END
```

---

## 2. PASSWORD RESET FLOW

```
┌───────────────────────────────────────────────────────┐
│         ADMIN PASSWORD RESET PROCESS                  │
└───────────────────────────────────────────────────────┘

START
  ↓
USER CLICKS "Forgot Password"
  ↓
SHOW EMAIL FORM
User enters email
  ↓
SUBMIT EMAIL (POST /admin/forgot-password)
  ↓
FIND ADMIN BY EMAIL
  ├─ NOT FOUND → Show generic message (security)
  │              "If email exists, link will be sent"
  └─ FOUND → Continue
     ↓
     GENERATE RESET TOKEN (random 60 char)
     HASH TOKEN
     STORE TO password_reset_tokens TABLE
     SET expiry = NOW() + 1 hour
     ↓
     SEND EMAIL WITH RESET LINK
     Email contains: /admin/reset-password/{token}
     ↓
     SHOW SUCCESS MESSAGE
     "Check your email for reset instructions"

USER CLICKS EMAIL LINK
  ↓
GET /admin/reset-password/{token}
  ↓
VERIFY TOKEN
  ├─ NOT FOUND → Show error "Invalid or expired"
  ├─ EXPIRED (> 1 hour) → Show error "Link expired"
  └─ VALID → Continue
     ↓
     SHOW PASSWORD RESET FORM
     User enters new password + confirmation
     ↓
     SUBMIT FORM (POST /admin/reset-password)
     ↓
     VALIDATE PASSWORD
     ├─ Not matching → Show error
     ├─ Too weak → Show error
     │  (Must have: 8+ chars, 1 uppercase, 1 number, 1 special)
     └─ Valid → Continue
     ↓
     VERIFY TOKEN AGAIN
     ├─ Invalid → Show error
     └─ Valid → Continue
     ↓
     UPDATE admin.password = Hash(new_password)
     DELETE token from password_reset_tokens
     ↓
     SHOW SUCCESS
     "Password reset. Please login."
     REDIRECT TO /admin/login

END
```

---

## 3. DESTINATION CRUD FLOW

```
┌──────────────────────────────────────────────────────────────┐
│      DESTINATION MANAGEMENT - COMPLETE CRUD FLOW             │
└──────────────────────────────────────────────────────────────┘

═════════════════════════════════════════════════════════════════
║                        CREATE FLOW                           ║
═════════════════════════════════════════════════════════════════

ADMIN CLICKS "Add Destination"
  ↓
MIDDLEWARE CHECKS
├─ Authenticated? NO → Redirect to login
├─ Role = admin|super_admin? NO → Show 403
└─ Permission = create_destination? NO → Show 403
  ↓
GET /admin/destinations/create
  ↓
LOAD CREATE FORM
Show fields:
├─ Name (required)
├─ Description (required)
├─ Long Description (optional)
├─ Category (dropdown: park, beach, museum, etc.)
├─ Latitude/Longitude (required)
├─ Thumbnail image (required)
├─ Cover image (required)
└─ Rating (optional)
  ↓
ADMIN FILLS FORM & SUBMITS
  ↓
POST /admin/destinations
  ↓
VALIDATE ALL FIELDS
├─ Name: required, 3-200 chars
├─ Description: required, 10-500 chars
├─ Category: required, in enum
├─ Coordinates: required, valid
├─ Images: required, jpeg/png/webp, max 5MB
└─ Rating: optional, 1-5 numeric
  ↓
Validation passed?
├─ NO → Return with errors, show form again
└─ YES → Continue

PROCESS IMAGES
├─ Thumbnail: Resize to 400x300 px
├─ Cover: Resize to 1200x600 px
├─ Compress with quality 80%
├─ Save to storage/destinations/{random_id}/
└─ Generate S3/CDN URLs

CREATE DESTINATION RECORD
INSERT INTO destinations (
  name, slug, description, long_description,
  category, latitude, longitude,
  thumbnail_url, cover_url,
  admin_id, is_active, created_at, updated_at
)
  ↓
LOG AUDIT TRAIL
INSERT INTO admin_activity_logs (
  admin_id, action='create', entity_type='destination',
  new_values={...}, ip_address, user_agent, created_at
)
  ↓
RETURN SUCCESS
Show toast: "Destination created successfully"
REDIRECT TO /admin/destinations/{id}/edit (for adding gallery)

═════════════════════════════════════════════════════════════════
║                        READ FLOW                             ║
═════════════════════════════════════════════════════════════════

GET /admin/destinations
  ↓
MIDDLEWARE CHECK (auth:admin)
  ↓
APPLY FILTERS
├─ Search (name, description)
├─ Category filter
├─ Status filter (active/inactive)
└─ Featured filter

QUERY DESTINATIONS
SELECT * FROM destinations
WHERE is_active = true  [AND other filters]
WITH galleries, facilities
ORDER BY created_at DESC
LIMIT 15 (paginated)
  ↓
LOAD STATISTICS
├─ Total active destinations
├─ Pending reviews count
├─ Featured destinations
└─ Recent activity

RENDER TABLE
Show columns:
├─ Image (thumbnail)
├─ Name
├─ Category
├─ Rating
├─ Status
├─ Created by
├─ Actions (Edit, Delete, View)
└─ Pagination controls

═════════════════════════════════════════════════════════════════
║                        UPDATE FLOW                           ║
═════════════════════════════════════════════════════════════════

GET /admin/destinations/{id}/edit
  ↓
LOAD DESTINATION WITH RELATIONS
├─ Gallery images
├─ Facilities
└─ Review count

POPULATE FORM WITH CURRENT VALUES
  ↓
ADMIN MODIFIES FIELDS
Optional: Upload new images
  ↓
PUT /admin/destinations/{id}
  ↓
VALIDATE (same as create)
  ↓
STORE OLD VALUES FOR AUDIT
old_values = destination.toArray()
  ↓
UPDATE IMAGES (if changed)
├─ DELETE old image from storage
├─ UPLOAD new image
└─ UPDATE URL in database

UPDATE RECORD
UPDATE destinations SET
  name, description, category, coordinates,
  thumbnail_url, cover_url, updated_at
WHERE id = {id}
  ↓
CALCULATE CHANGES
Compare old_values vs new_values
Generate changes_log = {field: {old, new}, ...}
  ↓
LOG AUDIT TRAIL
INSERT INTO admin_activity_logs (
  action='update',
  old_values, new_values, changes,
  ...
)
  ↓
CACHE INVALIDATION
Cache::forget("destination.{id}")
Cache::forget("destinations.list")
  ↓
RETURN SUCCESS
Show toast: "Destination updated"

═════════════════════════════════════════════════════════════════
║                        DELETE FLOW                           ║
═════════════════════════════════════════════════════════════════

ADMIN CLICKS DELETE ICON ON DESTINATION
  ↓
CONFIRMATION MODAL APPEARS
├─ Show warning if has active reviews/reports
└─ "Are you sure? This action cannot be undone"

ADMIN CONFIRMS
  ↓
DELETE /admin/destinations/{id}
  ↓
PERMISSION CHECK (delete_destination)
  ↓
PERFORM SOFT DELETE
├─ Check for active reviews/reports
│  └─ If exists: delete those first
├─ SET deleted_at = NOW()
├─ Keep data in DB for recovery
└─ Data hidden from public app

DELETE ASSOCIATED FILES
├─ Gallery images
├─ Thumbnail
├─ Cover image
└─ From storage & CDN

CHECK RELATED DATA
├─ Trips containing this destination
├─ Wishlist items
├─ Analytics data (keep for historical)
└─ Reviews (mark as deleted if active)

LOG AUDIT TRAIL
INSERT INTO admin_activity_logs (
  action='soft_delete',
  reason='Admin deletion',
  recoverable_until=NOW() + 30 days,
  ...
)
  ↓
SCHEDULE PERMANENT DELETE (30 days later)
  ↓
RETURN SUCCESS
Toast: "Destination deleted (recoverable)"
Remove from table
  ↓
CACHE INVALIDATION
Cache::forget("destinations.*")

═════════════════════════════════════════════════════════════════
║                   GALLERY MANAGEMENT                         ║
═════════════════════════════════════════════════════════════════

IN EDIT PAGE, SCROLL TO GALLERY SECTION
  ↓
SHOW EXISTING IMAGES (with delete buttons)
Show upload area with drag-drop
  ↓
ADMIN UPLOADS IMAGES
Validation: max 10 per destination
  ↓
POST /admin/destinations/{id}/gallery
  ↓
PROCESS EACH IMAGE
├─ Resize to 1200x800 (main)
├─ Resize to 400x300 (thumbnail)
├─ Optimize & compress
└─ Save both versions

INSERT INTO destination_galleries
(destination_id, image_url, thumb_url, order)
  ↓
LOG ACTION
  ↓
RETURN IMAGE LIST with delete/order buttons
  ↓
ADMIN CAN:
├─ Delete image
├─ Reorder using drag handles
└─ Set as primary (featured)

END
```

---

## 4. REVIEW MODERATION FLOW

```
┌────────────────────────────────────────────────────┐
│      REVIEW MODERATION PROCESS                     │
└────────────────────────────────────────────────────┘

PENDING REVIEW CREATED
(User posts review from app)
  ↓
REVIEW STATUS = 'pending'
INSERT INTO reviews (status='pending', ...)
  ↓
ADMIN NOTIFIED
├─ Badge on sidebar
├─ Notification in navbar
└─ Email to moderator (if configured)

MODERATOR CLICKS "Reviews"
  ↓
GET /admin/reviews?status=pending
  ↓
SHOW REVIEWS TABLE
├─ Filters: status (pending/approved/rejected)
├─ Filters: destination
├─ Sort: newest first
└─ Pagination

MODERATOR CLICKS REVIEW ROW
  ↓
GET /admin/reviews/{id}
  ↓
SHOW REVIEW DETAIL
├─ User name
├─ Destination name (with link)
├─ Rating (stars)
├─ Title & content
├─ Images (if uploaded)
├─ Posted date/time
├─ Report count (if flagged)
└─ Actions: Approve, Reject, Delete

MODERATOR REVIEWS CONTENT
  ↓
DECISION:
├─ APPROVE → PATCH /admin/reviews/{id}/approve
├─ REJECT → PATCH /admin/reviews/{id}/reject
└─ DELETE → DELETE /admin/reviews/{id}

IF APPROVE:
  UPDATE reviews SET status='approved', approved_by={admin_id}
  Destination rating includes this review
  LOG: action='approve', ...
  Toast: "Review approved"
  ↓
  Review appears in app

IF REJECT:
  UPDATE reviews SET status='rejected'
  LOG: action='reject', ...
  NOTIFY USER (optional)
  Toast: "Review rejected"

IF DELETE:
  Soft delete (deleted_at = NOW())
  LOG: action='soft_delete'
  Toast: "Review deleted"

RECALCULATE DESTINATION RATING
rating = AVG(rating) WHERE status='approved'
rating_count = COUNT(*) WHERE status='approved'

END
```

---

## 5. REPORT HANDLING FLOW

```
┌────────────────────────────────────────────────────┐
│         REPORT HANDLING WORKFLOW                   │
└────────────────────────────────────────────────────┘

USER REPORTS CONTENT
(from mobile app)
  ↓
INSERT INTO reports (
  user_id, reportable_type, reportable_id,
  reason, description, status='pending'
)
  ↓
ADMIN NOTIFIED
├─ Notification badge
└─ Email alert

ADMIN CLICKS "Reports"
  ↓
GET /admin/reports?status=pending
  ↓
FILTERS AVAILABLE
├─ Status: pending, investigating, resolved
├─ Type: destination, review, event
├─ Date range
└─ Reason (spam, inappropriate, etc.)

ADMIN SELECTS REPORT
  ↓
GET /admin/reports/{id}
  ↓
SHOW REPORT DETAIL
├─ Reporter info (limited)
├─ Reported content (destination/review/event)
├─ Reason for report
├─ Description
├─ Evidence (files/screenshots)
├─ Report date/time
└─ Previous reports on same content

ADMIN INVESTIGATES
├─ Review reported content
├─ Check evidence
├─ Look for patterns
└─ Decide action

TAKE ACTION:
├─ RESOLVE (no action needed)
│   PATCH /admin/reports/{id}/resolve
│   status = 'resolved'
│   action_taken = 'ignore'
│
├─ FLAG FOR FOLLOW-UP
│   PATCH /admin/reports/{id}/flag
│   Assign to another moderator
│
└─ TAKE ACTION (delete/warn)
    POST /admin/reports/{id}/action
    
    If DELETE CONTENT:
    ├─ DELETE the reported item (soft delete)
    ├─ NOTIFY user who posted it
    ├─ Keep evidence in database
    │  (reportable_type, reportable_id still set)
    └─ action_taken = 'delete_content'
    
    If WARN USER:
    ├─ Track warnings for user
    ├─ After 3 warnings → disable account
    └─ action_taken = 'warn_user'

LOG AUDIT TRAIL
├─ What was reported
├─ Action taken
├─ Reason for action
└─ Admin who handled it

NOTIFY AFFECTED PARTIES
├─ If content deleted: notify poster
├─ If user warned: notify user
├─ Public: no notification

UPDATE REPORT STATUS
status = 'resolved'
resolved_at = NOW()
reason_for_action = {...}

CACHE INVALIDATION
├─ Clear destination cache
├─ Clear analytics cache
└─ Update statistics

END
```

---

## 6. AUTHORIZATION & PERMISSION CHECK

```
┌─────────────────────────────────────────────────────┐
│         RBAC PERMISSION CHECK PROCESS               │
└─────────────────────────────────────────────────────┘

REQUEST RECEIVED
  ↓
MIDDLEWARE: auth:admin
  ├─ Is session valid? NO → Redirect to login
  └─ Is admin active? NO → Redirect to login
  ↓
MIDDLEWARE: role:admin,super_admin
  ├─ Is user role in allowed list?
  │  ├─ NO → Return 403 Forbidden
  │  └─ YES → Continue
  │
  └─ Check role.permissions
     ├─ If insufficient → Return 403
     └─ If sufficient → Continue
  ↓
MIDDLEWARE (optional): permission:create_destination
  ├─ Check permission directly
  ├─ If not granted → Return 403
  └─ If granted → Continue
  ↓
CONTROLLER ACTION
  ├─ Additional permission check (if needed)
  ├─ Additional business logic check
  └─ Execute action or return error

EXAMPLE: Create Destination

CHECK FLOW:
1. Is authenticated? ✓
2. Is admin? ✓
3. Role = 'admin'? ✓
4. Permission 'create_destination'? ✓
5. Can access resource? ✓
6. Can perform action? ✓
→ ALLOW

DENY EXAMPLE:
1. Is authenticated? ✓
2. Is admin? ✓
3. Role = 'moderator'? ✗ (required: admin|super_admin)
→ DENY - Return 403

END
```

---

## 7. DATABASE TRANSACTION FLOW

```
┌──────────────────────────────────────────────────────┐
│     DATABASE TRANSACTION PATTERN                     │
└──────────────────────────────────────────────────────┘

START TRANSACTION
  ↓
INSERT/UPDATE/DELETE DESTINATION
  ↓
IF ERROR OCCURS
├─ ROLLBACK all changes
├─ Return error to user
└─ Log exception
  ↓
IF SUCCESS
├─ COMMIT transaction
├─ INSERT audit log
├─ INVALIDATE cache
├─ DISPATCH events (if needed)
└─ RETURN success
  ↓
END TRANSACTION

EXAMPLE CODE:
```php
DB::beginTransaction();
try {
    $destination = Destination::create($data);
    AdminActivityLog::log('create', 'destination', $destination->id);
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

END
```

---

## 8. PAGINATION & FILTERING

```
┌──────────────────────────────────────────────────────┐
│     PAGINATION & FILTERING FLOW                      │
└──────────────────────────────────────────────────────┘

USER ACCESSES LIST PAGE
→ GET /admin/destinations

Query parameters:
├─ ?page=1 (current page)
├─ ?per_page=15 (items per page)
├─ ?search=keyword
├─ ?category=beach
├─ ?status=active
├─ ?sort=name
└─ ?order=asc

BUILD QUERY
$query = Destination::query();

If search:
  WHERE name LIKE %search%
     OR description LIKE %search%

If category:
  WHERE category = 'beach'

If status:
  WHERE is_active = true/false

Apply sort:
  ORDER BY {sort} {order}

PAGINATION
→ paginate(15)

Results: 15 items per page

Pagination links:
├─ First page (page=1)
├─ Previous page (page={current-1})
├─ Page numbers (1, 2, 3, ...)
├─ Next page (page={current+1})
└─ Last page (page={last})

USER CLICKS PAGE
→ GET /admin/destinations?page=2&search=...
  (Preserves other query params)
  ↓
FETCH PAGE 2 DATA
RENDER TABLE WITH NEW DATA
```

---

**These diagrams are implemented as:**
- Text-based flowcharts (ASCII)
- Decision trees
- Process flows
- Database transaction patterns
- Authorization hierarchies
- User action sequences

For visual versions, consider using:
- Draw.io / Excalidraw
- Mermaid.js
- PlantUML
- LucidChart
