<x-mail::message>
# Release Status Changed

Your release **{{ $releaseTitle }}** status has been updated.

**Previous status:** {{ $oldStatus->getLabel() }}
**New status:** {{ $newStatus->getLabel() }}

@if($rejectReason)
**Reason:** {{ $rejectReason }}
@endif

Thank you,
{{ config('app.name') }}
</x-mail::message>
