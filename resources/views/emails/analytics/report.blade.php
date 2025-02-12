@component('mail::message')
# Analytics Report

Your scheduled analytics report is attached.

@component('mail::button', ['url' => route('admin.analytics.index')])
View Analytics Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 