<?php

namespace App\Console\Commands;

use App\Services\LoanService;
use Illuminate\Console\Command;

class MarkOverdueLoans extends Command
{
    protected $signature = 'loans:mark-overdue';

    protected $description = 'Mark all active loans past their due date as overdue.';

    public function __construct(private readonly LoanService $loanService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = $this->loanService->markOverdue();

        $this->info("Marked {$count} loan(s) as overdue.");

        return Command::SUCCESS;
    }
}
