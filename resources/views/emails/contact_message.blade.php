@component('mail::message')
# {{ $isConfirmation ? 'Thanks for reaching out!' : 'New Contact Message' }}

@if($isConfirmation)
We’ve received your message and will reply soon. Here’s a copy:
@else
You’ve got a new contact message from your website.
@endif

@component('mail::panel')
**Name:** {{ $data['name'] }}

**Email:** {{ $data['email'] }}

@if(!empty($data['phone']))
**Phone:** {{ $data['phone'] }}
@endif

@if(!empty($data['social']))
**Social:** {{ $data['social'] }}
@endif

**Message:**
> {!! nl2br(e($data['message'])) !!}
@endcomponent

@if($isConfirmation)
Thanks,  
{{ config('app.name') }}
@else
— {{ config('app.name') }}
@endif
@endcomponent
