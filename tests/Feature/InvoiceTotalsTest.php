<?php

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;

it('recalculates totals when items are added', function () {
    $invoice = Invoice::factory()->create(['tax_rate' => 14, 'discount' => 100]);

    InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'Fees', 'quantity' => 2, 'unit_price' => 1000]);
    InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'Court fees', 'quantity' => 1, 'unit_price' => 600]);

    $invoice->refresh();

    expect((float) $invoice->subtotal)->toBe(2600.0)
        ->and((float) $invoice->tax_amount)->toBe(350.0)
        ->and((float) $invoice->total)->toBe(2850.0)
        ->and($invoice->status)->toBe(InvoiceStatus::SENT);
});

it('moves to partially paid then paid as payments are recorded', function () {
    $invoice = Invoice::factory()->create();
    InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'Fees', 'quantity' => 1, 'unit_price' => 5000]);

    Payment::factory()->create(['client_id' => $invoice->client_id, 'invoice_id' => $invoice->id, 'amount' => 2000]);
    expect($invoice->refresh()->status)->toBe(InvoiceStatus::PARTIALLY_PAID)
        ->and($invoice->balance)->toBe(3000.0);

    Payment::factory()->create(['client_id' => $invoice->client_id, 'invoice_id' => $invoice->id, 'amount' => 3000]);
    expect($invoice->refresh()->status)->toBe(InvoiceStatus::PAID)
        ->and($invoice->balance)->toBe(0.0);
});

it('generates sequential invoice numbers per year', function () {
    $first = Invoice::factory()->create();
    $second = Invoice::factory()->create();

    $year = now()->format('Y');

    expect($first->number)->toBe("INV-{$year}-0001")
        ->and($second->number)->toBe("INV-{$year}-0002");
});

it('marks sent invoices as overdue after due date', function () {
    $invoice = Invoice::factory()->create(['due_at' => now()->subDay()]);
    InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'Fees', 'quantity' => 1, 'unit_price' => 100]);

    $this->artisan('firm:mark-overdue-invoices')->assertSuccessful();

    expect($invoice->refresh()->status)->toBe(InvoiceStatus::OVERDUE);
});
