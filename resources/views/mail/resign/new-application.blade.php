@component('mail::message')
# Pengajuan Resign Baru

{{$resignApplication->member->full_name}} mengajukan resign baru.

Terima Kasih,<br>
{{ config('app.name') }}
@endcomponent
