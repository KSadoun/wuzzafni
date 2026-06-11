<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function createPayment(Request $request): JsonResponse
    {
        $request->validate([
            'application_id' => ['required', 'integer', 'exists:applications,id'],
        ]);

        $application = Application::with(['job', 'payment'])->findOrFail($request->application_id);
        $this->authorize('view', $application);

        if ($application->status !== 'accepted') {
            return response()->json(['message' => 'Payment is only available for accepted applications.'], 422);
        }

        $payment = $application->payment ?? $this->paymentService->createPendingPayment($application);

        if ($payment->payment_status === 'paid') {
            return response()->json(['message' => 'This application has already been paid for.'], 422);
        }

        $amount = number_format((float) $payment->amount, 2, '.', '');
        $currency = config('payment.currency', 'USD');

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => (string) $payment->id,
                    'description' => 'Hiring fee for '.$application->job->title,
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => $amount,
                    ],
                ],
            ],
            'application_context' => [
                'cancel_url' => url("/employer/applications/{$application->id}/payment/cancel"),
                'return_url' => url("/employer/applications/{$application->id}/payment/success"),
            ],
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            $payment->update(['transaction_id' => $response['id']]);

            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return response()->json([
                        'status' => 'success',
                        'order_id' => $response['id'],
                        'approval_url' => $link['href'],
                        'amount' => $amount,
                        'currency' => $currency,
                    ]);
                }
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to create PayPal order.'], 500);
    }

    public function success(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'application_id' => ['required', 'integer', 'exists:applications,id'],
        ]);

        $application = Application::with('payment')->findOrFail($request->application_id);
        $this->authorize('view', $application);

        $payment = $application->payment;

        if (! $payment) {
            return response()->json(['message' => 'No payment record found for this application.'], 404);
        }

        if ($payment->payment_status === 'paid') {
            return response()->json([
                'status' => 'COMPLETED',
                'message' => 'Payment already completed.',
                'payment' => $payment,
            ]);
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $token = $request->get('token');
        $response = $provider->capturePaymentOrder($token);

        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            $captureId = $response['purchase_units'][0]['payments']['captures'][0]['id'] ?? $token;
            $this->paymentService->markAsPaid($payment, $captureId);

            return response()->json([
                'status' => 'COMPLETED',
                'message' => 'Payment captured successfully!',
                'payment' => $payment->fresh(),
            ]);
        }

        $this->paymentService->markAsFailed($payment);

        return response()->json(['status' => 'error', 'message' => 'Payment capture failed.'], 400);
    }

    public function cancel(Request $request): JsonResponse
    {
        if ($request->filled('application_id')) {
            $application = Application::with('payment')->find($request->application_id);

            if ($application) {
                $this->authorize('view', $application);
            }
        }

        return response()->json(['status' => 'cancelled', 'message' => 'Payment was cancelled.']);
    }
}
