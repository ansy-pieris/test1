<?php

namespace App\Livewire\Shop;

use App\Models\CartItem;
use Livewire\Component;

class CartCounter extends Component
{
    protected $listeners = ['cartUpdated' => '$refresh'];

    public function render()
    {
        $count = auth()->check() 
            ? CartItem::where('user_id', auth()->id())->sum('quantity')
            : 0;

        \Log::info('CartCounter render called', ['count' => $count, 'user_id' => auth()->id()]);

        return view('livewire.shop.cart-counter', compact('count'));
    }
}