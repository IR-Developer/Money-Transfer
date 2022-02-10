<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class transferController extends Controller
{
    public function transfer(Request $request)
    {

        $rules = [
            'amount' => ['required', 'integer', 'min:100000', 'max:500000000'],
            'des_code' => ['required', 'string', 'exists:users,shomare_hesab', 'regex:/^[0-9]{11}$/i', 'size:11'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $user = Auth::user();
            $desUser = User::where('shomare_hesab', $request->des_code)->first();
            $ourTransferCode = rand(100, 999) . time() . rand(100, 999);

            // Check User Credit
            if ($user->credit - 100000 < $request->amount) {
                // Error Response
                return response()->json([
                    'status' => false,
                    'message' => 'موجودی شما برای این تراکنش کافی نمی باشد. (مبلغ قابل تراکنش ' . ($user->credit - 100000) . ' تومان می باشد.)',
                ], 401);
            }

            // Send Request To Bank
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer TOKEN_ID',
            ])->withBody(json_encode([
                "amount" => $request->amount,
                "description" => "شرح تراکنش",
                "destinationFirstname" => $desUser->firstname,
                "destinationLastname" => $desUser->lastname,
                "destinationNumber" => $desUser->shomare_sheba,
                "paymentNumber" => $ourTransferCode,
                "deposit" => $user->credit,
                "sourceFirstName" => $user->firstname,
                "sourceLastName" => $user->lastname,
            ]), 'application/json')->post('https://devbeta.finnotech.ir/oak/v2/clients/{clientId}/transferTo?trackId={trackId}'); //{clientId} va {trackId} ro nadashtam.

            // Get Bank Response
            $responseArray = json_decode($response->body());

            $info = [
                'sender_id' => $user->id,
                'des_id' => $desUser->id,
                'status' => ($responseArray['status'] === 'DONE') ? true : false,
                'amount' => $request->amount,
                'our_ref_code ' => $ourTransferCode,
                'bank_ref_code ' => $responseArray['result']['refCode'],
            ];

            // Create New Transfer Record
            if (Transfer::create($info)) {
                if ($responseArray['status'] === 'DONE') {
                    // Success Response
                    return response()->json([
                        'status' => true,
                        'message' => 'Transfer Done Successfully',
                    ], 200);
                }

                // Error Response
                return response()->json([
                    'status' => false,
                    'message' => $responseArray['result']['message'],
                ], 200);
            }
        }

        // Error Response
        return response()->json([
            'status' => false,
            'errors' => $validator->errors(),
        ], 401);
    }

    public function history()
    {
        $user = Auth::user();
        $allTransfers = $user->sends->merge($user->recives)->sortDesc();
        $resultArray = array();

        foreach ($allTransfers as $transfer){
            array_push($resultArray, [
                'type' => ($user->id === $transfer->src_id) ? 'send' : 'recive',
                'amount' => $transfer->amount,
                'status' => $transfer->status,
                'ref_code' => $transfer->bank_transfer_code,
                'timestamp' => strtotime($transfer->created_at),
            ]);
        }

        return response()->json($resultArray, 200);
    }
}
