<x-mail::message>
# Contract Ready

The contract for your release **{{ $releaseTitle }}** is ready for download.

<x-mail::button :url="$contractUrl">
Download Contract
</x-mail::button>

Thank you,
{{ config('app.name') }}
</x-mail::message>
