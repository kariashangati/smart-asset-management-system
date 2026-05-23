# Department-Based Manager & Asset Assignment Implementation Plan

## Overview
This document outlines a safe, phased approach to add department assignment for managers and assets throughout the admin panel.

**Current State:**
- ✅ Assets already have `department_id` 
- ✅ Departments table exists
- ✅ Users exist with roles (admin, asset_manager)
- ❌ Users don't have `department_id` assignment
- ❌ Managers can see all assets (no filtering)

**Goal:**
- Managers belong to a department
- Managers can only manage assets from their department
- All admin operations remain accessible to admins
- Safe data integrity with proper foreign keys

---

## PHASE 1: Database & Model Layer
### Objective
Add database structure for manager-to-department relationship and create model relationships.

### Files to CREATE
1. **`database/migrations/YYYY_MM_DD_HHMMSS_add_department_id_to_users_table.php`** (NEW)
   - Add `department_id` column to users table
   - Add foreign key constraint
   - Make nullable for admin users

### Files to UPDATE
1. **`app/Models/User.php`**
   - Add `department_id` to $fillable
   - Add relationship: `department()` (belongsTo Department)
   - Add method: `belongsToDepartment()` to check if user has department
   - Add method: `isDepartmentManager()` to check if asset_manager role

2. **`app/Models/Department.php`**
   - Add relationship: `managers()` (hasMany User) 
   - Add relationship: `assets()` (hasMany Asset)

3. **`app/Models/Asset.php`** (Minor update)
   - Already has department relationship ✓
   - Add method: `belongsToDepartment($departmentId)` helper

### Files to STAY UNCHANGED
- `database/migrations/*` (existing migrations - DO NOT MODIFY)
- `app/Models/AssetCategory.php`
- `app/Models/TrackerDevice.php`

### Schema Details

**Migration: add_department_id_to_users_table**
```sql
-- Add department_id column (nullable for admin)
ALTER TABLE users ADD COLUMN department_id BIGINT UNSIGNED NULLABLE AFTER phone;

-- Add foreign key constraint
ALTER TABLE users ADD CONSTRAINT users_department_id_foreign 
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL;

-- Add index for performance
ALTER TABLE users ADD INDEX users_department_id_index (department_id);
```

**Constraints & Rules:**
- Admin users: `department_id = NULL` (have access to all)
- Managers: `department_id = their_department` (required)
- Cascade behavior: SET NULL on department delete (manager loses department but stays in system)

---

## PHASE 2: Request Validation & Business Logic
### Objective
Add validation rules and service layer logic for department assignment.

### Files to CREATE
1. **`app/Services/DepartmentService.php`** (NEW)
   - Validates manager can only be assigned to existing department
   - Validates admin users cannot have department_id
   - Handles manager-department assignment logic

2. **`app/Policies/AssetPolicy.php`** (NEW)
   - Define authorization rules for asset operations
   - Method: `view()` - manager sees only their dept assets
   - Method: `create()` - manager can create in their dept only
   - Method: `update()` - manager can edit only their dept assets
   - Method: `delete()` - manager can delete only their dept assets

### Files to UPDATE
1. **`app/Http/Requests/StoreUserRequest.php`**
   - Add validation rule: `department_id` (required if role is asset_manager)
   - Add validation rule: department must exist
   - Add custom message for validation

2. **`app/Http/Requests/UpdateUserRequest.php`**
   - Add same validation as StoreUserRequest
   - Allow clearing department if changing from manager to another role

3. **`app/Http/Controllers/Admin/UserController.php`**
   - Update `index()` to load departments
   - Update `store()` to use DepartmentService validation
   - Update `update()` to handle department changes
   - Add method: `validateDepartmentAccess()` (internal helper)

4. **`app/Http/Controllers/Admin/AssetController.php`**
   - Add method: `applyDepartmentFilter()` to index query
   - Update `index()` to filter by manager's department if user is manager
   - Update `create()` to set default department for managers
   - Add authorization policy check

5. **`app/Services/UserService.php`** (if exists, update)
   - Update `create()` method to include department_id handling
   - Update `update()` method to handle department changes
   - Add validation: manager must have department

### Files to STAY UNCHANGED
- `app/Models/User.php` (relationships only, done in Phase 1)
- `routes/web.php` (done in Phase 3)

### Validation Rules

