<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Console\Command;

class MarkOverdueInvoicesCommand extends Command
{
    protected $signature = 'firm:mark-overdue-invoices';

    protected $description = 'Mark sent invoices whose due date has passed as overdue.';

    public function handle(): int
    {
        $count = Invoice::query()
            ->where('status', InvoiceStatus::SENT)
            ->whereNotNull('due_at')
            ->whereDate('due_at', '<', today())
            ->update(['status' => InvoiceStatus::OVERDUE]);

        $this->info("{$count} invoice(s) marked overdue.");

        return self::SUCCESS;
    }
}
