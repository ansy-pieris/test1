@extends('layouts.app')

@section('title', 'Manage Products - Admin')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <main class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-semibold mb-6">Manage Products</h1>

        <!-- Add Product Form -->
        <div id="add-product-message" class="relative"></div>
        <section class="bg-white p-6 rounded shadow mb-10">
            <h2 class="text-xl font-semibold mb-4">Add New Product</h2>
            <form id="add-product-form" class="space-y-4" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <div>
                    <label for="product_name" class="block font-medium mb-1">Product Name</label>
                    <input
                        type="text" id="product_name" name="name" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                        pattern=".{2,100}" title="Product name should be 2 to 100 characters" />
                </div>

                <div>
                    <label for="description" class="block font-medium mb-1">Description</label>
                    <textarea
                        id="description" name="description" rows="3" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                        minlength="10" maxlength="1000" title="Description should be between 10 and 1000 characters"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="price" class="block font-medium mb-1">Price (Rs.)</label>
                        <input
                            type="number" step="0.01" min="0" id="price" name="price" required
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                            title="Enter a valid price" />
                    </div>

                    <div>
                        <label for="quantity" class="block font-medium mb-1">Quantity Available</label>
                        <input
                            type="number" id="quantity" name="stock" min="0" required
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                            title="Quantity cannot be negative" />
                    </div>

                    <div>
                        <label for="category" class="block font-medium mb-1">Category</label>
                        <select
                            id="category" name="category_id" required
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="image" class="block font-medium mb-1">Upload Image</label>
                    <input
                        type="file" id="image" name="image" accept="image/*"
                        class="w-full mb-2" onchange="resizeAndPreviewImage(event)" />
                    <div id="image-preview" class="mt-2">
                        <img src="" alt="Preview" id="preview" class="h-24 rounded hidden" />
                    </div>
                </div>

                <button type="submit" class="bg-black text-white px-5 py-2 rounded hover:bg-gray-800">Add Product</button>
            </form>
        </section>

        <!-- Search and Filter -->
        <section class="bg-white p-4 rounded shadow mb-6">
            <form method="GET" action="{{ route('admin.products') }}" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-2">
                    <input type="search" id="searchInput" name="search" placeholder="Search products..."
                        class="border border-gray-300 rounded px-3 py-2 w-full md:w-64" value="{{ request('search') }}" />
                </div>
                
                <div class="flex items-center gap-2">
                    <select id="filterCategory" name="category" class="border border-gray-300 rounded px-3 py-2 w-full md:w-auto" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </section>

        <!-- Products Table -->
        <section class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Products List</h2>
            <div class="overflow-x-auto">
                <table id="productsTable" class="min-w-full table-auto border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="border border-gray-300 px-4 py-2">Product ID</th>
                            <th class="border border-gray-300 px-4 py-2">Name</th>
                            <th class="border border-gray-300 px-4 py-2">Description</th>
                            <th class="border border-gray-300 px-4 py-2">Category</th>
                            <th class="border border-gray-300 px-4 py-2">Price</th>
                            <th class="border border-gray-300 px-4 py-2">Quantity</th>
                            <th class="border border-gray-300 px-4 py-2">Image</th>
                            <th class="border border-gray-300 px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        @if($products->count() > 0)
                            @foreach($products as $product)
                                <tr data-category="{{ $product->category_id }}">
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $product->product_id }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $product->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ Str::limit($product->description, 50) }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $product->category->name ?? 'N/A' }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-right">Rs. {{ number_format($product->price, 2) }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $product->stock }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        @if($product->image)
                                            <img src="{{ Storage::url('products/' . $product->image) }}" alt="Product Image" class="mx-auto rounded w-26 h-28 object-cover" />
                                        @else
                                            <span class="text-gray-500">No image</span>
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center space-x-2">
                                        <!-- Edit Button -->
                                        <button
                                            type="button"
                                            class="text-blue-600 hover:underline edit-product-btn"
                                            data-product-id="{{ $product->product_id }}"
                                            data-product-name="{{ $product->name }}"
                                            data-description="{{ $product->description }}"
                                            data-price="{{ $product->price }}"
                                            data-quantity="{{ $product->stock }}"
                                            data-category="{{ $product->category_id }}"
                                            data-image="{{ $product->image }}"
                                        >Edit</button>

                                        <!-- Delete Button -->
                                        <button
                                            type="button"
                                            class="text-red-600 hover:underline delete-product-btn"
                                            data-product-id="{{ $product->product_id }}"
                                        >Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-4">No products found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="mt-4 flex justify-center space-x-2" id="paginationControls"></div>
        </section>

        <!-- Edit Product Modal -->
        <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-2xl relative">
                <button type="button" onclick="closeEditModal()" class="absolute top-2 right-2 text-gray-600 hover:text-black">&times;</button>
                <h2 class="text-xl font-semibold mb-4">Edit Product</h2>
                <form id="edit-product-form" class="space-y-4" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="product_id" value="" />
                    <div>
                        <label class="block font-medium mb-1">Product Name</label>
                        <input
                            type="text" name="edit_product_name" value=""
                            class="w-full border border-gray-300 rounded px-3 py-2"
                            required pattern=".{2,100}" title="Product name should be 2 to 100 characters" />
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Description</label>
                        <textarea
                            name="edit_description" rows="3"
                            class="w-full border border-gray-300 rounded px-3 py-2"
                            required minlength="10" maxlength="1000" title="Description should be between 10 and 1000 characters"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-medium mb-1">Price</label>
                            <input
                                type="number" step="0.01" min="0" name="edit_price" value=""
                                class="w-full border border-gray-300 rounded px-3 py-2"
                                required title="Enter a valid price" />
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Quantity</label>
                            <input
                                type="number" min="0" name="edit_quantity" value=""
                                class="w-full border border-gray-300 rounded px-3 py-2"
                                required title="Quantity cannot be negative" />
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Category</label>
                            <select
                                name="edit_category"
                                class="w-full border border-gray-300 rounded px-3 py-2"
                                required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Image</label>
                        <input
                            type="file"
                            name="image"
                            accept="image/*"
                            class="w-full border border-gray-300 rounded px-3 py-2"
                            onchange="resizeAndPreviewImageEdit(event)"
                        />
                        <div id="edit-image-preview" class="mt-2">
                            <img src="" alt="Preview" id="edit-preview" class="h-24 rounded hidden" />
                        </div>
                    </div>
                    <button
                        type="submit"
                        class="bg-black text-white px-5 py-2 rounded hover:bg-gray-800"
                    >
                        Update Product
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
// ========== AJAX ADD PRODUCT ==========
document.getElementById('add-product-form').addEventListener('submit', function(e) {
    e.preventDefault();
    let form = e.target;
    let formData = new FormData(form);

    fetch('{{ route("admin.products.store") }}', {
        method: 'POST',
        body: formData,
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        showProductMessage(data.success || data.error, !!data.success);
        if (data.success) {
            form.reset();
            document.getElementById('preview').classList.add('hidden');
            reloadTableFromHTML();
        }
    })
    .catch(error => {
        showProductMessage('An error occurred. Please try again.', false);
    });
});

