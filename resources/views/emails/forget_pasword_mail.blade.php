@component('mail::message')

Hello {{$user_name}}

Click here to reset your password

@component('mail::button', ['url' => route('getResetPassword',$reset_code)])
Reset Password
@endcomponent
<p>Or copy & paste the following link to your browser</p>
<p><a href="{{route('getResetPassword',$reset_code)}}">{{route('getResetPassword',$reset_code)}}</a></p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
