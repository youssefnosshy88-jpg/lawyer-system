<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class PrintController extends Controller
{
    public function invoice(Invoice $invoice): View
    {
        Gate::authorize('view', $invoice);

        $invoice->load(['client', 'items', 'payments', 'legalCase', 'company']);

        return view('print.invoice', ['invoice' => $invoice]);
    }

    public function receipt(Payment $payment): View
    {
        Gate::authorize('view', $payment);

        $payment->load(['client', 'invoice', 'receiver']);

        return view('print.receipt', ['payment' => $payment]);
    }
}
