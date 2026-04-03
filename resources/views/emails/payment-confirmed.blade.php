<x-mail::message>
# Payment Confirmed

Your payment for order **{{ $orderKey }}** has been confirmed.

**Service:** {{ $serviceName }}

Thank you,
{{ config('app.name') }}
</x-mail::message>
