# Smart Asset Management System - API Documentation

## Overview
Comprehensive REST API for real-time asset tracking, geofence management, and alert monitoring.

## Base URL
```
https://api.yourdomain.com/api
```

## Authentication
All API endpoints (except webhooks) require Bearer token authentication.

```bash
Authorization: Bearer {token}
```

---

## Assets

### List Assets
```
GET /assets
```
**Query Parameters:**
- `per_page` (int, default: 15)
- `department_id` (int, optional)
- `status` (string: active, inactive, maintenance, retired)
- `asset_type` (string: vehicle, equipment, device, other)
- `search` (string: search by name or serial number)

**Response:**
```json
{
  "success": true,
  "message": "Assets retrieved successfully",
  "data": [...],
  "pagination": {...}
}
```

### Create Asset
```
POST /assets
```
**Request Body:**
```json
{
  "name": "Asset Name",
  "asset_type": "vehicle",
  "serial_number": "SN-001",
  "department_id": 1,
  "status": "active",
  "tracker_device_id": 1
}
```

### Get Asset
```
GET /assets/{id}
```

### Update Asset
```
PUT /assets/{id}
```

### Delete Asset
```
DELETE /assets/{id}
```

### Get Assets by Department
```
GET /assets/department/{departmentId}
```

---

## Locations

### Get Current Location
```
GET /assets/{asset_id}/location
```
**Response:**
```json
{
  "success": true,
  "data": {
    "asset_id": 1,
    "latitude": 40.7128,
    "longitude": -74.0060,
    "last_recorded_at": "2026-05-24T10:30:00Z",
    "last_motion_detected": true
  }
}
```

### Get Location History
```
GET /assets/{asset_id}/location-history
```
**Query Parameters:**
- `per_page` (int, optional)
- `limit` (int, default: 100)

### Get Location Statistics
```
GET /assets/{asset_id}/location-stats
```
**Response:**
```json
{
  "success": true,
  "data": {
    "asset_id": 1,
    "average_speed": 45.50,
    "total_distance_km": 125.34
  }
}
```

### Get Location by Date Range
```
GET /assets/{asset_id}/location-range
```
**Query Parameters:**
- `from` (datetime: Y-m-d H:i:s, required)
- `to` (datetime: Y-m-d H:i:s, required)

### Store Location Log
```
POST /location-logs
```
**Request Body:**
```json
{
  "tracker_device_id": 1,
  "asset_id": 1,
  "latitude": 40.7128,
  "longitude": -74.0060,
  "speed": 50,
  "motion_detected": true,
  "recorded_at": "2026-05-24 10:30:00"
}
```

### Batch Store Location Logs
```
POST /location-logs/batch
```
**Request Body:**
```json
{
  "locations": [
    {
      "tracker_device_id": 1,
      "asset_id": 1,
      "latitude": 40.7128,
      "longitude": -74.0060,
      "speed": 50
    }
  ]
}
```

---

## Geofences

### List Geofences
```
GET /geofences
```
**Query Parameters:**
- `status` (string: active, inactive)
- `asset_id` (int, optional)
- `search` (string, optional)

### Create Geofence
```
POST /geofences
```
**Request Body:**
```json
{
  "name": "Warehouse Zone",
  "description": "Main warehouse area",
  "center_latitude": 40.7128,
  "center_longitude": -74.0060,
  "radius_meters": 1000,
  "status": "active",
  "alert_on_breach": true
}
```

### Get Geofence
```
GET /geofences/{id}
```

### Update Geofence
```
PUT /geofences/{id}
```

### Delete Geofence
```
DELETE /geofences/{id}
```

### Get Geofence Violations
```
GET /geofences/{id}/violations
```
**Response:**
```json
{
  "success": true,
  "message": "Assets outside geofence retrieved",
  "data": [...],
  "count": 5
}
```

### Check Asset Inside Geofence
```
POST /geofences/{id}/check-asset
```
**Request Body:**
```json
{
  "asset_id": 1
}
```

### Assign Assets to Geofence
```
POST /geofences/{id}/assign-assets
```
**Request Body:**
```json
{
  "asset_ids": [1, 2, 3]
}
```

---

## Alerts

### List Alerts
```
GET /alerts
```
**Query Parameters:**
- `status` (string: unread, read, resolved)
- `severity` (string: low, medium, high)
- `alert_type` (string)
- `asset_id` (int, optional)

### Get Alert
```
GET /alerts/{id}
```

### Get Asset Alerts
```
GET /assets/{asset_id}/alerts
```

### Mark Alert as Read
```
PATCH /alerts/{id}/mark-read
```

### Mark Alert as Resolved
```
PATCH /alerts/{id}/mark-resolved
```
**Request Body:**
```json
{
  "resolution_notes": "Issue was resolved"
}
```

### Get Unread Alerts Count
```
GET /alerts/count/unread
```

### Get Alerts Summary
```
GET /alerts/summary
```

### Delete Alert
```
DELETE /alerts/{id}
```

---

## Webhooks (No Auth Required)

### Location Webhook
```
POST /webhooks/location
```
**Request Body:**
```json
{
  "tracker_device_id": 1,
  "asset_id": 1,
  "latitude": 40.7128,
  "longitude": -74.0060,
  "speed": 50,
  "motion_detected": true,
  "timestamp": 1234567890
}
```
**Response:**
```json
{
  "success": true,
  "message": "Location update received and queued for processing"
}
```
**Status Code:** 202 Accepted

### Alert Webhook
```
POST /webhooks/alert
```
**Request Body:**
```json
{
  "tracker_device_id": 1,
  "asset_id": 1,
  "alert_type": "geofence_breach",
  "severity": "high",
  "message": "Asset has left the geofence"
}
```

### Health Check
```
GET /webhooks/health
```
**Response:**
```json
{
  "status": "ok",
  "timestamp": "2026-05-24T10:30:00Z"
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "message": "Validation error",
  "errors": {
    "field": ["Error message"]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized"
}
```

### 404 Not Found
```json
{
  "message": "Not found"
}
```

### 422 Unprocessable Entity
```json
{
  "message": "Validation error",
  "errors": {}
}
```

---

## Rate Limiting
API requests are rate-limited to 60 requests per minute per IP address.

---

## Broadcasting Events

The system supports real-time updates via WebSocket broadcasting.

### Private Channels
- `asset.{asset_id}` - Asset-specific updates
- `department.{department_id}` - Department-wide updates
- `geofence.{geofence_id}` - Geofence breach notifications
- `alerts` - All alert events
- `breaches` - Geofence breach events
- `locations` - Location update events

### Events

#### Asset Location Updated
```javascript
echo.private(`asset.${assetId}`)
  .listen('.asset.location_updated', (data) => {
    console.log('Location:', data);
  });
```

#### Alert Created
```javascript
echo.private('alerts')
  .listen('.alert.created', (data) => {
    console.log('New Alert:', data);
  });
```

#### Geofence Breach Detected
```javascript
echo.private(`geofence.${geofenceId}`)
  .listen('.geofence.breach_detected', (data) => {
    console.log('Breach:', data);
  });
```
