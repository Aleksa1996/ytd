@component('mail::message')
<p> Hi <b>Aleksa</b>, </p>
<p> You have new message from contact form (<b>Youtube downloader</b>): </p>
<p> Name: <b> {{ $data['name'] }} </b> ({{ $data['email'] }}) </p>
<p> Subject: <b>{{ $data['subject'] }}</b> </p>
Message:
@component('mail::panel')
{{ $data['message'] }}
@endcomponent
Thanks, <br /> {{ config('app.name') }} team
@endcomponent
