@component('mail::message')
# Pengajuan Pinjaman Baru

{{$changeDepositApplication->member->full_name}} mengajukan pinjaman baru.

<!-- @component('mail::button', ['url' => config('URL').'/loan-detail/'.encrypt($changeDepositApplication->id)])
Cek Sekarang
@endcomponent -->

Terima Kasih,<br>
{{ config('app.name') }}
@endcomponent
