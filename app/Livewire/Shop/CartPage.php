<?php

namespace App\Livewire\Shop;

use App\Models\CartItem;
use Livewire\Component;

class CartPage extends Component
{
    public $items;
    public $subtotal;
    public $tax;
    public $total;

    protected $listeners = ['cartUpdated' => 'refreshCart'];

    public function mount()
    {
        $this->refreshCart();
    }

    public function refreshCart()
    {
        if (!auth()->check()) {
            $this->items = collect();
            $this->subtotal = 0;
            $this->tax = 0;
            $this->total = 0;
            return;
        }

        $this->items = CartItem::with('product')
                        ->where('user_id', auth()->id())
                        ->get();

        $this->subtotal = $this->items->sum(function($item) {
            return $item->quantity * ($item->product->price ?? 0);
        });

        $this->tax = $this->subtotal * 0.08; // 8% tax
        $this->total = $this->subtotal + $this->tax;
    }

    public function updateQuantity($itemId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeItem($itemId);
            return;
        }

        $item = CartItem::where('cart_id', $itemId)
                       ->where('user_id', auth()->id())
                       ->first();

        if ($item) {
            // Check stock availability
            if ($quantity > $item->product->stock) {
                session()->flash('error', 'Only ' . $item->product->stock . ' items available in stock.');
                return;
            }
            
            $item->update(['quantity' => $quantity]);
            $this->refreshCart(); // Refresh data immediately
            $this->dispatch('cartUpdated'); // For other components
            session()->flash('success', 'Cart updated successfully.');
        }
    }

    public function removeItem($itemId)
    {
        CartItem::where('cart_id', $itemId)
               ->where('user_id', auth()->id())
               ->delete();
               
        $this->refreshCart(); // Refresh data immediately
        $this->dispatch('cartUpdated'); // For other components
        session()->flash('success', 'Item removed from cart.');
    }

    public function clearCart()
    {
        CartItem::where('user_id', auth()->id())->delete();
        
        $this->refreshCart(); // Refresh data immediately
        $this->dispatch('cartUpdated'); // For other components
        session()->flash('success', 'Cart cleared successfully.');
    }

    public function render()
    {
        if (!auth()->check()) {
            return view('livewire.shop.cart-page', [
                'items' => collect(),
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
            ]);
        }

        $items = CartItem::with('product')
                        ->where('user_id', auth()->id())
                        ->get();

        $subtotal = $items->sum(function($item) {
            return $item->quantity * ($item->product->price ?? 0);
        });

        $tax = $subtotal * 0.08; // 8% tax
        $total = $subtotal + $tax;

        return view('livewire.shop.cart-page', [
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }
}
