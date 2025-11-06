@component('mail::message')
# {{ $p['greeting'] ?? 'Hello ' . ($p['name'] ?? '') }}

{!! $p['body_html'] !!}

@component('mail::panel')
If you have any further questions, just reply to this email.
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
