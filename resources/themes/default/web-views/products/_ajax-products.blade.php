@if (count($products) > 0)
    @php($decimal_point_settings = getWebConfig(name: 'decimal_point_settings'))
    @foreach ($products as $product)
        @if (!empty($product['product_id']))
            @php($product = $product->product)
        @endif
        <div class="items-card-container p-2">
            @if (!empty($product))
                @include('web-views.partials._filter-single-product', [
                    'product' => $product,
                    'decimal_point_settings' => $decimal_point_settings,
                ])
            @endif
        </div>
    @endforeach

    <div class="col-12">
        <nav class="d-flex justify-content-between pt-2" aria-label="Page navigation" id="paginator-ajax">
            {!! $products->links() !!}
        </nav>
        {{-- <div id="pagination-container">
            {!! $products->withQueryString()->links() !!}
        </div> --}}
    </div>
@else
    <div class="d-flex justify-content-center align-items-center w-100 py-5">
        <div>
            <img src="{{ theme_asset(path: 'public/assets/front-end/img/media/product.svg') }}" class="img-fluid"
                alt="">
            <h6 class="text-muted">{{ translate('no_product_found') }}</h6>
        </div>
    </div>
@endif
<script>
    $(document).ready(function () {
        // Initial load with current URL parameters
        loadProducts(window.location.href);

        let filterTimeout;
        $('.filter-option').change(function () {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(function () {
                updateURLWithFilters();
            }, 300);
        });

        // Pagination click
        $(document).on('click', '#pagination-container a', function (e) {
            e.preventDefault();
            loadProducts($(this).attr('href'));
            history.pushState(null, '', $(this).attr('href')); // Update URL on pagination
        });

        function updateURLWithFilters() {
            let params = new URLSearchParams(window.location.search);

            $('.filter-option').each(function () {
                let name = $(this).attr('name');
                let value = $(this).val();
                if (value) {
                    params.set(name, value);
                } else {
                    params.delete(name);
                }
            });

            let newUrl = window.location.pathname + '?' + params.toString();
            history.pushState(null, '', newUrl); // Updates the URL
            loadProducts(newUrl); // Load filtered products
        }

        function loadProducts(url = window.location.href) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    $('#product-grid').html(data.view); // Make sure your container has this ID
                    $('#total-product-count').text(data.total_product);
                },
                error: function (xhr) {
                    console.log('Error loading products:', xhr);
                }
            });
        }
    });
</script>

