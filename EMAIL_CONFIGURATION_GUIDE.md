# Email Configuration Guide

## Overview
This guide explains how to properly configure email notifications in the Smart Asset Management System. The system supports multiple email services and includes password reset, user credentials, and alert notifications.

---

## ⚙️ Configuration Steps

### Step 1: Copy Environment Variables
```bash
cp .env.example .env
```

### Step 2: Configure Email Service

#### Option A: Gmail (Recommended for Development)
1. Create an App Password:
   - Go to https://myaccount.google.com/security
   - Enable 2-Factor Authentication
   - Create an App Password (select "Mail" and "Windows Computer")
   - Copy the 16-character password

2. Update `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_FROM_ADDRESS="noreply@smartassets.com"
   MAIL_FROM_NAME="Smart Asset Management"
   ```

#### Option B: Mailtrap (Best for Testing)
1. Sign up at https://mailtrap.io (free tier available)
2. Create a new inbox
3. Copy SMTP credentials from Mailtrap dashboard

4. Update `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_mailtrap_username
   MAIL_PASSWORD=your_mailtrap_password
   MAIL_FROM_ADDRESS="hello@example.com"
   MAIL_FROM_NAME="Smart Asset Management"
   ```

#### Option C: Sendgrid
1. Sign up at https://sendgrid.com
2. Create API key from Settings → API Keys
3. Update `.env`:
   ```env
   MAIL_MAILER=sendgrid
   SENDGRID_API_KEY=your_sendgrid_api_key
   MAIL_FROM_ADDRESS="noreply@smartassets.com"
   MAIL_FROM_NAME="Smart Asset Management"
   ```

#### Option D: Log Emails (Development Only)
For local development without sending actual emails:
```env
MAIL_MAILER=log
MAIL_LOG_CHANNEL=single
```
Emails will be logged to `storage/logs/laravel.log`

### Step 3: Configure Queue

The system uses a database queue to process emails asynchronously. Ensure queue is set up:

```env
QUEUE_CONNECTION=database
```

### Step 4: Start Queue Worker

Run this command in a separate terminal to process queued jobs:

```bash
php artisan queue:work --queue=notifications
```

Or in production (using Supervisor):
```bash
php artisan queue:work --queue=notifications --daemon
```

---

## 📧 Email Features

### 1. Password Reset Email
**Triggered:** When user requests password reset via `/api/password/forgot`

**Features:**
- 60-minute token expiration
- Secure reset link
- User-friendly email template
- Error logging
- **Asynchronous processing (non-blocking)**

**Test it:**
```bash
curl -X POST http://localhost:8000/api/password/forgot \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com"}'
```

### 2. User Credentials Email
**Triggered:** When admin creates new user via `/api/admin/users/create`

**Features:**
- Auto-generated password
- Option to force password reset on first login
- Welcome message
- Bulk import support

**Test it:**
```bash
curl -X POST http://localhost:8000/api/admin/users/create \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "role": "asset_manager",
    "send_credentials": true,
    "force_password_reset": true
  }'
```

### 3. Alert Notification Email
**Triggered:** When critical alerts are created

**Features:**
- Severity-based formatting
- Automatic recipient targeting (admins, managers)
- Real-time alert details

---

## 🔍 Troubleshooting

### Emails Not Sending?

1. **Check Mail Configuration:**
   ```bash
   php artisan tinker
   > config('mail')
   ```

2. **Check Queue Jobs:**
   ```bash
   php artisan queue:failed
   ```

3. **Check Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Test Mail Connection:**
   ```bash
   php artisan mail:send-test test@example.com
   ```

5. **Verify Queue Worker is Running:**
   ```bash
   ps aux | grep "queue:work"
   ```

### Common Issues:

| Issue | Solution |
|-------|----------|
| "MAIL_MAILER=log" in .env | Change to MAIL_MAILER=smtp or MAIL_MAILER=sendgrid |
| Queue worker not running | Run `php artisan queue:work --queue=notifications` |
| Connection timeout | Check MAIL_HOST and MAIL_PORT are correct |
| Authentication failed | Verify MAIL_USERNAME and MAIL_PASSWORD |
| Jobs stuck in database | Run `php artisan queue:retry all` |
| Emails going to spam | Add SPF, DKIM, DMARC records to your domain |
| Page hangs on password reset | Ensure queue worker is running with `php artisan queue:work --queue=notifications` |

---

## 📱 Testing Emails Locally

### Option 1: Log Viewer
Update `.env`:
```env
MAIL_MAILER=log
```
View emails in `storage/logs/laravel.log`

### Option 2: Mailtrap (Recommended)
Sign up for free Mailtrap account and view all emails in their inbox.

### Option 3: MailHog
Run locally for inbox inspection:
```bash
docker run -d -p 1025:1025 -p 8025:8025 mailhog/mailhog
```
Then view at http://localhost:8025

---

## 🚀 Production Setup

For production deployment:

1. **Use Sendgrid or similar service** (recommended)
2. **Run queue worker with Supervisor:**
   ```ini
   [program:smartassets-queue]
   process_name=%(program_name)s_%(process_num)02d
   command=php /path/to/artisan queue:work --queue=notifications --daemon
   autostart=true
   autorestart=true
   numprocs=2
   user=www-data
   ```

3. **Enable email rate limiting** to prevent abuse
4. **Set up monitoring** for failed jobs
5. **Configure email authentication** (SPF, DKIM, DMARC)

---

## 📞 Support

For issues or questions about email configuration, contact your system administrator or check Laravel's official documentation: https://laravel.com/docs/mail
