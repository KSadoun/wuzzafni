<?php
namespace App\Http\Controllers;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PayPalController extends Controller
{
    // 1. Create the Order and return the approval link
    public function createPayment(): JsonResponse
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "100.00"
                    ]
                ]
            ],
            "application_context" => [
                "cancel_url" => route('paypal.cancel'),
                "return_url" => route('paypal.success'),
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return response()->json([
                        'status' => 'success',
                        'order_id' => $response['id'],
                        'approval_url' => $link['href']
                    ], 200);
                }
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to create order'], 500);
    }

    // 2. Capture the payment using the token/order ID sent by the client
    public function success(Request $request): JsonResponse
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        
        // PayPal appends 'token' to the return URL, which represents the PayPal Order ID
        $token = $request->get('token'); 
        $response = $provider->capturePaymentOrder($token);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            return response()->json([
                'status' => 'COMPLETED',
                'message' => 'Payment captured successfully!',
                'data' => $response
            ], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'Payment capture failed'], 400);
    }

    // 3. Handle cancellation
    public function cancel(): JsonResponse
    {
        return response()->json(['status' => 'cancelled', 'message' => 'User cancelled the payment.'], 200);
    }
}