**StoreUserRequest & UpdateUserRequest:**
```php
'department_id' => [
    Rule::requiredIf(fn () => $this->input('role') === 'asset_manager'),
    'nullable',
    'exists:departments,id'
],
```

**DepartmentService Validation:**
- If role = asset_manager AND department_id = null → Error
- If role = admin AND department_id != null → Warn (will be ignored)
- If department_id provided but doesn't exist → Error

---

## PHASE 3: Admin Panel UI & Security
### Objective
Update all admin views and add access control policies.

### Files to CREATE
None (all updates to existing files)

### Files to UPDATE - Views

1. **`resources/views/admin/users/index.blade.php`**
   - Add "Department" column in users table (show department name)
   - Update create modal:
     - Add department dropdown field (required for managers)
     - Hide department field for admin users via JavaScript
     - Add helper text: "Required for Asset Managers"
   - Update edit modal:
     - Show current department
     - Allow changing department for managers
     - Show warning if changing from manager to admin (department will be ignored)
   - Add permission check: `@can('users.view')`

2. **`resources/views/admin/assets/index.blade.php`**
   - Add department filter dropdown (for admins)
   - For managers: show only their department in asset list
   - Add visual indicator: "Department: [Name]" badge on each asset row
   - Add JavaScript: disable department dropdown for managers in create/edit modal

3. **`resources/views/admin/assets/create.blade.php`** (if separate file)
   - For managers: auto-select their department
   - Disable department field for managers (show as read-only)
   - Show message: "Assets are assigned to your department by default"

4. **`resources/views/admin/assets/edit.blade.php`** (if separate file)
   - For managers: show their department (read-only)
   - For admins: show all departments (editable)

5. **`resources/views/admin/dashboard.blade.php`**
   - Update dashboard for managers:
     - Show only their department's assets
     - Update stats: "Your Department's Assets"
     - Add department name display

6. **`resources/views/admin/departments/index.blade.php`**
   - Add "Assigned Managers" column showing manager count
   - Add link to view managers in each department (optional nice-to-have)

### Files to UPDATE - Controllers & Middleware

1. **`app/Http/Controllers/Admin/UserController.php`** (Additional)
   - Update `index()`:
     ```php
     $departments = Department::all();
     return view('admin.users.index', compact('users', 'roles', 'departments'));
     ```
   - Add to all methods:
     ```php
     $this->authorize('users.update', auth()->user());
     ```

2. **`app/Http/Controllers/Admin/AssetController.php`** (Additional)
   - Update `index()`:
     ```php
     $query = Asset::with(['category', 'department', ...]);
     
     if (auth()->user()->isDepartmentManager()) {
         $query->where('department_id', auth()->user()->department_id);
     }
     
     $assets = $query->get();
     ```
   - Update `create()` & `edit()`:
     ```php
     if (auth()->user()->isDepartmentManager()) {
         $departments = auth()->user()->department()->get();
     } else {
         $departments = Department::all();
     }
     ```

3. **`app/Http/Controllers/Admin/DashboardController.php`** (Update)
   - Update queries to filter by department if user is manager:
     ```php
     $assetsQuery = Asset::query();
     if (auth()->user()->isDepartmentManager()) {
         $assetsQuery->where('department_id', auth()->user()->department_id);
     }
     ```

### Files to UPDATE - Routes (Phase 3)

1. **`routes/web.php`**
   - Add policy middleware to asset routes:
     ```php
     Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])
         ->middleware(['permission:assets.delete', 'can:delete,asset'])
         ->name('assets.destroy');
     ```
   - Add similar for update/view routes

### Security Checklist for Phase 3

✅ All asset queries filtered by department for managers
✅ Asset creation defaults to manager's department
✅ Asset deletion blocked if not manager's department
✅ Department field read-only for managers in UI
✅ Dashboard stats filtered by department
✅ User table shows departments assigned
✅ Form validation prevents invalid department assignment

---

## Implementation Order (Safe Sequence)

### Phase 1 (Database & Models)
1. Create migration file
2. Update User model (relationships + methods)
3. Update Department model (relationships)
4. Update Asset model (helper methods)
5. ✅ Run migration: `php artisan migrate`
6. ✅ Test: Check database schema

### Phase 2 (Logic & Validation)
1. Create DepartmentService.php
2. Create AssetPolicy.php
3. Update StoreUserRequest.php
4. Update UpdateUserRequest.php
5. Update UserController.php
6. Update AssetController.php
7. ✅ Test: Create user with department assignment
8. ✅ Test: Update user role (department should update)

