# Smart Asset Management System - Developer Guide

## Setup Instructions

### Prerequisites
- PHP 8.1+
- Laravel 10+
- MySQL 8.0+
- Redis (for caching and broadcasting)
- Node.js 18+ (for frontend assets)

### Installation

1. **Clone Repository**
```bash
git clone https://github.com/yourusername/smart-asset-management-system.git
cd smart-asset-management-system
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Configuration**
```bash
# Update .env with your database credentials
DB_DATABASE=smart_assets
DB_USERNAME=root
DB_PASSWORD=password

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

5. **Configure Broadcasting**
```env
BROADCAST_DRIVER=redis
REDIS_CLIENT=predis
```

6. **Setup Queue Processing**
```bash
# Start queue worker
php artisan queue:work

# Or using Supervisor for production
```

7. **Start Development Server**
```bash
php artisan serve
npm run dev
```

---

## Architecture Overview

### Directory Structure
```
app/
├── Broadcasting/       # Channel authentication
├── Console/           # Artisan commands
├── Events/            # Event definitions
├── Http/
│   ├── Controllers/   # API controllers
│   ├── Requests/      # Form requests
│   └── Resources/     # API resources
├── Jobs/              # Queued jobs
├── Listeners/         # Event listeners
├── Models/            # Database models
├── Notifications/     # Notification classes
├── Policies/          # Authorization policies
├── Providers/         # Service providers
└── Services/          # Business logic services

database/
├── migrations/        # Database migrations
├── factories/         # Model factories
└── seeders/           # Database seeders

tests/
├── Feature/           # Feature tests
└── Unit/              # Unit tests
```

---

## Key Features

### 1. Real-Time Asset Tracking
- Location updates via webhook integration
- Real-time broadcasting to connected clients
- Location history and statistics

### 2. Geofence Management
- Create and manage circular geofences
- Real-time breach detection
- Automatic alert generation

### 3. Alert System
- Severity-based alerts (low, medium, high)
- Real-time notifications via email and database
- Alert lifecycle management (unread → read → resolved)

### 4. Multi-Department Support
- Department-level access control
- Department managers can only view their assets
- Role-based authorization

### 5. Async Processing
- Queue-based location processing
- Automatic database cleanup (30-day retention)
- Scheduled commands for maintenance tasks

---

## Event Flow

### Location Update Flow
```
Tracker Device → Webhook (/api/webhooks/location)
  ↓
ProcessAssetLocationUpdate (Job)
  ↓
AssetLocationUpdated (Event)
  ↓
├─ LogAssetLocation (Listener) → Save to LocationLog
├─ CheckGeofenceBreach (Listener) → Check geofences
│   ↓
│   └─ AlertCreated (Event)
│       ↓
│       └─ NotifyAlertCreated (Listener) → Send notifications
└─ Broadcast to WebSocket clients
```

### Database Cleanup Flow
```
Scheduler (Daily at 2 AM)
  ↓
CleanupOldLocationLogs (Command)
  ↓
Delete logs older than 30 days
```

---

## Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
php artisan test tests/Feature/Api/AssetControllerTest.php
php artisan test tests/Unit/Services/GeofenceServiceTest.php
```

### Test Coverage
```bash
php artisan test --coverage
```

### Available Tests
- **Feature Tests**: API endpoint testing (5 test classes)
- **Unit Tests**: Service layer testing (2 test classes)
- **Total Test Coverage**: 40+ test cases

---

## Database Schema

### Key Tables

#### Assets
```sql
CREATE TABLE assets (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255),
  asset_type ENUM('vehicle', 'equipment', 'device', 'other'),
  serial_number VARCHAR(255) UNIQUE,
  status ENUM('active', 'inactive', 'maintenance', 'retired'),
  department_id BIGINT,
  tracker_device_id BIGINT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

#### Location Logs
```sql
CREATE TABLE location_logs (
  id BIGINT PRIMARY KEY,
  asset_id BIGINT,
  tracker_device_id BIGINT,
  latitude DECIMAL(10, 8),
  longitude DECIMAL(11, 8),
  speed FLOAT,
  motion_detected BOOLEAN,
  processed BOOLEAN DEFAULT FALSE,
  recorded_at TIMESTAMP,
  received_at TIMESTAMP,
  created_at TIMESTAMP
);
INDEX idx_asset_recorded (asset_id, recorded_at);
INDEX idx_processed (processed);
```

