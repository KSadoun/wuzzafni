<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Payment;

class PaymentService
{
    public function calculateHiringFee(Application $application): float
    {
        $job = $application->job;
        $level = strtolower($job->experience_level ?? 'default');
        $fees = config('payment.hiring_fees', []);

        return (float) ($fees[$level] ?? $fees['default'] ?? 100.00);
    }

    public function createPendingPayment(Application $application): Payment
    {
        $existing = $application->payment;

        if ($existing) {
            return $existing;
        }

        return Payment::create([
            'employer_profile_id' => $application->employer_profile_id,
            'application_id' => $application->id,
            'amount' => $this->calculateHiringFee($application),
            'payment_method' => 'paypal',
            'payment_status' => 'pending',
        ]);
    }

    public function markAsPaid(Payment $payment, string $transactionId): Payment
    {
        $payment->update([
            'payment_status' => 'paid',
            'transaction_id' => $transactionId,
        ]);

        return $payment->fresh();
    }

    public function markAsFailed(Payment $payment): Payment
    {
        $payment->update(['payment_status' => 'failed']);

        return $payment->fresh();
    }
}
