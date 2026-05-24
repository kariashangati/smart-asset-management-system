@component('mail::message')
# Asset Alert Notification

Hello,

An alert has been triggered for one of your tracked assets.

@component('mail::panel')
**Asset:** {{ $assetName }}

**Alert Type:** {{ $alertType }}

**Severity:** {{ $severity }}

**Description:** {{ $description }}

**Time:** {{ $timestamp }}
@endcomponent

@component('mail::button', ['url' => config('app.url') . '/alerts'])
View Alert Details
@endcomponent

Best regards,
Smart Asset Management System
@endcomponent