#### Geofences
```sql
CREATE TABLE geofences (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255),
  center_latitude DECIMAL(10, 8),
  center_longitude DECIMAL(11, 8),
  radius_meters INT,
  status ENUM('active', 'inactive'),
  alert_on_breach BOOLEAN DEFAULT TRUE,
  created_by BIGINT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

#### Alerts
```sql
CREATE TABLE alerts (
  id BIGINT PRIMARY KEY,
  asset_id BIGINT,
  tracker_device_id BIGINT,
  alert_type VARCHAR(255),
  severity ENUM('low', 'medium', 'high'),
  title VARCHAR(255),
  message TEXT,
  status ENUM('unread', 'read', 'resolved'),
  latitude DECIMAL(10, 8),
  longitude DECIMAL(11, 8),
  triggered_at TIMESTAMP,
  resolved_at TIMESTAMP,
  resolution_notes TEXT,
  created_at TIMESTAMP
);
INDEX idx_asset_status (asset_id, status);
INDEX idx_severity_status (status, severity);
INDEX idx_triggered (triggered_at);
```

---

## Console Commands

### Process Location Updates
```bash
php artisan location:process --minutes=5
```
Processes pending location logs from the last 5 minutes.

### Cleanup Old Location Logs
```bash
php artisan location:cleanup --days=30
```
Deletes location logs older than 30 days.

### Schedule Commands
Adds to scheduler in `app/Console/Kernel.php`:
- Every 5 minutes: Process location updates
- Daily at 2 AM: Cleanup old logs

---

## API Integration Examples

### cURL - Get Assets
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  https://api.yourdomain.com/api/assets
```

### JavaScript - Real-time Updates
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,
  cluster: process.env.MIX_PUSHER_APP_CLUSTER,
});

// Listen for location updates
window.Echo.private(`asset.${assetId}`)
  .listen('.asset.location_updated', (e) => {
    console.log('Asset moved:', e);
  });
```

### Python - Webhook Integration
```python
import requests

api_url = 'https://api.yourdomain.com/api/webhooks/location'

payload = {
    'tracker_device_id': 1,
    'asset_id': 1,
    'latitude': 40.7128,
    'longitude': -74.0060,
    'speed': 50,
    'motion_detected': True
}

response = requests.post(api_url, json=payload)
print(response.json())
```

---

## Troubleshooting

### Queue Jobs Not Processing
```bash
# Check queue status
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Start queue worker with verbose logging
php artisan queue:work --verbose
```

### Broadcasting Not Working
```bash
# Check Redis connection
redis-cli ping

# Verify broadcasting configuration
php artisan tinker
> broadcast('test')
```

### Location Updates Not Received
```bash
# Check processed flag in location_logs
SELECT COUNT(*) FROM location_logs WHERE processed = FALSE;

# Manually trigger processing
php artisan location:process --minutes=30
```

---

## Performance Optimization

### Database Optimization
- Add indexes on frequently queried columns (done in migrations)
- Use pagination for large datasets
- Implement query caching for read-heavy operations

### Queue Optimization
- Use Redis for queue backend
- Run multiple queue workers for parallel processing
- Monitor queue length and adjust workers accordingly

### Broadcasting Optimization
- Use presence channels for user activity tracking
- Implement message compression for large payloads
- Use Redis as broadcast backend

---

## Security Considerations

1. **Authentication**: Uses Laravel Sanctum for API tokens
2. **Authorization**: Role-based access control (RBAC) via policies
3. **Validation**: Form request validation for all inputs
4. **Rate Limiting**: 60 requests per minute per IP
5. **Webhook Security**: Consider adding HMAC signature verification
6. **Database Security**: Use parameterized queries (built-in via Eloquent)

---

## Future Enhancements

- [ ] Historical analytics and reporting dashboard
- [ ] Advanced filtering and search capabilities
- [ ] Mobile app integration
- [ ] AI-based anomaly detection
- [ ] Integration with third-party mapping services
- [ ] Bulk asset import/export
- [ ] Audit logging for compliance
- [ ] Custom alert rules engine

---

## Support & Documentation

- [API Documentation](./API_DOCUMENTATION.md)
- [Laravel Documentation](https://laravel.com/docs)
- [Pusher Broadcasting](https://pusher.com/docs)
- [Redis Documentation](https://redis.io/docs)