// ========== AJAX DELETE PRODUCT ==========
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('delete-product-btn')) {
        e.preventDefault();
        const productId = e.target.getAttribute('data-product-id');
        if (!confirm('Are you sure you want to delete this product?')) return;

        fetch('{{ route("admin.products.destroy") }}', {
            method: 'POST',
            body: new URLSearchParams({ 
                product_id: productId,
                _token: '{{ csrf_token() }}'
            }),
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(res => res.json())
        .then(data => {
            showProductMessage(data.success || data.error, !!data.success);
            if (data.success) reloadTableFromHTML();
        })
        .catch(error => {
            showProductMessage('An error occurred. Please try again.', false);
        });
    }
});

// ========== OPEN EDIT MODAL + FILL FORM ==========
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('edit-product-btn')) {
        e.preventDefault();

        // Fill modal fields
        document.querySelector('#edit-product-form [name="product_id"]').value = e.target.getAttribute('data-product-id');
        document.querySelector('#edit-product-form [name="edit_product_name"]').value = e.target.getAttribute('data-product-name');
        document.querySelector('#edit-product-form [name="edit_description"]').value = e.target.getAttribute('data-description');
        document.querySelector('#edit-product-form [name="edit_price"]').value = e.target.getAttribute('data-price');
        document.querySelector('#edit-product-form [name="edit_quantity"]').value = e.target.getAttribute('data-quantity');
        document.querySelector('#edit-product-form [name="edit_category"]').value = e.target.getAttribute('data-category');
        
        // Image preview
        const img = document.getElementById('edit-preview');
        const imgPath = e.target.getAttribute('data-image');
        if (imgPath) {
            img.src = "{{ Storage::url('products/') }}" + imgPath;
            img.classList.remove('hidden');
        } else {
            img.classList.add('hidden');
        }
        
        openEditModal();
    }
});

