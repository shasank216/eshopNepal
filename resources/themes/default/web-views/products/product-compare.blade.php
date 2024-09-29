@extends('layouts.front-end.app')

@section('title', 'Product Compare')

@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset('public/assets/front-end/css/product-details.css') }}" />
@endpush

@section('content')
    <!-- Bread Crumb -->
    <div class="braed-crumbs">
        <div class="container-fluid">
            <span>
                <a href="{{ url('/') }}" class="home">
                    <i class="fa fa-home" aria-hidden="true"></i>
                    Home
                </a>
            </span>
            <i class="fa fa-angle-right" aria-hidden="true"></i>
            <span class="text-capitalize product">Product Compare</span>
        </div>
    </div>
    <!-- Bread Crumb Ends -->

    <section class="compare-section">
        <div class="product-compare-section container">
            @forelse ($products as $product)
                <div class="product-compare-table">
                    <div class="compare-product_item" data-product-id="{{ $product->id }}"
                        data-category-id="{{ $product->category_id }}">
                        <img src="{{ getValidImage('storage/app/public/product/thumbnail/' . $product->thumbnail, 'product') }}"
                            alt="{{ $product->name }}">

                        <div class="product-compart-title my-2">
                            <a href="">{{ $product->name }}</a>
                        </div>

                        <div class="compare-product-price d-flex flex-wrap align-items-center gap-8 my-2">
                            <span class="text-accent text-dark">Rs.{{ number_format($product->unit_price, 2) }}
                                @if ($product->discount > 0)
                                    <del
                                        class="compare-discount-product-price">Rs.{{ number_format($product->unit_price + $product->discount, 2) }}</del>
                                @endif
                            </span>
                        </div>
                        

                        <div class="product-compare-description">
                            <div class="product-compare-rating my-3">
                                <div class="compare-stars">
                                    {{ number_format($product->average_rating, 1) }}
                                    <i class="fa fa-star text-white" aria-hidden="true"></i>
                                </div>
                                <div class="compare-review-count">
                                    <a href="#" class="">{{ number_format($product->reviews_count) }}
                                        Reviews</a>
                                </div>
                            </div>

                            <div class="product-compare-specification my-3">
                                <span>{!! $product->details ?: 'No specifications available.' !!}</span>
                            </div>

                            <div class="product-compare-variants my-2">
                                <p class="varients-title m-0">Color</p>
                                <p class="varients-content m-0">
                                    @php
                                        $colors = json_decode($product->colors, true);
                                        $colorNames = $colors
                                            ? implode(
                                                ', ',
                                                array_map(
                                                    fn($color) => '<span style="background-color: ' .
                                                        htmlspecialchars($color) .
                                                        '; display: inline-block; width: 20px; height: 20px; border: 1px solid #ddd; border-radius: 50%;"></span>',
                                                    $colors,
                                                ),
                                            )
                                            : 'No colors available';
                                    @endphp
                                    {!! $colorNames !!}
                                </p>
                            </div>
                        </div>

                        <div class="compare-buynow my-3">
                            <button type="button" class="btn add-to-cart" data-id="{{ $product->id }}"
                                style="background-color: white; color: black;">
                                <i class="czi-cart text-black me-2"></i>
                                <span class="string-limit text-black">Add to cart</span>
                            </button>
                            <button type="button" class="btn remove-from-compare" data-id="{{ $product->id }}"
                                style="background-color: red; color: white;">
                                <i class="fa fa-times text-white me-2"></i>
                                <span class="string-limit text-white">Remove</span>
                            </button>
                        </div>
                    </div>
                </div>

            @empty
                <p>No products to compare.</p>
            @endforelse
        </div>
    </section>

@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Event listener for the Add to Cart button
            $('.add-to-cart').on('click', function(e) {
                e.preventDefault();

                // Get product ID dynamically from the form
                var productId = $(this).closest('form').find('input[name="id"]').val();

                // Set up the AJAX request
                $.ajax({
                    // alert(productId);
                    url: "{{ route('cart.add') }}", // Add to cart route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Pass CSRF token
                        id: productId, // Pass the product ID
                        quantity: 1 // You can change the quantity if needed
                    },
                    success: function(response) {
                        if (response.status === 1) {
                            // console.log(response.cart);
                            toastr.success(response.message);
                            // alert('Product added to cart successfully!'); // You can replace with Toast message or a cart update
                            // You can update your cart UI here, for example, update cart counter, etc.
                            setTimeout(function() {
                                location.reload();
                            }, 1000); // 1000ms = 1 second delay before reload
                        } else {
                            // alert('Failed to add product to cart!');
                            toastr.error('Something went wrong, please try again.');
                        }
                    },
                    error: function(response) {
                        // alert('Error occurred while adding to cart!');
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
    <!-- Add any custom scripts here -->
@endpush
