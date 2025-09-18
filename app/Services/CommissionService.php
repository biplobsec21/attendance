<?php

namespace App\Services;

class CommissionService
{
    protected array $transactions = [];
    protected array $summary = [];
    protected array $rules = [];

    public function calculate(array $sales, array $rules)
    {
        $this->transactions = [];
        $this->summary = [];
        $this->rules = $rules;

        // Process each sale
        foreach ($sales as $seller => $sale) {
            $productValue = $sale['amount'];
            $directPercent = $rules[$seller]['direct'] ?? 0;

            // Direct earning from product sale
            $directEarning = ($directPercent / 100) * $productValue;
            $this->addEarning($seller, $directEarning);

            // Distribute commissions recursively
            if (!empty($rules[$seller]['commissions'])) {
                $this->distributeCommission($seller, $directEarning, $rules[$seller]['commissions']);
            }
        }

        // Calculate expenses from transactions
        foreach ($this->transactions as $tx) {
            $this->summary[$tx['from']]['expense'] = ($this->summary[$tx['from']]['expense'] ?? 0) + $tx['amount'];
        }

        // Round values
        foreach ($this->summary as $person => &$data) {
            $data['earning'] = round($data['earning'], 4);
            $data['expense'] = round($data['expense'] ?? 0, 4);
        }

        return [
            'summary'      => $this->summary,
            'transactions' => $this->transactions,
        ];
    }

    /**
     * Recursive function to distribute commissions
     */
    protected function distributeCommission(string $from, float $baseAmount, array $commissions)
    {
        foreach ($commissions as $to => $percent) {
            $commission = ($percent / 100) * $baseAmount;
            if ($commission <= 0) {
                continue;
            }

            // Record transaction
            $this->transactions[] = [
                'from'   => $from,
                'to'     => $to,
                'amount' => round($commission, 4),
            ];

            // Add to recipient's earning
            $this->addEarning($to, $commission);

            // If recipient has further commissions, recurse
            if (!empty($this->rules[$to]['commissions'])) {
                $this->distributeCommission($to, $commission, $this->rules[$to]['commissions']);
            }
        }
    }

    /**
     * Helper: Add earning to a person
     */
    protected function addEarning(string $person, float $amount)
    {
        $this->summary[$person]['earning'] = ($this->summary[$person]['earning'] ?? 0) + $amount;
    }
}
