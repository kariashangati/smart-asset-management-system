# Smart Asset Management System - Phase 5 Developer Guide

## Phase 5: Advanced Features Implementation

### Overview
Phase 5 introduces enterprise-grade features including email notifications, advanced user management, real-time Google Maps integration, analytics and reporting, custom alert rules, and audit logging.

---

## New Features

### 1. Email Notification System

#### Real-time Alert Email Notifications
- Automatic email delivery when alerts are triggered
- Severity-based email formatting
- Recipient targeting (admins, managers)
- Email tracking (email_sent flag)

**Configuration:**
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@smartassets.com
MAIL_FROM_NAME="Smart Asset Management"
```

**How it works:**
```
Alert Created → AlertCreated Event
     ↓
NotifyAlertCreated Listener
     ↓
AlertEmailNotification (Queued)
     ↓
Email Sent to Managers/Admins
```

#### Password Reset via Email
- Secure password reset link via email
- 60-minute token expiration
- Endpoint: `POST /api/password/forgot`
- Reset endpoint: `POST /api/password/reset`

**Request Example:**
```bash
curl -X POST http://localhost:8000/api/password/forgot \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com"}'
```

#### New User Credentials Email
- Automatic credential delivery to new users
- Admin option to force password reset on first login
- Regenerate password endpoint
- Endpoint: `POST /api/admin/users/create`

**Request Example:**
```bash
curl -X POST http://localhost:8000/api/admin/users/create \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "role": "asset_manager",
    "department_id": 1,
    "send_credentials": true,
    "force_password_reset": true
  }'
```

### 2. Advanced User Management

#### Bulk CSV Import
- Import multiple users from CSV file
- Automatic credential generation and email distribution
- Error handling and validation
- Endpoint: `POST /api/admin/users/import`

**CSV Format:**
```csv
name,email,role,department_id
John Doe,john@example.com,asset_manager,1
Jane Smith,jane@example.com,viewer,2
```

**Request:**
```bash
curl -X POST http://localhost:8000/api/admin/users/import \
  -H "Authorization: Bearer {token}" \
  -F "file=@users.csv"
```

#### Password Regeneration
- Admin can regenerate user passwords
- Option to send new password via email
- Endpoint: `POST /api/admin/users/{user}/regenerate-password`

### 3. Google Maps Integration

#### Live Map View
- Real-time asset location display
- Multiple map view modes (Map, Satellite)
- Interactive markers with asset info
- Zoom and pan controls
- Geofence visualization

**API Key Setup:**
```env
GOOGLE_MAPS_API_KEY=AIzaSyCyR24JTLCsPB3EguGCzLorT-CrVrxr4bk
```

#### Map Endpoints

**Get Assets for Map:**
```
GET /api/map/assets
```
Query Parameters:
- `department_id` (optional)
- `status` (optional)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Vehicle A",
      "latitude": 40.7128,
      "longitude": -74.0060,
      "last_updated": "2026-05-24T10:30:00Z",
      "department": "Operations"
    }
  ],
  "map_config": {
    "google_maps_api_key": "YOUR_KEY",
    "default_center": {"lat": 40.7128, "lng": -74.0060},
    "default_zoom": 12
  }
}
```

**Get Asset Track History:**
```
GET /api/map/assets/{id}/track
```

**Get Active Geofences:**
```
GET /api/map/geofences
```

#### Frontend Implementation (JavaScript/Vue.js)
```javascript
// Load Google Maps
script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY&libraries=maps,marker"

// Get assets
const response = await fetch('/api/map/assets', {
  headers: { 'Authorization': `Bearer ${token}` }
});

const { data, map_config } = await response.json();

// Initialize map
const map = new google.maps.Map(document.getElementById('map'), {
  center: map_config.default_center,
  zoom: map_config.default_zoom,
  mapTypeId: 'satellite' // or 'roadmap'
});

// Add markers
data.forEach(asset => {
  new google.maps.Marker({
    position: { lat: asset.latitude, lng: asset.longitude },
    map: map,
    title: asset.name,
    icon: 'https://example.com/vehicle-icon.png'
  });
});

// Add geofences as circles
const geofences = await fetch('/api/map/geofences').then(r => r.json());

geofences.data.forEach(geofence => {
  new google.maps.Circle({
    center: geofence.center,
    radius: geofence.radius,
    map: map,
    fillColor: '#FF0000',
    fillOpacity: 0.2,
    strokeColor: '#FF0000',
    strokeOpacity: 0.8
  });
});
```

### 4. Custom Alert Rules Engine

#### Rule Types
- **speed_threshold** - Alert when speed exceeds limit
- **geofence_breach** - Alert on geofence violation
- **inactivity** - Alert when asset inactive for period
- **custom** - Custom conditions (JSON)

