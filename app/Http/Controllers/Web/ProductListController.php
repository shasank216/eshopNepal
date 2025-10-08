<?php

namespace App\Http\Controllers\Web;

use App\Utils\Helpers;
use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Review;
use App\Models\Shop;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Models\Translation;
use App\Models\Wishlist;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Color;
use App\Enums\ViewPaths\Admin\Currency;
use App\Repositories\WishlistRepository;
use App\Models\Order;
use App\Models\Seller;
use App\Models\DealOfTheDay;
use App\Models\Banner;
use App\Models\MostDemanded;

class ProductListController extends Controller
{
    public function __construct(
        private Product      $product,
        private Order        $order,
        private OrderDetail  $order_details,
        private Category     $category,
        private Seller       $seller,
        private Review       $review,
        private DealOfTheDay $deal_of_the_day,
        private Banner       $banner,
        private MostDemanded $most_demanded,
        private readonly WishlistRepository  $wishlistRepo,
    ) {}



    public function products(Request $request)
    {
        $theme_name = theme_root_path();

        return match ($theme_name) {
            'default' => self::default_theme($request),
            'theme_aster' => self::theme_aster($request),
            'theme_fashion' => self::theme_fashion($request),
            'theme_all_purpose' => self::theme_all_purpose($request),
        };
    }

