@extends('layouts.guest')
@section('title', $product->name)

@php use Illuminate\Support\Str; @endphp

@section('content')
  {{-- Success / Error flashes --}}
  @if (session('success'))
    <script>alert(@json(session('success')));</script>
  @endif
  @if (session('error'))
    <div class="max-w-6xl mx-auto mt-24 px-6 text-red-400">{{ session('error') }}</div>
  @endif

  <div class="min-h-screen bg-black text-white pt-24 px-6 lg:px-20 pb-16">
    <div class="max-w-6xl mx-auto flex flex-col lg:flex-row items-center gap-10">
      {{-- Product Image --}}
      <div class="w-full lg:w-1/2">
        @php
          $img = $product->image ?: '';
          $src = Str::startsWith($img, ['http', 'storage/', '/images/'])
                  ? asset($img)
                  : asset('storage/products/'.$img);
        @endphp
        <div class="w-full h-96 lg:h-[500px] overflow-hidden bg-white rounded-xl shadow-lg">
          <img src="{{ $src }}"
               alt="{{ $product->name }}"
               class="w-full h-full object-cover">
        </div>
      </div>

      {{-- Product Details + Add to Cart --}}
      <div class="w-full lg:w-1/2 space-y-6">
        <h1 class="text-4xl font-bold">{{ $product->name }}</h1>
        <p class="text-2xl font-semibold text-blue-300">Rs. {{ number_format($product->price, 2) }}</p>

        <p class="text-gray-300 whitespace-pre-line">{{ $product->description }}</p>

        {{-- Stock info --}}
        @if ($product->stock <= 0)
          <p class="text-sm text-red-500 font-semibold">Out of stock</p>
        @elseif ($product->stock < 10)
          <p class="text-sm text-yellow-400">Only {{ $product->stock }} left in stock</p>
        @else
          <p class="text-sm text-green-400">In Stock</p>
        @endif

        <p class="text-sm text-gray-400">
          Category: {{ $product->category->name ?? 'Uncategorized' }}
        </p>

        {{-- Add to Cart (auth required) --}}
        @if ($product->stock > 0)
          @auth
            <div class="space-y-4 mt-4">
              <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                <div>
                  <label for="quantity" class="block mb-2 font-semibold">Quantity</label>
                  <input type="number" name="quantity" min="1" max="{{ $product->stock }}" value="1"
                         class="w-24 bg-black border border-white text-white px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-white">
                </div>
                <button type="submit" class="bg-white text-black font-bold px-6 py-3 rounded hover:bg-gray-300 transition">
                  Add to Cart
                </button>
              </form>
            </div>
          @else
            <a href="{{ route('login') }}"
               class="inline-block mt-4 bg-white/0 border border-white/30 text-white px-6 py-3 rounded hover:bg-white/5 transition">
              Login to buy
            </a>
          @endauth
        @else
          <div class="mt-6">
            <button disabled
                    class="bg-gray-600 text-white font-bold px-6 py-3 rounded cursor-not-allowed opacity-50">
              Out of Stock
            </button>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