#### Actions
- **email** - Send email notification
- **sms** - Send SMS via Twilio
- **push** - Send push notification via FCM
- **database** - Create alert in database only

#### API Endpoints

**Create Rule:**
```
POST /api/assets/{asset}/custom-rules
```

**Request Body:**
```json
{
  "rule_name": "Speed Limit Alert",
  "rule_type": "speed_threshold",
  "threshold_value": 100,
  "action": "email",
  "recipient_emails": ["manager@example.com"],
  "recipient_phones": ["+1234567890"],
  "is_active": true
}
```

**List Rules:**
```
GET /api/assets/{asset}/custom-rules
```

**Update Rule:**
```
PUT /api/custom-rules/{rule}
```

**Toggle Active Status:**
```
PATCH /api/custom-rules/{rule}/toggle
```

**Delete Rule:**
```
DELETE /api/custom-rules/{rule}
```

### 5. Analytics & Reporting

#### Dashboard Metrics
Endpoint: `GET /api/admin/dashboard/metrics`

**Response:**
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_assets": 150,
      "active_assets": 120,
      "total_alerts": 450,
      "unresolved_alerts": 25,
      "total_users": 45,
      "total_departments": 8,
      "total_asset_value": 2500000
    },
    "trends": {
      "alerts_30_days": [...]
    },
    "top_assets_by_alerts": [...]
  }
}
```

#### System Health Check
Endpoint: `GET /api/admin/dashboard/health`

**Response:**
```json
{
  "success": true,
  "data": {
    "database": "connected",
    "redis": "connected",
    "queue_jobs": 45,
    "failed_jobs": 2,
    "timestamp": "2026-05-24T10:30:00Z"
  }
}
```

#### Reports

**Asset Summary Report:**
```
GET /api/reports/assets
```

**Alerts Report (with date range):**
```
GET /api/reports/alerts?from=2026-05-01&to=2026-05-31&asset_id=1
```

**Location Tracking Report:**
```
GET /api/reports/tracking?asset_id=1&from=2026-05-01&to=2026-05-31
```

#### Data Export

**Export Assets as PDF:**
```
GET /api/reports/assets/export/pdf
```

**Export Assets as CSV:**
```
GET /api/reports/assets/export/csv
```

**Export Alerts as PDF:**
```
GET /api/reports/alerts/export/pdf?from=2026-05-01&to=2026-05-31
```

### 6. Asset Value Tracking

- New field: `asset_value` (decimal, 12,2)
- Tracked in reports
- Included in audit logs
- Visible in asset summary

**Update Asset Value:**
```
PUT /api/assets/{id}
```

```json
{
  "asset_value": 50000.00
}
```

### 7. Audit Logging (Spatie Activity Log)

- Automatic logging of all asset changes
- Track who made changes and when
- Log model: `activity_log` table
- Searchable and queryable

**View Activity Log:**
```bash
php artisan tinker
> Activity::where('subject_type', 'App\\Models\\Asset')->get()
```

---

## Installation & Setup

### 1. Install New Packages
```bash
composer install
```

New packages automatically added to `composer.json`:
- `barryvdh/laravel-dompdf` - PDF generation
- `maatwebsite/excel` - CSV import/export
- `elasticsearch/elasticsearch` - Search engine
- `laravel-notification-channels/twilio` - SMS notifications
- `laravel-notification-channels/fcm` - Push notifications
- `spatie/laravel-activitylog` - Audit logging

### 2. Publish Config Files
```bash
# Activity Log
php artisan vendor:publish --provider="Spatie\\Activitylog\\ActivitylogServiceProvider" --tag="config"

# DomPDF
php artisan vendor:publish --vendor="barryvdh/laravel-dompdf"
```

### 3. Run Migrations
```bash
php artisan migrate
```

New tables created:
- `activity_log` - Audit trail
- `custom_alert_rules` - User-defined alert rules
- `notifications` - Database notifications

New columns added:
- `assets.asset_value`
- `alerts.email_sent`, `sms_sent`, `push_sent`

### 4. Environment Configuration

**.env Setup:**
```env
# Email Configuration
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@smartassets.com
MAIL_FROM_NAME="Smart Asset Management"

# Google Maps
GOOGLE_MAPS_API_KEY=AIzaSyCyR24JTLCsPB3EguGCzLorT-CrVrxr4bk

# Twilio (SMS)
TWILIO_AUTH_TOKEN=your_token
TWILIO_ACCOUNT_SID=your_sid
TWILIO_FROM_NUMBER=+1234567890

# Firebase (Push Notifications)
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_API_KEY=your_api_key
FIREBASE_MESSAGING_SENDER_ID=your_sender_id

