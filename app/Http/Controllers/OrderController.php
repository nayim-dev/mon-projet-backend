<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController 
{
    // Admin : toutes les commandes
    public function index()
    {
        return response()->json(Order::with(['user', 'items.product'])->get());
    }

    // User : ses propres commandes
    public function myOrders(Request $request)
    {
        $orders = Order::with('items.product')
                       ->where('user_id', $request->user()->id)
                       ->get();
        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return response()->json($order);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'               => 'required|array',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric',
        ]);

        $total = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'total'   => $total,
            'status'  => 'pending',
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);
        }

        return response()->json($order->load('items'), 201);
    }

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,confirmed,delivered,cancelled',
    ]);

    $order = Order::findOrFail($id);
    $order->update(['status' => $request->status]);

    // Messages de notification selon le statut
    $messages = [
        'confirmed'  => "✅ Votre commande #{$order->id} a été confirmée !",
        'delivered'  => "📦 Votre commande #{$order->id} a été livrée !",
        'cancelled'  => "❌ Votre commande #{$order->id} a été annulée.",
        'pending'    => "⏳ Votre commande #{$order->id} est en attente.",
    ];

    // Créer la notification pour l'utilisateur
    \App\Models\Notification::create([
        'user_id' => $order->user_id,
        'message' => $messages[$request->status],
    ]);

    return response()->json($order);
}
}