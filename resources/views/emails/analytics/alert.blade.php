@component('mail::message')
# Analytics Alert: {{ $type }}

@foreach ($data as $key => $value)
**{{ Str::title($key) }}:** {{ $value }}
@endforeach

@component('mail::button', ['url' => route('admin.analytics.index')])
View Analytics
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 