# Elasticsearch (Advanced Search)
ELASTICSEARCH_HOST=localhost
ELASTICSEARCH_PORT=9200
```

### 5. Queue Setup

**Start Queue Worker:**
```bash
# For development
php artisan queue:work

# For production (using Supervisor)
# See Laravel docs for Supervisor setup
```

**Monitor Queue:**
```bash
php artisan queue:monitor
```

---

## Testing Phase 5 Features

### Run Tests
```bash
# All tests
php artisan test

# Specific test
php artisan test tests/Feature/Api/CustomAlertRuleTest.php
php artisan test tests/Feature/Api/ReportTest.php
```

### Test Coverage
- Custom Alert Rules (CRUD, toggle)
- Reports (summary, export)
- User Management (create, import, regenerate password)
- Email notifications
- Password reset flow

---

## Notification Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    ALERT TRIGGERED                          │
└────────────────────┬────────────────────────────────────────┘
                     │
         ┌───────────▼───────────┐
         │  AlertCreated Event   │
         └───────────┬───────────┘
                     │
        ┌────────────┴────────────┐
        │                         │
   ┌────▼──────┐          ┌──────▼────┐
   │  Listener │          │  Listener │
   │  1: Email │          │  2: SMS   │
   └────┬──────┘          └──────┬────┘
        │                        │
   ┌────▼──────────┐      ┌─────▼─────┐
   │ Check Custom  │      │  Check    │
   │ Rules         │      │  Custom   │
   │ (action=email)│      │  Rules    │
   └────┬──────────┘      │(action=sms)│
        │                  └─────┬─────┘
   ┌────▼──────────────┐         │
   │ Send Email via    │    ┌────▼──────────┐
   │ AlertEmailNotify  │    │ Send SMS via  │
   │                   │    │ Twilio        │
   │ Queue: notify     │    │               │
   │ Mark: email_sent  │    │ Mark: sms_sent│
   └───────────────────┘    └────────────────┘

   Broadcast Event to WebSocket Clients
   │
   ├─> asset.{asset_id}
   ├─> department.{dept_id}
   ├─> alerts
   └─> breaches
```

---

## Security Best Practices

### Email Security
1. Use SMTP over TLS/SSL
2. Implement rate limiting on password reset
3. Token expiration (60 minutes)
4. Hash tokens in database

### User Management Security
1. Strong password generation (12+ random chars)
2. Force password reset on first login
3. Admin can regenerate passwords
4. Activity logging for user creation

### Maps Security
1. Restrict API key to specific domains
2. Enable only Maps JavaScript API
3. Implement location privacy settings
4. Audit location access

### Data Export Security
1. Authorization checks before export
2. Audit log all exports
3. Sensitive data redaction options
4. Encryption for CSV exports in transit

---

## Performance Optimization

### Queue Configuration
- Dedicated notification queue
- Priority queues for urgent alerts
- Job batching for bulk operations

### Caching
```php
// Cache dashboard metrics (1 hour)
Cache::remember('dashboard.metrics', 3600, function () {
    return // metrics calculation
});
```

### Database Optimization
- Indexes on frequently queried columns
- Eager loading for relationships
- Pagination for large datasets

### Map Optimization
- Marker clustering for 100+ assets
- Lazy load geofence data
- Debounce location updates on map

---

## Troubleshooting

### Emails Not Sending
```bash
# Check mail logs
php artisan tinker
> Mail::send(new TestMailable)

# Test SMTP connection
php artisan mail:test your@email.com
```

### Queue Jobs Failing
```bash
# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Maps Not Loading
```bash
# Check API key
echo env('GOOGLE_MAPS_API_KEY');

# Verify API key restrictions
# https://console.cloud.google.com/
```

---

## API Rate Limits

- **General Endpoints:** 60 requests/minute
- **User Management:** 10 requests/minute
- **Export Endpoints:** 5 requests/minute
- **Map Endpoints:** 100 requests/minute

---

## Next Steps

1. **Frontend Development**
   - Build dashboard UI
   - Implement map component
   - Create forms for user management

2. **Mobile App**
   - iOS/Android app with offline support
   - Push notifications
   - Live location tracking

3. **Advanced Analytics**
   - Predictive analytics
   - Anomaly detection
   - ML-based insights

4. **Integration Marketplace**
   - Third-party API integrations
   - Webhook management
   - Custom plugin system

---

## Support & Documentation

- [Phase 5 API Documentation](#api-documentation)
- [Phase 1-4 Developer Guide](./DEVELOPER_GUIDE.md)
- [API Documentation](./API_DOCUMENTATION.md)
- [Laravel Mail Docs](https://laravel.com/docs/mail)
- [Google Maps API](https://developers.google.com/maps)
- [Twilio SMS](https://www.twilio.com/docs/sms)
- [Firebase Cloud Messaging](https://firebase.google.com/docs/cloud-messaging)
