<x-mail::message>
# Hello {{ $user->name }},

Your account password has been updated successfully.

Here is your new password:
@component('mail::panel')
{{ $newPassword }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
