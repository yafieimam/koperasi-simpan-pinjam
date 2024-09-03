@component('mail::message')
# {{$article->title}}

{{$article->content}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
