@props(['product'])

<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
    <a href="{{ route('products.show', $product->slug) }}">
        @if($product->primaryImage)
            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                 alt="{{ $product->name }}" 
                 class="w-full h-48 object-cover">
        @else
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                <i class="fas fa-image text-gray-400 text-4xl"></i>
            </div>
        @endif
    </a>
    
    <div class="p-4">
        <div class="flex justify-between items-start">
            <div>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded">
                    {{ $product->category->name ?? 'Uncategorized' }}
                </span>
                @if($product->brand)
                    <span class="text-xs text-gray-500 ml-2">
                        {{ $product->brand->name }}
                    </span>
                @endif
            </div>
            
            @auth
                <button class="wishlist-toggle text-gray-400 hover:text-red-500" 
                        data-product-id="{{ $product->id }}">
                    <i class="far fa-heart"></i>
                </button>
            @endauth
        </div>
        
        <h3 class="font-semibold text-lg mt-2 mb-1">
            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-blue-600">
                {{ Str::limit($product->name, 40) }}
            </a>
        </h3>
        
        <div class="flex items-center mb-3">
            @php
                $rating = $product->reviews->avg('rating') ?? 0;
                $reviewCount = $product->reviews->count();
            @endphp
            
            <div class="flex text-yellow-400">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($rating))
                        <i class="fas fa-star"></i>
                    @elseif($i == ceil($rating) && $rating - floor($rating) >= 0.5)
                        <i class="fas fa-star-half-alt"></i>
                    @else
                        <i class="far fa-star"></i>
                    @endif
                @endfor
            </div>
            <span class="text-sm text-gray-500 ml-2">
                ({{ $reviewCount }})
            </span>
        </div>
        
        <div class="flex items-center justify-between">
            <div>
                @if($product->discount_price)
                    <div class="flex items-center">
                        <span class="text-xl font-bold text-red-600">
                            Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                        </span>
                        <span class="text-sm text-gray-500 line-through ml-2">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                        <span class="text-xs font-semibold bg-red-100 text-red-800 px-2 py-1 rounded ml-2">
                            -{{ $product->discount_percentage }}%
                        </span>
                    </div>
                @else
                    <span class="text-xl font-bold text-gray-800">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>
                @endif
            </div>
            
            <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-cart-plus"></i>
                </button>
            </form>
        </div>
        
        @if($product->stock <= 0)
            <div class="mt-2">
                <span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded">
                    Stok Habis
                </span>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wishlist Toggle
    document.querySelectorAll('.wishlist-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const icon = this.querySelector('i');
            
            fetch("{{ route('wishlist.toggle') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'added') {
                    icon.classList.remove('far', 'fa-heart');
                    icon.classList.add('fas', 'fa-heart', 'text-red-500');
                } else {
                    icon.classList.remove('fas', 'fa-heart', 'text-red-500');
                    icon.classList.add('far', 'fa-heart');
                }
                
                // Show notification
                alert(data.message);
            });
        });
    });
    
    // Add to Cart
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: this.querySelector('[name="product_id"]').value,
                    quantity: this.querySelector('[name="quantity"]').value
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || 'Produk ditambahkan ke keranjang');
                // Update cart count
                // You might want to update cart count indicator here
            });
        });
    });
});
</script>
@endpush