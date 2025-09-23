<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct()
    {
        // Require login + verified email (Jetstream)
        $this->middleware(['auth', 'verified']);
    }

    /**
     * GET /checkout
     * Show checkout form + cart summary.
     */
    public function create(Request $request)
    {
        $user  = $request->user();
        $items = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        $total = $items->sum(fn ($i) => $i->quantity * ($i->product->price ?? 0));

        return view('checkout.create', [
            'items' => $items,
            'total' => $total,
            'defaults' => [
                'shipping_name'    => $user->name,
                'shipping_phone'   => $user->phone ?? '',
                'shipping_address' => $user->address ?? '',
                'shipping_city'    => $user->city ?? '',
                'shipping_postal'  => $user->postal_code ?? '',
            ],
        ]);
    }

    /**
     * POST /checkout
     * Create order and order_items from cart, then clear cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'shipping_name'    => 'required|string|max:120',
            'shipping_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city'    => 'required|string',
            'shipping_postal'  => 'required|string|max:12',
        ]);

        $user  = $request->user();
        $items = CartItem::with('product')->where('user_id', $user->id)->get();
        abort_if($items->isEmpty(), 400, 'Cart empty');

        $total = $items->sum(fn ($i) => $i->quantity * ($i->product->price ?? 0));

        DB::transaction(function () use ($user, $items, $total, $request) {
            $order = Order::create([
                'user_id'         => $user->id,
                'status'          => 'pending',
                'tracking_no'     => null,
                'shipping_name'   => $request->shipping_name,
                'shipping_phone'  => $request->shipping_phone,
                'shipping_address'=> $request->shipping_address,
                'shipping_city'   => $request->shipping_city,
                'shipping_postal' => $request->shipping_postal,
                'total'           => $total,
            ]);

            foreach ($items as $i) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $i->product_id,
                    'qty'        => $i->quantity,
                    'price'      => $i->product->price ?? 0, // snapshot
                ]);

                // remove from cart
                $i->delete();
            }
        });

        return redirect()->route('dashboard')->with('status', 'Order placed!');
    }
}
