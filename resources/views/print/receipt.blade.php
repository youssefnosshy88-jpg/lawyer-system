@extends('layouts.print')

@section('title', __('billing.payments.receipt') . ' ' . $payment->receipt_no)

@section('header-meta')
    <div class="muted">{{ __('billing.payments.paid_at') }}: {{ $payment->paid_at->format('Y-m-d') }}</div>
@endsection

@section('content')
    <div class="grid">
        <div class="box">
            <h4>{{ __('billing.print.received_from') }}</h4>
            <strong>{{ $payment->client->name }}</strong><br>
            {{ $payment->client->phone }}
        </div>
        <div class="box">
            <h4>{{ __('billing.fields.amount') }}</h4>
            <strong style="font-size: 22px">{{ number_format($payment->amount, 2) }} {{ $payment->invoice?->currency ?? config('firm.currency') }}</strong><br>
            <span class="muted">{{ __('billing.payments.method') }}: {{ $payment->method->getLabel() }}</span>
            @if($payment->reference)<br><span class="muted">{{ __('billing.payments.reference') }}: {{ $payment->reference }}</span>@endif
        </div>
    </div>

    <table>
        <tr><th>{{ __('billing.invoices.singular') }}</th><td>{{ $payment->invoice?->number ?? '-' }}</td></tr>
        @if($payment->invoice)
            <tr><th>{{ __('billing.invoices.total') }}</th><td>{{ number_format($payment->invoice->total, 2) }}</td></tr>
            <tr><th>{{ __('billing.invoices.balance') }}</th><td>{{ number_format($payment->invoice->balance, 2) }}</td></tr>
        @endif
        <tr><th>{{ __('billing.payments.received_by') }}</th><td>{{ $payment->receiver?->name ?? '-' }}</td></tr>
        @if($payment->notes)
            <tr><th>{{ __('app.fields.notes') }}</th><td>{{ $payment->notes }}</td></tr>
        @endif
    </table>

    <div class="sign">
        <div>{{ __('billing.print.received_by_signature') }}</div>
        <div>{{ __('billing.print.client_signature') }}</div>
    </div>
@endsection
