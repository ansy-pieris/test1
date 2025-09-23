<div class="space-y-4">
  <input class="border p-2 w-full" placeholder="Search…" wire:model.debounce.400ms="search" />
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach($products as $p)
      <div class="rounded border p-3">
        <h3 class="font-semibold">{{ $p->name }}</h3>
        <p class="text-sm">{{ \Illuminate\Support\Str::limit($p->description, 80) }}</p>
        <div class="mt-2 flex justify-between">
          <span>LKR {{ number_format($p->price, 2) }}</span>
          <button class="text-blue-600" wire:click="$dispatch('add-to-cart', { id: {{ $p->id }} })">
            Add
          </button>
        </div>
      </div>
    @endforeach
  </div>
  {{ $products->links() }}
</div>
