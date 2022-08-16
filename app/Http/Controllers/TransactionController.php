<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Enums\UserRoleEnum;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::filterUserData()
                                ->limit($request->query('limit') ?? null)
                                ->sortProduct($request->query('sortby') ?? null)
                                ->orderProduct($request->query('orderby') ?? null)
                                ->get();
        return TransactionResource::collection($transactions);
    }

    public function store(StoreTransactionRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $product = Product::where('uuid',$data['product_uuid'])->first();
            $isStockProductEnough = $product->quantity >= $data['amount'];

            if (! $isStockProductEnough) {
                throw new \Exception('Product stock is not enough');
            }

            $basePrice = $product->price * $data['amount'];

            $transactionData = [];
            $transactionData['amount'] = $data['amount'];
            $transactionData['user_id'] = auth()->user()->id;
            $transactionData['tax'] = ($basePrice * 10) / 100;
            $transactionData['admin_fee'] = (($basePrice + $transactionData['tax']) * 5) / 100;
            $transactionData['total'] = $basePrice + $transactionData['tax'] + $transactionData['admin_fee'];

            $transaction = $product->transactions()->create($transactionData);

            $product->decrement('quantity', $data['amount']);

            DB::commit();

            return response()->json([
                'message' => 'Transaction created successfully',
                'transaction_detail' => $transaction
            ], 201);
        } catch(\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Transaction $transaction)
    {
        $isCustomerRole = auth()->user()->role == UserRoleEnum::CUSTOMER;
        $isTransactionBelongsToCustomer = $transaction->user_id != auth()->user()->id;

        if ($isCustomerRole) {
            if ($isTransactionBelongsToCustomer) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        return new TransactionResource($transaction);
    }
}
