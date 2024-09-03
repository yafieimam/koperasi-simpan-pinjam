@component('mail::message')
# Pengajuan Pinjaman

Terdapat Pinjaman Menunggu Persetujuan Anda.

@component('mail::button', ['url' => config('URL').'/loan-detail/'.encrypt($loanApplication->id)])
Cek Sekarang
@endcomponent

Terima Kasih,<br>
{{ config('app.name') }}
@endcomponent
