

@component('mail::message')
# {{ $details['title'] }}




{{'Congrats'}} {{ $details['name'] }}
{{'your verfication code is'}} {{ $details['otp'] }}


@endcomponent