@component('mail::message')
# Welcome to Smart Asset Management System

Hello {{ $user->name }},

Your account has been created successfully! Here are your login credentials:

@component('mail::panel')
**Email:** {{ $email }}

**Temporary Password:** {{ $password }}
@endcomponent

For security reasons, please change your password immediately after your first login.

@component('mail::button', ['url' => config('app.url') . '/login'])
Login Now
@endcomponent

If you have any questions, please contact your administrator.

Best regards,
Smart Asset Management System
@endcomponent
