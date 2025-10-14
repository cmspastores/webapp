<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agreement;
use App\Models\Bill;
use Carbon\Carbon;

class GenerateMonthlyBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly bills for all active agreements every 30 days based on start date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->startOfDay();
        $agreements = Agreement::where('is_active', true)->get();

        foreach ($agreements as $agreement) {
            $lastBill = Bill::where('agreement_id', $agreement->agreement_id)
                ->orderBy('period_end', 'desc')
                ->first();

            if (!$lastBill) {
                // No bill yet → create the first bill on start date
                if ($today->greaterThanOrEqualTo(Carbon::parse($agreement->start_date))) {
                    $periodStart = Carbon::parse($agreement->start_date);
                    $periodEnd = (clone $periodStart)->addDays(29)->endOfDay();

                    Bill::create([
                        'agreement_id' => $agreement->agreement_id,
                        'renter_id' => $agreement->renter_id,
                        'room_id' => $agreement->room_id,
                        'period_start' => $periodStart->toDateString(),
                        'period_end' => $periodEnd->toDateString(),
                        'due_date' => (clone $periodEnd)->addDays(7)->toDateString(),
                        'amount_due' => $agreement->monthly_rent ?? 0,
                        'balance' => $agreement->monthly_rent ?? 0,
                        'status' => 'unpaid',
                    ]);

                    $this->info("✅ First bill created for Agreement #{$agreement->agreement_id}");
                }
                continue;
            }

            // There’s already at least one bill → check if 30 days passed
            $nextBillStart = Carbon::parse($lastBill->period_start)->addDays(30)->startOfDay();
            $nextBillEnd = (clone $nextBillStart)->addDays(29)->endOfDay();

            if ($today->greaterThanOrEqualTo($nextBillStart)) {
                // Prevent duplicate bill for the same period
                $exists = Bill::where('agreement_id', $agreement->agreement_id)
                    ->whereDate('period_start', $nextBillStart->toDateString())
                    ->exists();

                if ($exists) continue;

                Bill::create([
                    'agreement_id' => $agreement->agreement_id,
                    'renter_id' => $agreement->renter_id,
                    'room_id' => $agreement->room_id,
                    'period_start' => $nextBillStart->toDateString(),
                    'period_end' => $nextBillEnd->toDateString(),
                    'due_date' => (clone $nextBillEnd)->addDays(7)->toDateString(),
                    'amount_due' => $agreement->monthly_rent ?? 0,
                    'balance' => $agreement->monthly_rent ?? 0,
                    'status' => 'unpaid',
                ]);

                $this->info("🧾 New bill auto-generated for Agreement #{$agreement->agreement_id}");
            }
        }

        $this->info('✔ Auto-billing check complete.');
        return 0;
    }
}