// ========== AJAX EDIT PRODUCT SUBMIT ==========
document.getElementById('edit-product-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    // Map modal field names to backend expectation
    formData.set('name', formData.get('edit_product_name'));
    formData.set('description', formData.get('edit_description'));
    formData.set('price', formData.get('edit_price'));
    formData.set('stock', formData.get('edit_quantity'));
    formData.set('category_id', formData.get('edit_category'));
    formData.delete('edit_product_name');
    formData.delete('edit_description');
    formData.delete('edit_price');
    formData.delete('edit_quantity');
    formData.delete('edit_category');

    fetch('{{ route("admin.products.update") }}', {
        method: 'POST',
        body: formData,
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        showProductMessage(data.success || data.error, !!data.success);
        if (data.success) {
            closeEditModal();
            reloadTableFromHTML();
        }
    })
    .catch(error => {
        showProductMessage('An error occurred. Please try again.', false);
    });
});

// ========== Show Alert Message ==========
function showProductMessage(msg, isSuccess) {
    let msgDiv = document.getElementById('add-product-message');
    msgDiv.innerHTML = '';

    let alert = document.createElement('div');
    alert.className = 'transition-all duration-500 p-4 flex items-center gap-2 rounded shadow-lg border text-base font-medium mb-4 ' +
        (isSuccess
            ? 'bg-green-100 border-green-300 text-green-900'
            : 'bg-red-100 border-red-300 text-red-900');

    alert.innerHTML = `
        <span>
            ${isSuccess
                ? '<svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5 text-green-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5 text-red-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'}
        </span>
        <span>${msg}</span>
    `;

    msgDiv.appendChild(alert);

    setTimeout(() => {
        alert.classList.add('opacity-0', 'translate-y-3');
        setTimeout(() => alert.remove(), 500);
    }, 3000);
}

// ========== Reload Products Table Body ==========
function reloadTableFromHTML() {
    fetch(window.location.href)
        .then(res => res.text())
        .then(html => {
            let temp = document.createElement('div');
            temp.innerHTML = html;
            let newBody = temp.querySelector('#productsTableBody');
            if (newBody) {
                document.getElementById('productsTableBody').innerHTML = newBody.innerHTML;
                filterProductsAndPaginate();
            }
        });
}

// ====== Image Preview + Resize =====
function resizeAndPreviewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const canvas = document.createElement('canvas');
            const maxWidth = 300;
            let width = img.width;
            let height = img.height;

            if (width > maxWidth) {
                height = height * (maxWidth / width);
                width = maxWidth;
            }
            canvas.width = width;
            canvas.height = height;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            preview.src = canvas.toDataURL('image/jpeg');
            preview.classList.remove('hidden');
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function resizeAndPreviewImageEdit(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('edit-preview');
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const canvas = document.createElement('canvas');
            const maxWidth = 300;
            let width = img.width;
            let height = img.height;

            if (width > maxWidth) {
                height = height * (maxWidth / width);
                width = maxWidth;
            }
            canvas.width = width;
            canvas.height = height;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            preview.src = canvas.toDataURL('image/jpeg');
            preview.classList.remove('hidden');
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// ====== Pagination and Filtering ======
const rowsPerPage = 10;
let currentPage = 1;
let filteredRows = [];

function filterProductsAndPaginate() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const selectedCategory = document.getElementById('filterCategory').value;
    const allRows = Array.from(document.querySelectorAll('#productsTableBody tr'));

    filteredRows = allRows.filter(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const category = row.dataset.category;
        const matchesSearch = name.includes(searchTerm);
        const matchesCategory = !selectedCategory || category === selectedCategory;
        return matchesSearch && matchesCategory;
    });

    currentPage = 1;
    renderPage();
}

function renderPage() {
    const paginationControls = document.getElementById('paginationControls');
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);

    Array.from(document.querySelectorAll('#productsTableBody tr')).forEach(row => row.style.display = 'none');

    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    filteredRows.slice(start, end).forEach(row => row.style.display = '');

    paginationControls.innerHTML = '';

    if (totalPages > 1) {
        const prevBtn = document.createElement('button');
        prevBtn.textContent = 'Previous';
        prevBtn.className = 'px-3 py-1 border rounded disabled:opacity-50';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                renderPage();
            }
        };
        paginationControls.appendChild(prevBtn);

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `px-3 py-1 border rounded ${i === currentPage ? 'bg-black text-white' : 'bg-white text-black'}`;
            btn.onclick = () => {
                currentPage = i;
                renderPage();
            };
            paginationControls.appendChild(btn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Next';
        nextBtn.className = 'px-3 py-1 border rounded disabled:opacity-50';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderPage();
            }
        };
        paginationControls.appendChild(nextBtn);
    }
}

// ====== Modal Functions ======
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('searchInput')?.addEventListener('input', filterProductsAndPaginate);
    document.getElementById('filterCategory')?.addEventListener('change', filterProductsAndPaginate);
    filterProductsAndPaginate();
});
</script>
@endsection