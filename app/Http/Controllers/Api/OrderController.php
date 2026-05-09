<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::query()
            ->latest()
            ->get()
            ->map(fn (Order $order): array => $this->transformOrder($order));

        return response()->json([
            'data' => $orders,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'external_id' => ['nullable', 'string', 'max:100', 'unique:orders,external_id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'status' => ['required', 'string', Rule::in(['pending', 'processing', 'shipped', 'delivered'])],
            'currency' => ['required', 'string', 'size:3'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sku' => ['required', 'string', 'max:100'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $order = Order::create($validator->validated());

        Log::info('Order created', [
            'order_id' => $order->id,
            'external_id' => $order->external_id,
        ]);

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => $this->transformOrder($order),
        ], Response::HTTP_CREATED);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'data' => $this->transformOrder($order),
        ]);
    }

    private function transformOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'external_id' => $order->external_id,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'shipping_address' => $order->shipping_address,
            'status' => $order->status,
            'currency' => $order->currency,
            'total_amount' => (float) $order->total_amount,
            'items' => $order->items ?? [],
            'created_at' => optional($order->created_at)?->toISOString(),
            'updated_at' => optional($order->updated_at)?->toISOString(),
        ];
    }
}
