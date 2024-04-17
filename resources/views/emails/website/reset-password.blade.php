@component('mail::message')
# Hello There, {{$username}}

You had successfully reset your account password.

Thanks,
{{ config('app.name') }}
@endcomponent
