@component('mail::message')
# Hello There, {{$username}}

Press the bellow button to reset your password!

@component('mail::button', [
    'url' => url('auth/password/reset') .'?'. http_build_query(['token' => $reset_token])
    ])
    Reset Password
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
