@extends('layouts.guest')
@section('title', 'Home')

@section('content')
<div class="pt-20">

  {{-- Carousel Section --}}
  <section class="relative w-full h-[500px] overflow-hidden">
    <div id="carousel" class="relative w-full h-full" data-carousel="slide">
      <div class="relative h-full overflow-hidden rounded-lg">
        {{-- Item 1 --}}
        <div class="hidden duration-[1200ms] ease-in-out" data-carousel-item="active">
          <img src="{{ asset('images/Ares.jpg') }}" class="absolute block w-full h-full object-contain" alt="Slide 1">
        </div>
        {{-- Item 2 --}}
        <div class="hidden duration-[1200ms] ease-in-out" data-carousel-item>
          <img src="{{ asset('images/Ares2.jpg') }}" class="absolute block w-full h-full object-contain" alt="Slide 2">
        </div>
        {{-- Item 3 --}}
        <div class="hidden duration-[1200ms] ease-in-out" data-carousel-item>
          <img src="{{ asset('images/Ares3.jpg') }}" class="absolute block w-full h-full object-contain" alt="Slide 3">
        </div>
      </div>

      {{-- Slider indicators --}}
      <div class="absolute z-30 flex space-x-3 -translate-x-1/2 bottom-5 left-1/2">
        <button type="button" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white" aria-current="true" aria-label="Slide 1" data-carousel-slide-to="0"></button>
        <button type="button" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white" aria-label="Slide 2" data-carousel-slide-to="1"></button>
        <button type="button" class="w-3 h-3 rounded-full bg-white/50 hover:bg-white" aria-label="Slide 3" data-carousel-slide-to="2"></button>
      </div>

      {{-- Slider controls --}}
      <button type="button" class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/30 group-hover:bg-black/50">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </span>
      </button>
      <button type="button" class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/30 group-hover:bg-black/50">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </span>
      </button>
    </div>
  </section>

  {{-- Welcome Message --}}
  <div class="container mx-auto py-10 text-center">
    <h2 class="text-4xl font-bold mb-4">Welcome to ARES</h2>
    <p class="text-lg max-w-2xl mx-auto">Where power meets fashion. Discover bold apparel, empowering accessories, and footwear designed to make you stand out.</p>
  </div>

  {{-- Category Cards --}}
  <section class="container mx-auto px-4 py-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
    @php
      $categories = [
        ['title' => 'Men',        'img' => asset('images/Ares.jpg'), 'slug' => 'men'],
        ['title' => 'Women',      'img' => asset('images/Ares.jpg'), 'slug' => 'women'],
        ['title' => 'Footwear',   'img' => asset('images/Ares.jpg'), 'slug' => 'footwear'],
        ['title' => 'Accessories','img' => asset('images/Ares.jpg'), 'slug' => 'accessories'],
      ];
    @endphp

    @foreach ($categories as $cat)
      <a href="{{ route('products.category', ['category' => $cat['slug']]) }}" class="relative group overflow-hidden rounded-lg shadow-lg">
        <img src="{{ $cat['img'] }}" alt="{{ $cat['title'] }}" class="w-full h-100 object-cover transition-transform duration-700 group-hover:scale-110">
        <div class="absolute inset-0 bg-black bg-opacity-20 flex flex-col justify-center items-center text-center">
          <h3 class="text-xl font-semibold text-white mb-2">{{ $cat['title'] }}</h3>
          <span class="bg-white text-black px-4 py-2 rounded hover:bg-gray-200 transition">Shop Now</span>
        </div>
      </a>
    @endforeach
  </section>

  {{-- Featured Products --}}
  <section class="container mx-auto px-4 py-10">
    <h2 class="text-3xl font-bold text-center mb-8">Featured Products</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @for ($i = 1; $i <= 6; $i++)
        {{-- If you have real product slugs, swap the route below to products.show --}}
        <a href="#" class="bg-white text-black rounded-lg overflow-hidden shadow hover:shadow-lg transition">
          <img src="{{ asset('images/featured' . $i . '.jpg') }}" alt="Featured Product {{ $i }}" class="w-full h-60 object-cover">
          <div class="p-4 text-center">
            <h4 class="text-lg font-semibold mb-1">Featured Item {{ $i }}</h4>
            <p class="text-sm text-gray-700">Top-selling product of the month</p>
          </div>
        </a>
      @endfor
    </div>
  </section>

  {{-- Custom Uploaded Images --}}
  <section class="container mx-auto px-4 py-10">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <img src="{{ asset('images/Ares2.jpg') }}" alt="ARES Look 1" class="w-full rounded-lg shadow-lg">
      <img src="{{ asset('images/Ares.jpg') }}" alt="ARES Look 2" class="w-full rounded-lg shadow-lg">
    </div>
  </section>

  {{-- Flowbite Carousel JavaScript --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</div>
@endsection
