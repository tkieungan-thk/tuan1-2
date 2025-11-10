<x-mail::message>
# Hello {{ $user->name }}

<p>Your account has been successfully created on the system.</p>

<p><strong>Login information:</strong></p>
<ul>
<li>Email: {{ $user->email }}</li>
<li>Password: {{ $password }}</li>
</ul>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>