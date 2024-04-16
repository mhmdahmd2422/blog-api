@component('mail::message')
# Thanks for Joining our community, {{$username}}

Press the bellow button to be redirected to our homepage!

@component('mail::button', ['url' => config('app.url')])
    Homepage
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