    public function default_theme(Request $request)
    {

        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

        $porduct_data = Product::active()->with(['reviews']);

        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if (!$request->has('data_from') || empty($request['data_from']) || $request['data_from'] == 'latest') {
            $query = $porduct_data;
        }

        if ($request['data_from'] == 'top-rated') {
            $reviews = Review::select('product_id', DB::raw('AVG(rating) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')->get();
            $product_ids = [];
            foreach ($reviews as $review) {
                array_push($product_ids, $review['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'best-selling') {
            $details = OrderDetail::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'most-favorite') {
            $details = Wishlist::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'featured') {
            $query = Product::with(['reviews'])->active()->where('featured', 1);
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status' => 1])->where(['deal_type' => 'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id', $featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with(['reviews'])->withCount('reviews')->active()->whereIn('id', $featured_deal_product_ids);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhereHas('tags', function ($query) use ($value) {
                            $query->where('tag', 'like', "%{$value}%");
                        });
                }
            })->pluck('id');

            // ✅ Add this log
            \Log::info('Product IDs from search', ['ids' => $product_ids]);

            if ($product_ids->count() == 0) {
                $product_ids = Translation::where('translationable_type', 'App\Models\Product')
                    ->where('key', 'name')
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('value', 'like', "%{$value}%");
                        }
                    })
                    ->pluck('translationable_id');
            }

            if ($product_ids->count()) {
                $query = $porduct_data->whereIn('id', $product_ids);
            } else {
                // This fallback is missing in your code
                $query = $porduct_data->whereRaw('0 = 1'); // Show no products
            }
        }


        if ($request['data_from'] == 'discounted') {
            $query = Product::with(['reviews'])->withCount('reviews')->active()->where('discount', '!=', 0);
        }

        if ($request['sort_by'] == 'latest') {
            $fetched = $query->latest();
        } elseif ($request['sort_by'] == 'low-high') {

            $fetched = $query->orderBy('unit_price', 'ASC');
        } elseif ($request['sort_by'] == 'high-low') {

            $fetched = $query->orderBy('unit_price', 'DESC');
        } elseif ($request['sort_by'] == 'a-z') {

            $fetched = $query->orderBy('name', 'ASC');
        } elseif ($request['sort_by'] == 'z-a') {

            $fetched = $query->orderBy('name', 'DESC');
        } else {
            $fetched = $query->latest();
        }

        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }

        $data = [
            'id' => $request['id'],
            'name' => $request['name'],
            'data_from' => $request['data_from'],
            'sort_by' => $request['sort_by'],
            'page_no' => $request['page'],
            'min_price' => $request['min_price'],
            'max_price' => $request['max_price'],
        ];

        $products = $fetched->paginate(20)->appends($data);

        if ($request->ajax() && $request->wantsJson()) {

            $sellerVacationStartDate = ($products ['added_by'] == 'seller' && isset($products->seller->shop->vacation_start_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_start_date)) : null;
            $sellerVacationEndDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_end_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_end_date)) : null;
            $sellerTemporaryClose = ($products['added_by'] == 'seller' && isset($products->seller->shop->temporary_close)) ? $products->seller->shop->temporary_close : false;

            $temporaryClose = getWebConfig('temporary_close');
            $inHouseVacation = getWebConfig('vacation_add');
            $inHouseVacationStartDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_start_date'] : null;
            $inHouseVacationEndDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_end_date'] : null;
            $inHouseVacationStatus = $products['added_by'] == 'admin' ? $inHouseVacation['status'] : false;
            $inHouseTemporaryClose = $products['added_by'] == 'admin' ? $temporaryClose['status'] : false;

            return response()->json([
                'total_product' => $products->total(),
                'view' => view('web-views.products._ajax-products', compact(
                    'products',
                    'sellerVacationStartDate',
                    'sellerTemporaryClose',
                    'sellerTemporaryClose',
                    'temporaryClose',
                    'inHouseVacation',
                    'inHouseVacationStartDate',
                    'inHouseVacationEndDate',
                    'inHouseVacationStatus',
                    'inHouseTemporaryClose'
                ))->render()
            ], 200);
        }

        if ($request['data_from'] == 'category') {
            $data['brand_name'] = Category::find((int)$request['id'])->name;
        }
        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->find((int)$request['id']);

            if ($brand_data) {
                $data['brand_name'] = $brand_data->name;
            } else {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }


        //    $colors= Color::all();
        $colorData = Product::distinct()->pluck('colors');
        // Initialize an array to store the parsed colors
        $colors = [];

        // Parse each JSON string
        foreach ($colorData as $jsonString) {
            $parsedColors = json_decode($jsonString, true);

            // Merge parsed colors into the $colors array if not empty
            if (!empty($parsedColors)) {
                $colors = array_merge($colors, $parsedColors);
            }
        }


        $business_settings = DB::table('business_settings')->where('id', 1)->first();
        $business_settings_value = $business_settings->value;

        $defaultCurrencies = DB::table('currencies')->where('id', $business_settings_value)->first();
        $product = Product::paginate(10);
        $product = $this->product->active()->inRandomOrder()->first();
        // $wishlistStatus = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id'], 'customer_id' => auth('customer')->id()]);
        // $countWishlist = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id']]);
         $wishlistStatus = null;

        if (!empty($product) && isset($product->id)) {
            $wishlistStatus = $this->wishlistRepo->getListWhereCount(
                filters: [
                    'product_id' => $product->id,
                    'customer_id' => auth('customer')->id()
                ]
            );
        }
        //  $countWishlist = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id']]);
        $countWishlist = $product?->id
            ? $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product->id])
            : 0;

        // $sellerVacationStartDate = ($product['added_by'] == 'seller' && isset($product->seller->shop->vacation_start_date)) ? date('Y-m-d', strtotime($product->seller->shop->vacation_start_date)) : null;
        // $sellerVacationEndDate = ($product['added_by'] == 'seller' && isset($product->seller->shop->vacation_end_date)) ? date('Y-m-d', strtotime($product->seller->shop->vacation_end_date)) : null;
        // $sellerTemporaryClose = ($product['added_by'] == 'seller' && isset($product->seller->shop->temporary_close)) ? $product->seller->shop->temporary_close : false;

        $sellerVacationStartDate = (
            isset($product->added_by) 
            && $product->added_by === 'seller' 
            && optional(optional($product->seller)->shop)->vacation_start_date
        ) ? date('Y-m-d', strtotime($product->seller->shop->vacation_start_date)) : null;

        $sellerVacationEndDate = (
            isset($product->added_by) 
            && $product->added_by === 'seller' 
            && optional(optional($product->seller)->shop)->vacation_end_date
        ) ? date('Y-m-d', strtotime($product->seller->shop->vacation_end_date)) : null;

        $sellerTemporaryClose = (
            isset($product->added_by) 
            && $product->added_by === 'seller' 
            && optional(optional($product->seller)->shop)->temporary_close
        ) ? $product->seller->shop->temporary_close : false;

        $temporaryClose = getWebConfig('temporary_close');
        $inHouseVacation = getWebConfig('vacation_add');
        // $inHouseVacationStartDate = $product['added_by'] == 'admin' ? $inHouseVacation['vacation_start_date'] : null;
        // $inHouseVacationEndDate = $product['added_by'] == 'admin' ? $inHouseVacation['vacation_end_date'] : null;
        // $inHouseVacationStatus = $product['added_by'] == 'admin' ? $inHouseVacation['status'] : false;
        // $inHouseTemporaryClose = $product['added_by'] == 'admin' ? $temporaryClose['status'] : false;
        $inHouseVacationStartDate = (
            isset($product) && isset($product->added_by) && $product->added_by === 'admin'
        ) ? ($inHouseVacation['vacation_start_date'] ?? null) : null;
                $inHouseVacationEndDate = (
            isset($product) && isset($product->added_by) && $product->added_by === 'admin'
        ) ? ($inHouseVacation['vacation_end_date'] ?? null) : null;

        $inHouseVacationStatus = (
            isset($product) && isset($product->added_by) && $product->added_by === 'admin'
        ) ? ($inHouseVacation['status'] ?? false) : false;

        $inHouseTemporaryClose = (
            isset($product) && isset($product->added_by) && $product->added_by === 'admin'
        ) ? ($temporaryClose['status'] ?? false) : false;

        // web-views.products.view

        return view(VIEW_FILE_NAMES['products_view_page'], compact(
            'products',
            'data',
            'colors',
            'defaultCurrencies',
            'wishlistStatus',
            'countWishlist',
            'sellerVacationStartDate',
            'sellerTemporaryClose',
            'sellerTemporaryClose',
            'temporaryClose',
            'inHouseVacation',
            'inHouseVacationStartDate',
            'inHouseVacationEndDate',
            'inHouseVacationStatus',
            'inHouseTemporaryClose'
        ));
    }



    //biwek
    // Controller method
    // public function filterProducts(Request $request)
    // {
    //     $categories = $request->input('categories');

    //     // Retrieve filtered products with pagination
    //     // $products = Product::whereIn('category_id', $categories)
    //     //     ->paginate(10); // Adjust pagination per page if needed
    //     $products = Product::whereIn('category_id', $categories)->get();

    //     $decimal_point_settings = getWebConfig(name: 'decimal_point_settings'); // Retrieve decimal point settings

    //     // dd( $products);
    //     // $wishlistStatus = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id'], 'customer_id' => auth('customer')->id()]);
    //     //  $countWishlist = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id']]);

    //     $sellerVacationStartDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_start_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_start_date)) : null;
    //     $sellerVacationEndDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_end_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_end_date)) : null;
    //     $sellerTemporaryClose = ($products['added_by'] == 'seller' && isset($products->seller->shop->temporary_close)) ? $products->seller->shop->temporary_close : false;

    //     $temporaryClose = getWebConfig('temporary_close');
    //     $inHouseVacation = getWebConfig('vacation_add');
    //     $inHouseVacationStartDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_start_date'] : null;
    //     $inHouseVacationEndDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_end_date'] : null;
    //     $inHouseVacationStatus = $products['added_by'] == 'admin' ? $inHouseVacation['status'] : false;
    //     $inHouseTemporaryClose = $products['added_by'] == 'admin' ? $temporaryClose['status'] : false;

    //     // Return the rendered view for AJAX
    //     return response()->json([
    //         'data' => view('web-views.products._ajax-products', compact(
    //             'products',
    //             'decimal_point_settings',
    //             'sellerVacationStartDate',
    //             'sellerTemporaryClose',
    //             'sellerTemporaryClose',
    //             'temporaryClose',
    //             'inHouseVacation',
    //             'inHouseVacationStartDate',
    //             'inHouseVacationEndDate',
    //             'inHouseVacationStatus',
    //             'inHouseTemporaryClose'
    //         ))->render()
    //     ]);
    // }

    // public function filterProducts(Request $request)
    // {
    //     $categories = $request->input('categories');
    //     // $products = Product::whereIn('category_id', $categories)->paginate(10);
    //     // $categories = $request->input('categories', []);
    //     $products = Product::when(!empty($categories), function ($query) use ($categories) {
    //         $query->whereIn('category_id', $categories);
    //     })
    //         ->paginate(10)
    //         ->appends($request->except('page'));
    //     $decimal_point_settings = getWebConfig(name: 'decimal_point_settings');

    //     // Initialize variables that will be used in the view
    //     $sellerVacationStartDate = null;
    //     $sellerVacationEndDate = null;
    //     $sellerTemporaryClose = false; // Initialize the variable

    //     $temporaryClose = getWebConfig('temporary_close');
    //     $inHouseVacation = getWebConfig('vacation_add');
    //     $inHouseVacationStartDate = null;
    //     $inHouseVacationEndDate = null;
    //     $inHouseVacationStatus = false;
    //     $inHouseTemporaryClose = false;

    //     // If you're only showing one product, you can use first()
    //     if ($products->count() > 0) {
    //         $product = $products->first();

    //         if ($product->added_by == 'seller' && $product->seller && $product->seller->shop) {
    //             $sellerVacationStartDate = $product->seller->shop->vacation_start_date
    //                 ? date('Y-m-d', strtotime($product->seller->shop->vacation_start_date))
    //                 : null;
    //             $sellerVacationEndDate = $product->seller->shop->vacation_end_date
    //                 ? date('Y-m-d', strtotime($product->seller->shop->vacation_end_date))
    //                 : null;
    //             $sellerTemporaryClose = $product->seller->shop->temporary_close ?? false;
    //         }

    //         if ($product->added_by == 'admin') {
    //             $inHouseVacationStartDate = $inHouseVacation['vacation_start_date'] ?? null;
    //             $inHouseVacationEndDate = $inHouseVacation['vacation_end_date'] ?? null;
    //             $inHouseVacationStatus = $inHouseVacation['status'] ?? false;
    //             $inHouseTemporaryClose = $temporaryClose['status'] ?? false;
    //         }
    //     }
        

    //     return response()->json([
    //         'data' => view('web-views.products._ajax-products', compact(
    //             'products',
    //             'decimal_point_settings',
    //             'sellerVacationStartDate',
    //             'sellerVacationEndDate',
    //             'sellerTemporaryClose', // Now properly defined
    //             'temporaryClose',
    //             'inHouseVacation',
    //             'inHouseVacationStartDate',
    //             'inHouseVacationEndDate',
    //             'inHouseVacationStatus',
    //             'inHouseTemporaryClose'
    //         ))->render()
    //     ]);
    // }
    public function filterBrand(Request $request)
    {
        // Get the brand IDs from the request
        $brandIds = $request->input('brands'); // Make sure 'brands' matches the key in your AJAX request

        // Check if any brand IDs were provided
        if (!empty($brandIds)) {
            // Retrieve filtered products based on the selected brand IDs with pagination
            $products = Product::whereIn('brand_id', $brandIds)
                ->paginate(10); // Adjust pagination per page if needed
        } else {
            // If no brand IDs were provided, return an empty collection or handle as needed
            $products = collect(); // Empty collection
        }

        $decimal_point_settings = getWebConfig(name: 'decimal_point_settings'); // Retrieve decimal point settings

        // dd( $products);
        // $wishlistStatus = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id'], 'customer_id' => auth('customer')->id()]);
        //  $countWishlist = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id']]);

        $sellerVacationStartDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_start_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_start_date)) : null;
        $sellerVacationEndDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_end_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_end_date)) : null;
        $sellerTemporaryClose = ($products['added_by'] == 'seller' && isset($products->seller->shop->temporary_close)) ? $products->seller->shop->temporary_close : false;

        $temporaryClose = getWebConfig('temporary_close');
        $inHouseVacation = getWebConfig('vacation_add');
        $inHouseVacationStartDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_start_date'] : null;
        $inHouseVacationEndDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_end_date'] : null;
        $inHouseVacationStatus = $products['added_by'] == 'admin' ? $inHouseVacation['status'] : false;
        $inHouseTemporaryClose = $products['added_by'] == 'admin' ? $temporaryClose['status'] : false;

        // Return the rendered view for AJAX
        return response()->json([
            'data' => view('web-views.products._ajax-products', compact(
                'products',
                'decimal_point_settings',
                'sellerVacationStartDate',
                'sellerTemporaryClose',
                'sellerTemporaryClose',
                'temporaryClose',
                'inHouseVacation',
                'inHouseVacationStartDate',
                'inHouseVacationEndDate',
                'inHouseVacationStatus',
                'inHouseTemporaryClose'
            ))->render()
        ]);
    }
    public function filterPrice(Request $request)
    {
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        // Validate inputs
        $validated = $request->validate([
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|min:0',
        ]);

        // Filter products based on the price range
        $products = Product::whereBetween('unit_price', [$minPrice, $maxPrice])->paginate(10); // Using pagination for better performance

        $decimal_point_settings = getWebConfig(name: 'decimal_point_settings'); // Retrieve decimal point settings

        // dd( $products);
        // $wishlistStatus = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id'], 'customer_id' => auth('customer')->id()]);
        //  $countWishlist = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id']]);

        $sellerVacationStartDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_start_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_start_date)) : null;
        $sellerVacationEndDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_end_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_end_date)) : null;
        $sellerTemporaryClose = ($products['added_by'] == 'seller' && isset($products->seller->shop->temporary_close)) ? $products->seller->shop->temporary_close : false;

        $temporaryClose = getWebConfig('temporary_close');
        $inHouseVacation = getWebConfig('vacation_add');
        $inHouseVacationStartDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_start_date'] : null;
        $inHouseVacationEndDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_end_date'] : null;
        $inHouseVacationStatus = $products['added_by'] == 'admin' ? $inHouseVacation['status'] : false;
        $inHouseTemporaryClose = $products['added_by'] == 'admin' ? $temporaryClose['status'] : false;

        // Return the rendered view for AJAX
        return response()->json([
            'data' => view('web-views.products._ajax-products', compact(
                'products',
                'decimal_point_settings',
                'sellerVacationStartDate',
                'sellerTemporaryClose',
                'sellerTemporaryClose',
                'temporaryClose',
                'inHouseVacation',
                'inHouseVacationStartDate',
                'inHouseVacationEndDate',
                'inHouseVacationStatus',
                'inHouseTemporaryClose'
            ))->render()
        ]);
    }

    // filter rating
    public function filterRatings(Request $request)
    {
        $ratings = $request->input('ratings');

        // Get all product IDs that have reviews with the selected ratings
        $ratingProductIds = Review::whereIn('rating', $ratings)->pluck('product_id');

        // Get all products that match the product IDs
        $products = Product::whereIn('id', $ratingProductIds)->paginate(10);


        $decimal_point_settings = getWebConfig(name: 'decimal_point_settings'); // Retrieve decimal point settings

        // dd( $products);
        // $wishlistStatus = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id'], 'customer_id' => auth('customer')->id()]);
        //  $countWishlist = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id']]);

        $sellerVacationStartDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_start_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_start_date)) : null;
        $sellerVacationEndDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_end_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_end_date)) : null;
        $sellerTemporaryClose = ($products['added_by'] == 'seller' && isset($products->seller->shop->temporary_close)) ? $products->seller->shop->temporary_close : false;

        $temporaryClose = getWebConfig('temporary_close');
        $inHouseVacation = getWebConfig('vacation_add');
        $inHouseVacationStartDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_start_date'] : null;
        $inHouseVacationEndDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_end_date'] : null;
        $inHouseVacationStatus = $products['added_by'] == 'admin' ? $inHouseVacation['status'] : false;
        $inHouseTemporaryClose = $products['added_by'] == 'admin' ? $temporaryClose['status'] : false;

        // Return the rendered view for AJAX
        return response()->json([
            'data' => view('web-views.products._ajax-products', compact(
                'products',
                'decimal_point_settings',
                'sellerVacationStartDate',
                'sellerTemporaryClose',
                'sellerTemporaryClose',
                'temporaryClose',
                'inHouseVacation',
                'inHouseVacationStartDate',
                'inHouseVacationEndDate',
                'inHouseVacationStatus',
                'inHouseTemporaryClose'
            ))->render()
        ]);
    }
    // filter color
    public function filtercolorProduct(Request $request)
    {
        // Get the colors from the request, defaulting to an empty array if none are provided
        $colors = $request->input('colors', []);

        // Ensure colors array is not empty
        if (!empty($colors)) {
            // Filter products where the JSON column 'color' contains any of the colors in the $colors array
            $products = Product::where(function ($query) use ($colors) {
                foreach ($colors as $color) {
                    $query->orWhereJsonContains('colors', $color);
                }
            })->paginate(10);
        } else {
            // If no colors are selected, fetch all products or handle accordingly
            $products = Product::paginate(10);
        }

        // Debugging: View the products retrieved
        // dd($products);

        $decimal_point_settings = getWebConfig(name: 'decimal_point_settings'); // Retrieve decimal point settings

        // dd( $products);
        // $wishlistStatus = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id'], 'customer_id' => auth('customer')->id()]);
        //  $countWishlist = $this->wishlistRepo->getListWhereCount(filters: ['product_id' => $product['id']]);

        $sellerVacationStartDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_start_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_start_date)) : null;
        $sellerVacationEndDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_end_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_end_date)) : null;
        $sellerTemporaryClose = ($products['added_by'] == 'seller' && isset($products->seller->shop->temporary_close)) ? $products->seller->shop->temporary_close : false;

        $temporaryClose = getWebConfig('temporary_close');
        $inHouseVacation = getWebConfig('vacation_add');
        $inHouseVacationStartDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_start_date'] : null;
        $inHouseVacationEndDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_end_date'] : null;
        $inHouseVacationStatus = $products['added_by'] == 'admin' ? $inHouseVacation['status'] : false;
        $inHouseTemporaryClose = $products['added_by'] == 'admin' ? $temporaryClose['status'] : false;

        // Return the rendered view for AJAX
        return response()->json([
            'data' => view('web-views.products._ajax-products', compact(
                'products',
                'decimal_point_settings',
                'sellerVacationStartDate',
                'sellerTemporaryClose',
                'sellerTemporaryClose',
                'temporaryClose',
                'inHouseVacation',
                'inHouseVacationStartDate',
                'inHouseVacationEndDate',
                'inHouseVacationStatus',
                'inHouseTemporaryClose'
            ))->render()
        ]);
    }





    public function theme_aster($request)
    {
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];
        $porduct_data = Product::active()->with([
            'reviews',
            'rating',
            'seller.shop',
            'wishList' => function ($query) {
                return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
            },
            'compareList' => function ($query) {
                return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
            }
        ])->withCount('reviews');

        $product_ids = [];
        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request->has('search_category_value') && $request['search_category_value'] != 'all') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['search_category_value']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if (!$request->has('data_from') || $request['data_from'] == 'latest') {
            $query = $porduct_data;
        }

        if ($request['data_from'] == 'top-rated') {
            $reviews = Review::select('product_id', DB::raw('AVG(rating) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')->get();
            $product_ids = [];
            foreach ($reviews as $review) {
                array_push($product_ids, $review['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'best-selling') {
            $details = OrderDetail::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'most-favorite') {
            $details = Wishlist::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }
        if ($request['data_from'] == 'featured') {
            $query = Product::with([
                'reviews',
                'seller.shop',
                'wishList' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->active()->withCount('reviews')->where('featured', 1);
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status' => 1])->where(['deal_type' => 'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id', $featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with([
                'reviews',
                'seller.shop',
                'wishList' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->active()->withCount('reviews')->whereIn('id', $featured_deal_product_ids);
        }
        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::with([
                'seller.shop',
                'wishList' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%")
                            ->orWhereHas('tags', function ($query) use ($value) {
                                $query->where('tag', 'like', "%{$value}%");
                            });
                    }
                })->pluck('id');

            if ($product_ids->count() == 0) {
                $product_ids = Translation::where('translationable_type', 'App\Models\Product')
                    ->where('key', 'name')
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('value', 'like', "%{$value}%");
                        }
                    })
                    ->pluck('translationable_id');
            }

            $query = $porduct_data->WhereIn('id', $product_ids);
        }
        if ($request['data_from'] == 'discounted') {
            $query = Product::with([
                'reviews',
                'seller.shop',
                'wishList' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->active()->withCount('reviews')->where('discount', '!=', 0);
        }
        if (!$request['data_from'] && !$request['name'] && $request['ratings']) {
            $query = $query ?? $porduct_data;
        }
        if ($request['sort_by'] == 'latest') {
            $fetched = $query->latest();
        } elseif ($request['sort_by'] == 'low-high') {
            $fetched = $query->orderBy('unit_price', 'ASC');
        } elseif ($request['sort_by'] == 'high-low') {
            $fetched = $query->orderBy('unit_price', 'DESC');
        } elseif ($request['sort_by'] == 'a-z') {
            $fetched = $query->orderBy('name', 'ASC');
        } elseif ($request['sort_by'] == 'z-a') {
            $fetched = $query->orderBy('name', 'DESC');
        } else {
            $fetched = $query->latest();
        }
        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }
        if ($request['ratings'] != null) {
            $fetched->with('rating')->whereHas('rating', function ($query) use ($request) {
                return $query;
            });
        }

        $data = [
            'id' => $request['id'],
            'name' => $request['name'],
            'data_from' => $request['data_from'],
            'sort_by' => $request['sort_by'],
            'page_no' => $request['page'],
            'min_price' => $request['min_price'],
            'max_price' => $request['max_price'],
        ];
        $common_query = $fetched;
        $rating_1 = 0;
        $rating_2 = 0;
        $rating_3 = 0;
        $rating_4 = 0;
        $rating_5 = 0;

        foreach ($common_query->get() as $rating) {
            if (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] > 0 && $rating->rating[0]['average'] < 2)) {
                $rating_1 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 2 && $rating->rating[0]['average'] < 3)) {
                $rating_2 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 3 && $rating->rating[0]['average'] < 4)) {
                $rating_3 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 4 && $rating->rating[0]['average'] < 5)) {
                $rating_4 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] == 5)) {
                $rating_5 += 1;
            }
        }
        $ratings = [
            'rating_1' => $rating_1,
            'rating_2' => $rating_2,
            'rating_3' => $rating_3,
            'rating_4' => $rating_4,
            'rating_5' => $rating_5,
        ];

        $products = $common_query->paginate(20)->appends($data);

        if ($request['ratings'] != null) {
            $products = $products->map(function ($product) use ($request) {
                $product->rating = $product->rating->pluck('average')[0];
                return $product;
            });
            $products = $products->where('rating', '>=', $request['ratings'])
                ->where('rating', '<', $request['ratings'] + 1)
                ->paginate(20)->appends($data);
        }

        if ($request->ajax()) {
            return response()->json([
                'total_product' => $products->total(),
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products', 'product_ids'))->render(),
            ], 200);
        }
        if ($request['data_from'] == 'category') {
            $data['brand_name'] = Category::find((int)$request['id'])->name;
        }
        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->find((int)$request['id']);
            if ($brand_data) {
                $data['brand_name'] = $brand_data->name;
            } else {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }

        return view(VIEW_FILE_NAMES['products_view_page'], compact('products', 'data', 'ratings', 'product_ids'));
    }

    public function theme_fashion(Request $request)
    {

        $tag_category = [];
        if ($request->data_from == 'category') {
            $tag_category = Category::where('id', $request->id)->select('id', 'name')->get();
        }

        $tag_brand = [];
        if ($request->data_from == 'brand') {
            $tag_brand = Brand::where('id', $request->id)->select('id', 'name')->get();
        }
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

        $porduct_data = Product::active()->withSum('orderDetails', 'qty', function ($query) {
            $query->where('delivery_status', 'delivered');
        })
            ->with([
                'category',
                'reviews',
                'rating',
                'wishList' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->withCount('reviews');

        $product_ids = [];
        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request->has('search_category_value') && $request['search_category_value'] != 'all') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['search_category_value']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if ($request['data_from'] == 'latest') {
            $query = $porduct_data;
        }
        if (!$request->has('data_from') || $request['data_from'] == 'default') {
            $query = $porduct_data->orderBy('order_details_sum_qty', 'DESC');
        }

        if ($request['data_from'] == 'top-rated') {
            $reviews = Review::select('product_id', DB::raw('AVG(rating) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')->get();
            $product_ids = [];
            foreach ($reviews as $review) {
                array_push($product_ids, $review['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'best-selling') {
            $details = OrderDetail::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'most-favorite') {
            $details = Wishlist::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'featured') {
            $query = Product::with(['reviews'])->active()->withCount('reviews')->where('featured', 1);
        }

        if ($request->has('shop_id') && $request['shop_id'] == 0) {
            $query = Product::active()
                ->with(['reviews'])
                ->withCount('reviews')
                ->where(['added_by' => 'admin', 'featured' => 1]);
        } elseif ($request->has('shop_id') && $request['shop_id'] != 0) {
            $query = Product::active()
                ->withCount('reviews')
                ->where(['added_by' => 'seller', 'featured' => 1])
                ->with(['reviews', 'seller.shop' => function ($query) use ($request) {
                    $query->where('id', $request->shop_id);
                }])
                ->whereHas('seller.shop', function ($query) use ($request) {
                    $query->where('id', $request->shop_id)->whereNotNull('id');
                });
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status' => 1])->where(['deal_type' => 'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id', $featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with(['reviews'])->active()->whereIn('id', $featured_deal_product_ids);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhereHas('tags', function ($query) use ($value) {
                            $query->where('tag', 'like', "%{$value}%");
                        });
                }
            })->pluck('id');

            $sellers = Shop::where(function ($q) use ($request) {
                $q->orWhere('name', 'like', "%{$request['name']}%");
            })->whereHas('seller', function ($query) {
                return $query->where(['status' => 'approved']);
            })->with('products', function ($query) {
                return $query->active()->where('added_by', 'seller');
            })->get();

            $seller_products = [];
            foreach ($sellers as $seller) {
                if (isset($seller->product) && $seller->product->count() > 0) {
                    $ids = $seller->product->pluck('id');
                    array_push($seller_products, ...$ids);
                }
            }

            $inhouse_product = [];
            $company_name = Helpers::get_business_settings('company_name');

            if (strpos($request['name'], $company_name) !== false) {
                $inhouse_product = Product::active()->withCount('reviews')->Where('added_by', 'admin')->pluck('id');
            }

            $product_ids = $product_ids->merge($seller_products)->merge($inhouse_product);


            if ($product_ids->count() == 0) {
                $product_ids = Translation::where('translationable_type', 'App\Models\Product')
                    ->where('key', 'name')
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('value', 'like', "%{$value}%");
                        }
                    })
                    ->pluck('translationable_id');
            }

            $query = $porduct_data->WhereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'discounted') {
            $query = Product::with(['reviews'])->active()->withCount('reviews')->where('discount', '!=', 0);
        }

        if ($request['sort_by'] == 'latest') {
            $fetched = $query->latest();
        } elseif ($request['sort_by'] == 'low-high') {
            $fetched = $query->orderBy('unit_price', 'ASC');
        } elseif ($request['sort_by'] == 'high-low') {
            $fetched = $query->orderBy('unit_price', 'DESC');
        } elseif ($request['sort_by'] == 'a-z') {
            $fetched = $query->orderBy('name', 'ASC');
        } elseif ($request['sort_by'] == 'z-a') {
            $fetched = $query->orderBy('name', 'DESC');
        } else {
            $fetched = $query->latest();
        }

        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }
        $common_query = $fetched;

        $products = $common_query->paginate(20);

        $sellerVacationStartDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_start_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_start_date)) : null;
        $sellerVacationEndDate = ($products['added_by'] == 'seller' && isset($products->seller->shop->vacation_end_date)) ? date('Y-m-d', strtotime($products->seller->shop->vacation_end_date)) : null;
        $sellerTemporaryClose = ($products['added_by'] == 'seller' && isset($products->seller->shop->temporary_close)) ? $products->seller->shop->temporary_close : false;

        $temporaryClose = getWebConfig('temporary_close');
        $inHouseVacation = getWebConfig('vacation_add');
        $inHouseVacationStartDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_start_date'] : null;
        $inHouseVacationEndDate = $products['added_by'] == 'admin' ? $inHouseVacation['vacation_end_date'] : null;
        $inHouseVacationStatus = $products['added_by'] == 'admin' ? $inHouseVacation['status'] : false;
        $inHouseTemporaryClose = $products['added_by'] == 'admin' ? $temporaryClose['status'] : false;

        if ($request['ratings'] != null) {
            $products = $products->map(function ($product) use ($request) {
                $product->rating = $product->rating->pluck('average')[0];
                return $product;
            });
            $products = $products->where('rating', '>=', $request['ratings'])
                ->where('rating', '<', $request['ratings'] + 1)
                ->paginate(20);
        }

        // Categories start
        $categories = Category::withCount(['product' => function ($query) {
            $query->active();
        }])->with(['childes' => function ($query) {
            $query->with(['childes' => function ($query) {
                $query->withCount(['subSubCategoryProduct'])->where('position', 2);
            }])->withCount(['subCategoryProduct'])->where('position', 1);
        }, 'childes.childes'])
            ->where('position', 0)->get();
        // Categories End

        // Colors Start
        $colors_in_shop_merge = [];
        $colors_collection = Product::active()
            ->withCount('reviews')
            ->where('colors', '!=', '[]')
            ->pluck('colors')
            ->unique()
            ->toArray();

        foreach ($colors_collection as $color_json) {
            $color_array = json_decode($color_json, true);
            if ($color_array) {
                $colors_in_shop_merge = array_merge($colors_in_shop_merge, $color_array);
            }
        }
        $colors_in_shop = array_unique($colors_in_shop_merge);
        // Colors End
        $banner = \App\Models\BusinessSetting::where('type', 'banner_product_list_page')->whereJsonContains('value', ['status' => '1'])->first();

        if ($request->ajax()) {
            return response()->json([
                'total_product' => $products->total(),
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products', 'product_ids'))->render(),
            ], 200);
        }

        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->find((int)$request['id']);
            if (!$brand_data) {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }

        return view(VIEW_FILE_NAMES['products_view_page'], compact(
            'products',
            'tag_category',
            'tag_brand',
            'product_ids',
            'categories',
            'colors_in_shop',
            'banner',
            'sellerVacationStartDate',
            'sellerTemporaryClose',
            'sellerTemporaryClose',
            'temporaryClose',
            'inHouseVacation',
            'inHouseVacationStartDate',
            'inHouseVacationEndDate',
            'inHouseVacationStatus',
            'inHouseTemporaryClose'
        ));
    }

    public function theme_all_purpose(Request $request)
    {
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

        $porduct_data = Product::active()->with(['reviews', 'rating'])->withCount('reviews');

        $product_ids = [];
        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request->has('search_category_value') && $request['search_category_value'] != 'all') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['search_category_value']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if (!$request->has('data_from') || $request['data_from'] == 'latest') {
            $query = $porduct_data;
        }

        if ($request['data_from'] == 'top-rated') {
            $reviews = Review::select('product_id', DB::raw('AVG(rating) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')->get();
            $product_ids = [];
            foreach ($reviews as $review) {
                array_push($product_ids, $review['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'best-selling') {
            $details = OrderDetail::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'most-favorite') {
            $details = Wishlist::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'featured') {
            $query = Product::with(['reviews'])->active()->withCount('reviews')->where('featured', 1);
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status' => 1])->where(['deal_type' => 'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id', $featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with(['reviews'])->active()->withCount('reviews')->whereIn('id', $featured_deal_product_ids);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhereHas('tags', function ($query) use ($value) {
                            $query->where('tag', 'like', "%{$value}%");
                        });
                }
            })->pluck('id');

            if ($product_ids->count() == 0) {
                $product_ids = Translation::where('translationable_type', 'App\Models\Product')
                    ->where('key', 'name')
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('value', 'like', "%{$value}%");
                        }
                    })
                    ->pluck('translationable_id');
            }

            $query = $porduct_data->WhereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'discounted') {
            $query = Product::with(['reviews'])->active()->withCount('reviews')->where('discount', '!=', 0);
        }

        if ($request['sort_by'] == 'latest') {
            $fetched = $query->latest();
        } elseif ($request['sort_by'] == 'low-high') {
            $fetched = $query->orderBy('unit_price', 'ASC');
        } elseif ($request['sort_by'] == 'high-low') {
            $fetched = $query->orderBy('unit_price', 'DESC');
        } elseif ($request['sort_by'] == 'a-z') {
            $fetched = $query->orderBy('name', 'ASC');
        } elseif ($request['sort_by'] == 'z-a') {
            $fetched = $query->orderBy('name', 'DESC');
        } else {
            $fetched = $query->latest();
        }

        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }
        $common_query = $fetched;

        $rating_1 = 0;
        $rating_2 = 0;
        $rating_3 = 0;
        $rating_4 = 0;
        $rating_5 = 0;

        foreach ($common_query->get() as $rating) {
            if (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] > 0 && $rating->rating[0]['average'] < 2)) {
                $rating_1 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 2 && $rating->rating[0]['average'] < 3)) {
                $rating_2 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 3 && $rating->rating[0]['average'] < 4)) {
                $rating_3 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 4 && $rating->rating[0]['average'] < 5)) {
                $rating_4 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] == 5)) {
                $rating_5 += 1;
            }
        }
        $ratings = [
            'rating_1' => $rating_1,
            'rating_2' => $rating_2,
            'rating_3' => $rating_3,
            'rating_4' => $rating_4,
            'rating_5' => $rating_5,
        ];
        $data = [
            'id' => $request['id'],
            'name' => $request['name'],
            'data_from' => $request['data_from'],
        ];
        $products_count = $common_query->count();
        $products = $common_query->paginate(4)->appends($data);
        $categories = Category::withCount(['product' => function ($query) {
            $query->where(['status' => '1']);
        }])->with(['childes' => function ($sub_query) {
            $sub_query->with(['childes' => function ($sub_sub_query) {
                $sub_sub_query->withCount(['sub_sub_category_product'])->where('position', 2);
            }])->withCount(['sub_category_product'])->where('position', 1);
        }, 'childes.childes'])
            ->where('position', 0)->get();
        // Categories End
        // Colors Start
        $colors_in_shop_merge = [];
        $colors_collection = Product::active()
            ->withCount('reviews')
            ->where('colors', '!=', '[]')
            ->pluck('colors')
            ->unique()
            ->toArray();

        foreach ($colors_collection as $color_json) {
            $color_array = json_decode($color_json, true);
            if ($color_array) {
                $colors_in_shop_merge = array_merge($colors_in_shop_merge, $color_array);
            }
        }
        $colors_in_shop = array_unique($colors_in_shop_merge);
        // Colors End
        $banner = \App\Models\BusinessSetting::where('type', 'banner_product_list_page')->whereJsonContains('value', ['status' => '1'])->first();

        if ($request->ajax()) {
            return response()->json([
                'total_product' => $products->total(),
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products', 'product_ids'))->render(),
            ], 200);
        }

        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->find((int)$request['id']);
            if (!$brand_data) {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }
        return view(VIEW_FILE_NAMES['products_view_page'], compact('products', 'product_ids', 'products_count', 'categories', 'colors_in_shop', 'banner', 'ratings'));
    }




    // public function compare_product_list(){
    //     // Categories start
    //     $categories = Category::withCount(['product'=>function($query){
    //             $query->active();
    //         }])->with(['childes' => function ($query) {
    //             $query->with(['childes' => function ($query) {
    //                 $query->withCount(['subSubCategoryProduct'])->where('position', 2);
    //             }])->withCount(['subCategoryProduct'])->where('position', 1);
    //         }, 'childes.childes'])
    //         ->where('position', 0)->get();
    //     // Categories End

    //     return view(VIEW_FILE_NAMES['product_compare'], compact('categories'));
    // }


    public function compare_product_list(Request $request)
    {
        // Get the product IDs from the query string
        $productIds = $request->input('ids');

        // Convert the product IDs into an array
        $productIdsArray = $productIds ? explode(',', $productIds) : [];

        // Categories logic
        $categories = Category::withCount(['product' => function ($query) {
            $query->active();
        }])
            ->with(['childes' => function ($query) {
                $query->with(['childes' => function ($query) {
                    $query->withCount(['subSubCategoryProduct'])->where('position', 2);
                }])->withCount(['subCategoryProduct'])->where('position', 1);
            }, 'childes.childes'])
            ->where('position', 0)->get();

        // Filter products by selected product IDs and their categories
        $products = !empty($productIdsArray)
            ? Product::whereIn('id', $productIdsArray)
            ->whereIn('category_id', $categories->pluck('id')->toArray())
            ->get()
            : collect(); // Empty collection if no products are selected

        // Return the view with the categories and the filtered products
        // dd($products);
        $product = Product::paginate(10);

        $sellerVacationStartDate = ($product['added_by'] == 'seller' && isset($product->seller->shop->vacation_start_date)) ? date('Y-m-d', strtotime($product->seller->shop->vacation_start_date)) : null;
        $sellerVacationEndDate = ($product['added_by'] == 'seller' && isset($product->seller->shop->vacation_end_date)) ? date('Y-m-d', strtotime($product->seller->shop->vacation_end_date)) : null;
        $sellerTemporaryClose = ($product['added_by'] == 'seller' && isset($product->seller->shop->temporary_close)) ? $product->seller->shop->temporary_close : false;

        $temporaryClose = getWebConfig('temporary_close');
        $inHouseVacation = getWebConfig('vacation_add');
        $inHouseVacationStartDate = $product['added_by'] == 'admin' ? $inHouseVacation['vacation_start_date'] : null;
        $inHouseVacationEndDate = $product['added_by'] == 'admin' ? $inHouseVacation['vacation_end_date'] : null;
        $inHouseVacationStatus = $product['added_by'] == 'admin' ? $inHouseVacation['status'] : false;
        $inHouseTemporaryClose = $product['added_by'] == 'admin' ? $temporaryClose['status'] : false;


        return view(VIEW_FILE_NAMES['product_compare'], compact(
            'categories',
            'products',
            'sellerVacationStartDate',
            'sellerTemporaryClose',
            'sellerVacationEndDate',
            'temporaryClose',
            'inHouseVacation',
            'inHouseVacationStartDate',
            'inHouseVacationEndDate',
            'inHouseVacationStatus',
            'inHouseTemporaryClose'
        ));
    }
}