### Phase 3 (UI & Security)
1. Update user create/edit modals (views)
2. Update asset index view (show departments)
3. Update asset create/edit views
4. Update dashboard controller & view
5. Update routes with policies
6. Update department view (show manager count)
7. ✅ Test: Manager can only see their assets
8. ✅ Test: Admin can see all assets
9. ✅ Test: Manager cannot edit another department's asset

---

## Risk Management

### Rollback Plan
If any phase fails:
- **Phase 1 fails:** `php artisan migrate:rollback --step=1` (removes column)
- **Phase 2 fails:** Revert file changes (no database impact)
- **Phase 3 fails:** Clear views cache and revert blade files

### Data Integrity
- Existing admin users: `department_id = NULL` ✓
- Existing managers: Manual migration script (see below)
- Existing assets: Already have `department_id` ✓

### Migration Script (After Phase 1)
```php
// Optional: Run after migration to assign existing managers to a default department
// Example: Assign all managers to "ICT DEPARTMENT"

use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;

$ictDept = Department::where('code', 'ICT')->first();

if ($ictDept) {
    User::whereHas('roles', fn($q) => $q->where('name', 'asset_manager'))
        ->whereNull('department_id')
        ->update(['department_id' => $ictDept->id]);
}
```

---

## Testing Checklist

### Phase 1 Testing
- [ ] Migration runs without errors
- [ ] Check `users` table has `department_id` column
- [ ] Check foreign key constraint exists
- [ ] User model loads department relationship
- [ ] Department model loads managers relationship

### Phase 2 Testing
- [ ] Create user with manager role + department → Success
- [ ] Create user with admin role + department → Warning (ignored)
- [ ] Create user with manager role without department → Error
- [ ] Update manager to different department → Success
- [ ] Asset policy allows manager to view own dept assets
- [ ] Asset policy blocks manager from viewing other dept assets

### Phase 3 Testing
- [ ] Login as admin → See all assets in list
- [ ] Login as manager (ICT Dept) → See only ICT assets
- [ ] Login as manager → Create asset → Auto-assigns to their dept
- [ ] Login as manager → Cannot edit another dept's asset
- [ ] User creation modal shows department dropdown for managers
- [ ] Department column displays in user table
- [ ] Dashboard shows only manager's department assets

---

## Additional Enhancements (Optional - Post-Implementation)

1. **Audit Trail:** Track department assignment changes
   - Already have LogsActivity trait in models
   - Will automatically log changes ✓

2. **Department Reports:** Manager-specific reporting
   - Can run after Phase 3 complete

3. **Asset Transfer:** Allow admin to transfer assets between departments
   - Requires additional controller method

4. **Department-level Permissions:** More granular control
   - Requires additional permission rows in database

---

## Quick Reference: What Changes Where

```
PHASE 1: Database & Models
├── NEW: database/migrations/[timestamp]_add_department_id_to_users_table.php
├── UPDATE: app/Models/User.php
├── UPDATE: app/Models/Department.php
└── UPDATE: app/Models/Asset.php (minor)

PHASE 2: Logic & Validation
├── NEW: app/Services/DepartmentService.php
├── NEW: app/Policies/AssetPolicy.php
├── UPDATE: app/Http/Requests/StoreUserRequest.php
├── UPDATE: app/Http/Requests/UpdateUserRequest.php
├── UPDATE: app/Http/Controllers/Admin/UserController.php
├── UPDATE: app/Http/Controllers/Admin/AssetController.php
└── UPDATE: app/Services/UserService.php

PHASE 3: UI & Security
├── UPDATE: resources/views/admin/users/index.blade.php
├── UPDATE: resources/views/admin/assets/index.blade.php
├── UPDATE: resources/views/admin/assets/create.blade.php
├── UPDATE: resources/views/admin/assets/edit.blade.php
├── UPDATE: resources/views/admin/dashboard.blade.php
├── UPDATE: resources/views/admin/departments/index.blade.php
├── UPDATE: app/Http/Controllers/Admin/DashboardController.php
└── UPDATE: routes/web.php
```

---

## Next Steps

When ready, say: **"START PHASE 1"** and I will provide:
1. Complete migration file code
2. All model relationship code
3. Step-by-step commands to run
4. Verification commands to confirm success

This ensures safe, tested implementation! 🚀
