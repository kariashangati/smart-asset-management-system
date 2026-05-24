-- ========================================================================
-- SMART ASSET MANAGEMENT SYSTEM - PERMISSIONS & ROLES SQL SETUP
-- Safe SQL: Creates permissions if they don't exist, assigns to roles
-- ========================================================================

-- ========================================================================
-- 1. CREATE PERMISSIONS (IF NOT EXIST)
-- ========================================================================

INSERT IGNORE INTO permissions (name, guard_name, created_at, updated_at) VALUES
-- Dashboards
('dashboard.admin.view', 'web', NOW(), NOW()),
('dashboard.manager.view', 'web', NOW(), NOW()),

-- Users
('users.view', 'web', NOW(), NOW()),
('users.create', 'web', NOW(), NOW()),
('users.update', 'web', NOW(), NOW()),
('users.delete', 'web', NOW(), NOW()),

-- Roles
('roles.view', 'web', NOW(), NOW()),
('roles.create', 'web', NOW(), NOW()),
('roles.update', 'web', NOW(), NOW()),
('roles.delete', 'web', NOW(), NOW()),
('roles.assign', 'web', NOW(), NOW()),

-- Departments
('departments.view', 'web', NOW(), NOW()),
('departments.create', 'web', NOW(), NOW()),
('departments.update', 'web', NOW(), NOW()),
('departments.delete', 'web', NOW(), NOW()),

-- Asset Categories
('asset_categories.view', 'web', NOW(), NOW()),
('asset_categories.create', 'web', NOW(), NOW()),
('asset_categories.update', 'web', NOW(), NOW()),
('asset_categories.delete', 'web', NOW(), NOW()),

-- Assets
('assets.view', 'web', NOW(), NOW()),
('assets.create', 'web', NOW(), NOW()),
('assets.update', 'web', NOW(), NOW()),
('assets.delete', 'web', NOW(), NOW()),

-- Devices
('devices.view', 'web', NOW(), NOW()),
('devices.create', 'web', NOW(), NOW()),
('devices.update', 'web', NOW(), NOW()),
('devices.delete', 'web', NOW(), NOW()),
('devices.assign', 'web', NOW(), NOW()),

-- Device Assignments
('assignments.view', 'web', NOW(), NOW()),
('assignments.create', 'web', NOW(), NOW()),
('assignments.update', 'web', NOW(), NOW()),
('assignments.delete', 'web', NOW(), NOW()),

-- Tracking
('tracking.live_map.view', 'web', NOW(), NOW()),
('tracking.history.view', 'web', NOW(), NOW()),

-- Geofences
('geofences.view', 'web', NOW(), NOW()),
('geofences.create', 'web', NOW(), NOW()),
('geofences.update', 'web', NOW(), NOW()),
('geofences.delete', 'web', NOW(), NOW()),

-- Alerts
('alerts.view', 'web', NOW(), NOW()),
('alerts.create', 'web', NOW(), NOW()),
('alerts.update', 'web', NOW(), NOW()),
('alerts.delete', 'web', NOW(), NOW()),
('alerts.resolve', 'web', NOW(), NOW()),

-- Reports
('reports.view', 'web', NOW(), NOW()),
('reports.create', 'web', NOW(), NOW()),
('reports.export', 'web', NOW(), NOW()),

-- Audit Logs
('audit_logs.view', 'web', NOW(), NOW()),

-- Settings
('settings.manage', 'web', NOW(), NOW());

-- ========================================================================
-- 2. GET PERMISSION IDS (FOR ROLE ASSIGNMENT)
-- ========================================================================

