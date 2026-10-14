@extends('layouts.print')

@section('title', __('billing.invoices.singular') . ' ' . $invoice->number)

@section('header-meta')
    <div class="muted">{{ __('billing.invoices.issued_at') }}: {{ $invoice->issued_at->format('Y-m-d') }}</div>
    @if($invoice->due_at)
        <div class="muted">{{ __('billing.invoices.due_at') }}: {{ $invoice->due_at->format('Y-m-d') }}</div>
    @endif
    <span class="badge">{{ $invoice->status->getLabel() }}</span>
@endsection

@section('content')
    <div class="grid">
        <div class="box">
            <h4>{{ __('cases.fields.client') }}</h4>
            <strong>{{ $invoice->client->name }}</strong><br>
            {{ $invoice->client->address }}<br>
            {{ $invoice->client->phone }}
            @if($invoice->client->tax_number)<br>{{ __('clients.fields.tax_number') }}: {{ $invoice->client->tax_number }}@endif
        </div>
        <div class="box">
            <h4>{{ __('billing.invoices.regarding') }}</h4>
            @if($invoice->legalCase)
                {{ __('cases.singular') }}: {{ $invoice->legalCase->display_title }}<br>
            @endif
            @if($invoice->company)
                {{ __('companies.singular') }}: {{ $invoice->company->name }}<br>
            @endif
            @if(! $invoice->legalCase && ! $invoice->company)
                -
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px">#</th>
                <th>{{ __('billing.invoices.item_description') }}</th>
                <th class="num">{{ __('billing.invoices.quantity') }}</th>
                <th class="num">{{ __('billing.invoices.unit_price') }}</th>
                <th class="num">{{ __('billing.invoices.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="num">{{ number_format($item->quantity, 2) }}</td>
                    <td class="num">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="num">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>{{ __('billing.invoices.subtotal') }}</td><td class="num">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</td></tr>
        @if($invoice->discount > 0)
            <tr><td>{{ __('billing.invoices.discount') }}</td><td class="num">- {{ number_format($invoice->discount, 2) }}</td></tr>
        @endif
        @if($invoice->tax_amount > 0)
            <tr><td>{{ __('billing.invoices.tax_amount') }} ({{ $invoice->tax_rate }}%)</td><td class="num">{{ number_format($invoice->tax_amount, 2) }}</td></tr>
        @endif
        <tr class="grand"><td>{{ __('billing.invoices.total') }}</td><td class="num">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td></tr>
        <tr><td>{{ __('billing.invoices.paid_amount') }}</td><td class="num">{{ number_format($invoice->paid_amount, 2) }}</td></tr>
        <tr class="grand"><td>{{ __('billing.invoices.balance') }}</td><td class="num">{{ number_format($invoice->balance, 2) }} {{ $invoice->currency }}</td></tr>
    </table>

    @if($invoice->notes)
        <div class="box"><h4>{{ __('app.fields.notes') }}</h4>{{ $invoice->notes }}</div>
    @endif

    <div class="sign">
        <div>{{ __('billing.print.firm_signature') }}</div>
        <div>{{ __('billing.print.client_signature') }}</div>
    </div>
@endsection