-- Store permission IDs in variables for use below
SET @dashboard_admin_view = (SELECT id FROM permissions WHERE name = 'dashboard.admin.view');
SET @dashboard_manager_view = (SELECT id FROM permissions WHERE name = 'dashboard.manager.view');
SET @users_view = (SELECT id FROM permissions WHERE name = 'users.view');
SET @users_create = (SELECT id FROM permissions WHERE name = 'users.create');
SET @users_update = (SELECT id FROM permissions WHERE name = 'users.update');
SET @users_delete = (SELECT id FROM permissions WHERE name = 'users.delete');
SET @roles_view = (SELECT id FROM permissions WHERE name = 'roles.view');
SET @roles_create = (SELECT id FROM permissions WHERE name = 'roles.create');
SET @roles_update = (SELECT id FROM permissions WHERE name = 'roles.update');
SET @roles_delete = (SELECT id FROM permissions WHERE name = 'roles.delete');
SET @roles_assign = (SELECT id FROM permissions WHERE name = 'roles.assign');
SET @departments_view = (SELECT id FROM permissions WHERE name = 'departments.view');
SET @departments_create = (SELECT id FROM permissions WHERE name = 'departments.create');
SET @departments_update = (SELECT id FROM permissions WHERE name = 'departments.update');
SET @departments_delete = (SELECT id FROM permissions WHERE name = 'departments.delete');
SET @asset_categories_view = (SELECT id FROM permissions WHERE name = 'asset_categories.view');
SET @asset_categories_create = (SELECT id FROM permissions WHERE name = 'asset_categories.create');
SET @asset_categories_update = (SELECT id FROM permissions WHERE name = 'asset_categories.update');
SET @asset_categories_delete = (SELECT id FROM permissions WHERE name = 'asset_categories.delete');
SET @assets_view = (SELECT id FROM permissions WHERE name = 'assets.view');
SET @assets_create = (SELECT id FROM permissions WHERE name = 'assets.create');
SET @assets_update = (SELECT id FROM permissions WHERE name = 'assets.update');
SET @assets_delete = (SELECT id FROM permissions WHERE name = 'assets.delete');
SET @devices_view = (SELECT id FROM permissions WHERE name = 'devices.view');
SET @devices_create = (SELECT id FROM permissions WHERE name = 'devices.create');
SET @devices_update = (SELECT id FROM permissions WHERE name = 'devices.update');
SET @devices_delete = (SELECT id FROM permissions WHERE name = 'devices.delete');
SET @devices_assign = (SELECT id FROM permissions WHERE name = 'devices.assign');
SET @assignments_view = (SELECT id FROM permissions WHERE name = 'assignments.view');
SET @assignments_create = (SELECT id FROM permissions WHERE name = 'assignments.create');
SET @assignments_update = (SELECT id FROM permissions WHERE name = 'assignments.update');
SET @assignments_delete = (SELECT id FROM permissions WHERE name = 'assignments.delete');
SET @tracking_live_map_view = (SELECT id FROM permissions WHERE name = 'tracking.live_map.view');
SET @tracking_history_view = (SELECT id FROM permissions WHERE name = 'tracking.history.view');
SET @geofences_view = (SELECT id FROM permissions WHERE name = 'geofences.view');
SET @geofences_create = (SELECT id FROM permissions WHERE name = 'geofences.create');
SET @geofences_update = (SELECT id FROM permissions WHERE name = 'geofences.update');
SET @geofences_delete = (SELECT id FROM permissions WHERE name = 'geofences.delete');
SET @alerts_view = (SELECT id FROM permissions WHERE name = 'alerts.view');
SET @alerts_create = (SELECT id FROM permissions WHERE name = 'alerts.create');
SET @alerts_update = (SELECT id FROM permissions WHERE name = 'alerts.update');
SET @alerts_delete = (SELECT id FROM permissions WHERE name = 'alerts.delete');
SET @alerts_resolve = (SELECT id FROM permissions WHERE name = 'alerts.resolve');
SET @reports_view = (SELECT id FROM permissions WHERE name = 'reports.view');
SET @reports_create = (SELECT id FROM permissions WHERE name = 'reports.create');
SET @reports_export = (SELECT id FROM permissions WHERE name = 'reports.export');
SET @audit_logs_view = (SELECT id FROM permissions WHERE name = 'audit_logs.view');
SET @settings_manage = (SELECT id FROM permissions WHERE name = 'settings.manage');

-- ========================================================================
-- 3. CLEAR EXISTING ROLE-PERMISSION RELATIONSHIPS (ADMIN ONLY)
-- ========================================================================

-- Get admin role ID
SET @admin_role_id = (SELECT id FROM roles WHERE name = 'admin' AND guard_name = 'web');

-- Delete existing admin permissions to reassign all
DELETE FROM role_has_permissions WHERE role_id = @admin_role_id;

-- ========================================================================
-- 4. ASSIGN ALL PERMISSIONS TO ADMIN ROLE
-- ========================================================================

-- Admin gets EVERYTHING
INSERT IGNORE INTO role_has_permissions (role_id, permission_id) 
SELECT @admin_role_id, id FROM permissions WHERE guard_name = 'web';

-- ========================================================================
-- 5. CLEAR AND ASSIGN ASSET MANAGER PERMISSIONS
-- ========================================================================

SET @manager_role_id = (SELECT id FROM roles WHERE name = 'asset_manager' AND guard_name = 'web');

-- Delete existing manager permissions
DELETE FROM role_has_permissions WHERE role_id = @manager_role_id;

-- Asset Manager gets limited permissions
INSERT IGNORE INTO role_has_permissions (role_id, permission_id) VALUES
(@manager_role_id, @dashboard_manager_view),
(@manager_role_id, @assets_view),
(@manager_role_id, @assets_create),
(@manager_role_id, @assets_update),
(@manager_role_id, @tracking_live_map_view),
(@manager_role_id, @tracking_history_view),
(@manager_role_id, @geofences_view),
(@manager_role_id, @geofences_create),
(@manager_role_id, @geofences_update),
(@manager_role_id, @alerts_view);

-- ========================================================================
-- 6. VERIFY PERMISSIONS
-- ========================================================================

-- View all permissions count
SELECT 'Total Permissions Created:' as Info, COUNT(*) as Count FROM permissions WHERE guard_name = 'web';

-- View admin role permissions
SELECT 'Admin Permissions:' as Info, COUNT(*) as Count 
FROM role_has_permissions 
WHERE role_id = @admin_role_id;

-- View manager role permissions
SELECT 'Asset Manager Permissions:' as Info, COUNT(*) as Count 
FROM role_has_permissions 
WHERE role_id = @manager_role_id;

-- List all admin permissions
SELECT p.name FROM role_has_permissions rhp
JOIN permissions p ON rhp.permission_id = p.id
WHERE rhp.role_id = @admin_role_id
ORDER BY p.name;

-- ========================================================================
-- EXECUTION COMPLETE
-- ========================================================================
-- All permissions have been safely created (if not existing)
-- Admin role now has all permissions
-- Asset Manager role has been configured with appropriate permissions
-- Ready to use in the application
