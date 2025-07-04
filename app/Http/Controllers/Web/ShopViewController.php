<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Models\Seller;
use App\Models\Shop;
use App\Models\ShopFollower;
use App\Models\Wishlist;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopViewController extends Controller
{
      public function __construct(
        private Product      $product,
        
        private Category     $category,
       
    )
    {
    }

    //for seller Shop
    public function seller_shop(Request $request, $id)
    {
        $theme_name = theme_root_path();

        return match ($theme_name){
            'default' => self::default_theme($request, $id),
            'theme_aster' => self::theme_aster($request, $id),
            'theme_fashion' => self::theme_fashion($request, $id),
            'theme_all_purpose' => self::theme_all_purpose($request, $id),
        };
    }

    public function default_theme($request, $id) {
        
        $business_mode = getWebConfig(name: 'business_mode');

        if($id!=0 && $business_mode == 'single') {
            Toastr::error(translate('access_denied!!'));
            return back();
        }

        if ($id != 0) {
            $shop = Shop::where('id', $id)->first();
            if (!$shop) {
                Toastr::error(translate('shop_does_not_exist'));
                return back();
            }else {
                $active_seller = Seller::approved()->find($shop['seller_id']);
                if(!$active_seller) {
                    Toastr::warning(translate('not_found'));
                    return redirect('/');
                }
            }
        }

        $id = $id != 0 ? Shop::where('id', $id)->first()->seller_id : $id;

        $product_ids = Product::active()->when($id == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($id != 0, function ($query) use ($id) {
                return $query->where(['added_by' => 'seller','user_id'=> $id]);
            })
            ->pluck('id')->toArray();
            $avg_rating = Review::active()->where('status',1)->whereIn('product_id', $product_ids)->avg('rating');
            $total_review = Review::active()->where('status',1)->whereIn('product_id', $product_ids)->count();
            if($id == 0){
                $total_order = Order::where('seller_is','admin')->where('order_type','default_type')->count();
            }else{
                $seller = Seller::find($id);
                $total_order = $seller->orders->where('seller_is','seller')->where('order_type','default_type')->count();
            }


        $products = Product::withCount('reviews')->whereIn('id', $product_ids)->paginate(12);
        //finding category ids

        $category_info = [];
        foreach ($products as $product) {
            array_push($category_info, $product['category_ids']);
        }

        $category_info_decoded = [];
        foreach ($category_info as $info) {
            array_push($category_info_decoded, json_decode($info));
        }

        $category_ids = [];
        foreach ($category_info_decoded as $decoded) {
            foreach ($decoded as $info) {
                array_push($category_ids, $info->id);
            }
        }

        $categories = [];
        foreach ($category_ids as $category_id) {
            $category = Category::with(['childes.childes'])->where('position', 0)->find($category_id);
            if ($category != null) {
                array_push($categories, $category);
            }
        }
        $categories_product = array_unique($categories);
        
         $categories = $this->category->with('childes.childes')->where(['position' => 0])->priority()->take(14)->get();
        // dd($categoriess);

        $products = Product::active()
            ->withCount('reviews')
            ->when($id == 0, function ($query) {

                return $query->where(['added_by' => 'admin']);
            })
            ->when($id != 0, function ($query) use ($id) {
                return $query->where(['added_by' => 'seller','user_id'=> $id]);
            })
            ->when(!empty($request->product_name), function ($query) use($request){
                $key = explode(' ', $request->product_name);
                $query->where(function ($subquery) use ($key) {
                    foreach ($key as $value) {
                        $subquery->where('name', 'like', "%{$value}%")
                                 ->orWhereHas('tags', function ($tagQuery) use ($value) {
                                     $tagQuery->where('tag', 'like', "%{$value}%");
                                 });
                    }
                });
            })
            ->when($request->has('sort_by'), function($query) use($request){
                $query->when($request['sort_by'] == 'latest', function($query){
                    return $query->latest();
                })
                    ->when($request['sort_by'] == 'low-high', function($query){
                        return $query->orderBy('unit_price', 'ASC');
                    })
                    ->when($request['sort_by'] == 'high-low', function($query){
                        return $query->orderBy('unit_price', 'DESC');
                    })
                    ->when($request['sort_by'] == 'a-z', function($query){
                        return $query->orderBy('name', 'ASC');
                    })
                    ->when($request['sort_by'] == 'z-a', function($query){
                        return $query->orderBy('name', 'DESC');
                    })
                    ->when($request['sort_by'] == '', function($query){
                        return $query->latest();
                    });
            })
            ->when(!empty($request->category_id), function($query) use($request){
                $query->where('category_id',$request->category_id);
            })
            ->when(!empty($request->sub_category_id), function($query) use($request){
                $query->where('sub_category_id',$request->sub_category_id);
            })
            ->when(!empty($request->sub_sub_category_id), function($query) use($request){
                $query->where('sub_sub_category_id',$request->sub_sub_category_id);
            })
            ->latest()
            ->paginate(12)->appends([
                'category_id'=>$request->category_id,
                'sub_category_id'=>$request->sub_category_id,
                'sub_sub_category_id'=>$request->sub_sub_category_id,
                'product_name'=>$request->product_name
            ]);

        if ($id == 0) {
            $shop = ['id' => 0, 'name' => Helpers::get_business_settings('company_name')];
        } else {
            $shop = Shop::where('seller_id', $id)->first();
        }

        $currentDate = date('Y-m-d');
        $sellerVacationStartDate = $id != 0 ? date('Y-m-d', strtotime($shop->vacation_start_date)) : null;
        $sellerVacationEndDate = $id != 0 ? date('Y-m-d', strtotime($shop->vacation_end_date)) : null;
        $sellerTemporaryClose = $id != 0 ? $shop->temporary_close : false;
        $sellerVacationStatus = $id != 0 ? $shop->vacation_status : false;

        $temporaryClose = Helpers::get_business_settings('temporary_close');
        $inhouseVacation = Helpers::get_business_settings('vacation_add');
        $inHouseVacationStartDate = $id == 0 ? $inhouseVacation['vacation_start_date'] : null;
        $inHouseVacationEndDate = $id == 0 ? $inhouseVacation['vacation_end_date'] : null;
        $inHouseVacationStatus = $id == 0 ? $inhouseVacation['status'] : false;
        $inHouseTemporaryClose = $id == 0 ? $inhouseVacation['status'] : false;
        
        if ($request->ajax()) {
           
            return response()->json([
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products','categories'))->render(),
            ], 200);
        }
       
        
        return view(VIEW_FILE_NAMES['shop_view_page'], compact('products', 'shop', 'categories','categories_product','currentDate','sellerVacationStartDate','sellerVacationStatus',
            'sellerVacationEndDate','sellerTemporaryClose','inHouseVacationStartDate','inHouseVacationEndDate','inHouseVacationStatus','inHouseTemporaryClose'))
            ->with('seller_id', $id)
            ->with('total_review', $total_review)
            ->with('avg_rating', $avg_rating)
            ->with('total_order', $total_order);
    }

    public function theme_aster($request, $id){
        $business_mode = getWebConfig(name: 'business_mode');

        if($id!=0 && $business_mode == 'single') {
            Toastr::error(translate('access_denied!!'));
            return back();
        }

        if ($id != 0) {
            $shop = Shop::where('id', $id)->first();
            if (!$shop) {
                Toastr::error(translate('shop_does_not_exist'));
                return back();
            }else {
                $active_seller = Seller::approved()->find($shop['seller_id']);
                if(!$active_seller) {
                    Toastr::warning(translate('not_found'));
                    return redirect('/');
                }
            }
        }

        $id = $id != 0 ? Shop::where('id', $id)->first()->seller_id : $id;


        $product_rating = Product::active()->with('rating')
            ->withCount('reviews')
            ->when($id == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($id != 0, function ($query) use ($id) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $id);
            })->get();
        $rating_1 = 0;
        $rating_2 = 0;
        $rating_3 = 0;
        $rating_4 = 0;
        $rating_5 = 0;

        foreach($product_rating as $rating){
            if(isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] > 0 && $rating->rating[0]['average'] <2)){
                $rating_1 += 1;
            }elseif(isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >=2 && $rating->rating[0]['average'] <3)){
                $rating_2 += 1;
            }elseif(isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >=3 && $rating->rating[0]['average'] <4)){
                $rating_3 += 1;
            }elseif(isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >=4 && $rating->rating[0]['average'] <5)){
                $rating_4 += 1;
            }elseif(isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] == 5)){
                $rating_5 += 1;
            }
        }
        $ratings = [
            'rating_1'=>$rating_1,
            'rating_2'=>$rating_2,
            'rating_3'=>$rating_3,
            'rating_4'=>$rating_4,
            'rating_5'=>$rating_5,
        ];

        $product_ids = Product::active()->when($id == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($id != 0, function ($query) use ($id) {
                return $query->where(['added_by' => 'seller'])->where('user_id', $id);
            })
            ->pluck('id')->toArray();
        $reviewData = Review::active()->whereIn('product_id', $product_ids);
        $averageRating = $reviewData->avg('rating');
        $totalReviews = $reviewData->count();

        // color & seller wise review start
        $rattingStatusPositive = 0;
        $rattingStatusGood = 0;
        $rattingStatusNeutral = 0;
        $rattingStatusNegative = 0;
        foreach($reviewData->pluck('rating') as $singleRating)
        {
            ($singleRating >= 4?($rattingStatusPositive++):'');
            ($singleRating == 3?($rattingStatusGood++):'');
            ($singleRating == 2?($rattingStatusNeutral++):'');
            ($singleRating == 1?($rattingStatusNegative++):'');
        }
        $rattingStatusArray = [
            'positive' => $totalReviews != 0 ? ($rattingStatusPositive*100)/ $totalReviews:0,
            'good' => $totalReviews != 0 ?($rattingStatusGood*100)/ $totalReviews:0,
            'neutral' => $totalReviews != 0 ?($rattingStatusNeutral*100)/ $totalReviews:0,
            'negative' => $totalReviews != 0 ?($rattingStatusNegative*100)/ $totalReviews:0,
        ];


        if($id == 0){
            $totalOrder = Order::where('seller_is','admin')->where('order_type','default_type')->count();
            $products_for_review = Product::active()->where('added_by', 'admin')->withCount('reviews')->count();
            $featured_products = Product::active()->with([
                'seller.shop',
                'wishList'=>function($query){
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList'=>function($query){
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])
            ->withCount('reviews')
            ->where(['added_by'=>'admin','featured'=>'1'])->get();
        }else{
            $seller = Seller::find($id);
            $totalOrder = $seller->orders->where('seller_is','seller')->where('order_type','default_type')->count();
            $products_for_review = Product::active()->where('added_by', 'seller')->where('user_id', $seller->id)->withCount('reviews')->count();
            $featured_products = Product::active()->with([
                'seller.shop',
                'wishList'=>function($query){
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList'=>function($query){
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])
            ->withCount('reviews')
            ->where(['added_by'=>'seller','user_id'=>$seller->id,'featured'=>'1'])->get();
        }
        // Followers
        $followers = ShopFollower::where('shop_id',$id)->count();
        $follow_status = 0;
        if(auth('customer')->check()){
            $follow_status = ShopFollower::where(['shop_id'=>$id,'user_id'=>auth('customer')->id()])->count();
        }

        //finding category ids
        $products = Product::active()
            ->with([
                'seller.shop',
                'wishList'=>function($query){
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList'=>function($query){
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])
            ->withCount('reviews')
            ->when($id == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($id != 0, function ($query) use ($id) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $id);
            })->get();

        $category_info = [];
        foreach ($products as $product) {
            array_push($category_info, $product['category_ids']);
        }

        $category_info_decoded = [];
        foreach ($category_info as $info) {
            array_push($category_info_decoded, json_decode($info));
        }

        $category_ids = [];
        foreach ($category_info_decoded as $decoded) {
            foreach ($decoded as $info) {
                array_push($category_ids, $info->id);
            }
        }

        $categories = [];
        foreach ($category_ids as $category_id) {
            $category = Category::with(['childes.childes'])->where('position', 0)->find($category_id);
            if ($category != null) {
                array_push($categories, $category);
            }
        }
        $categories = array_unique($categories);
        //end

        $brand_info = [];
        foreach ($products as $product) {
            array_push($brand_info, $product['brand_id']);
        }

        $brands = Brand::active()->whereIn('id', $brand_info)->withCount('brandProducts')->latest()->get();

        foreach($brands as $brand)
        {
            $count = $products->where('brand_id', $brand->id)->count();
            $brand->count = $count;
        }

        if ($id == 0) {
            $shop = ['id' => 0, 'name' => Helpers::get_business_settings('company_name')];
        } else {
            $shop = Shop::where('seller_id', $id)->first();
        }

        //products search
        $products = Product::active()
            ->with([
                'seller.shop',
                'wishList'=>function($query){
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList'=>function($query){
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])
            ->withCount('reviews')
            ->when($id == '0', function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($id != '0', function ($query) use ($id) {
                return $query->where(['added_by' => 'seller', 'user_id'=> $id]);
            })
            ->when(!empty($request->product_name), function ($query) use ($request) {
                $key = explode(' ', $request->product_name);
                $query->where(function ($subquery) use ($key) {
                    foreach ($key as $value) {
                        $subquery->where('name', 'like', "%{$value}%")
                                 ->orWhereHas('tags', function ($tagQuery) use ($value) {
                                     $tagQuery->where('tag', 'like', "%{$value}%");
                                 });
                    }
                });
            })
            ->when(!empty($request->category_id), function($query) use($request){
                $query->whereJsonContains('category_ids', [
                    ['id' => strval($request->category_id)],
                ]);
            })
            ->when($request->has('sort_by'), function($query) use($request){
                $query->when($request['sort_by'] == 'latest', function($query){
                    return $query->latest();
                })
                    ->when($request['sort_by'] == 'low-high', function($query){
                        return $query->orderBy('unit_price', 'ASC');
                    })
                    ->when($request['sort_by'] == 'high-low', function($query){
                        return $query->orderBy('unit_price', 'DESC');
                    })
                    ->when($request['sort_by'] == 'a-z', function($query){
                        return $query->orderBy('name', 'ASC');
                    })
                    ->when($request['sort_by'] == 'z-a', function($query){
                        return $query->orderBy('name', 'DESC');
                    })
                    ->when($request['sort_by'] == '', function($query){
                        return $query->latest();
                    });
            })
            ->when($request['min_price'] != null || $request['max_price'] != null, function($query) use($request){
                return $query->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
            })
            ->when($request['data_from'] == 'latest', function($query){
                return $query->latest();
            })
            ->when($request['data_from'] == 'top-rated', function($query){
                $reviews = Review::active()->select('product_id', DB::raw('AVG(rating) as count'))
                    ->groupBy('product_id')->where('status', 1)
                    ->orderBy("count", 'desc')->get();
                $product_ids = [];
                foreach ($reviews as $review) {
                    array_push($product_ids, $review['product_id']);
                }
                return $query->whereIn('id', $product_ids);
            })
            ->when($request['data_from'] == 'best-selling', function($query){
                $details = OrderDetail::with('product')
                    ->select('product_id', DB::raw('COUNT(product_id) as count'))
                    ->groupBy('product_id')
                    ->orderBy("count", 'desc')
                    ->get();
                $product_ids = [];
                foreach ($details as $detail) {
                    array_push($product_ids, $detail['product_id']);
                }
                $query->whereIn('id', $product_ids);
            })
            ->when($request['data_from'] == 'most-favorite', function($query){
                $details = Wishlist::with('product')
                    ->select('product_id', DB::raw('COUNT(product_id) as count'))
                    ->groupBy('product_id')
                    ->orderBy("count", 'desc')
                    ->get();
                $product_ids = [];
                foreach ($details as $detail) {
                    array_push($product_ids, $detail['product_id']);
                }
                $query->whereIn('id', $product_ids);
            })
            ->when($request['data_from'] == 'featured_deal', function($query){
                $featured_deal_id = FlashDeal::where(['status'=>1])->where(['deal_type'=>'feature_deal'])->pluck('id')->first();
                $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id',$featured_deal_id)->pluck('product_id')->toArray();
                $query->whereIn('id', $featured_deal_product_ids);
            })
            ->when($request['brand_id'] != '', function($query) use($request, $id){
                $query->where(['user_id'=>$id,'brand_id'=>$request->brand_id]);
            });

        if ($request['ratings'] != null)
        {
            $products->with('rating')->whereHas('rating', function($query) use($request){
                return $query;
            });

            $products = $products->get();
            $products = $products->map(function($product) use($request){
                $product->rating = $product->rating->pluck('average')[0];
                return $product;
            });

            $products = $products->where('rating','>=',$request['ratings'])
                ->where('rating','<',$request['ratings']+1)->paginate(10);
        }

        $products = $products->paginate(15);

        $data = [
            'id' => $request['id'],
            'name' => $request['name'],
            'data_from' => $request['data_from'],
            'sort_by' => $request['sort_by'],
            'page_no' => $request['page'],
            'min_price' => $request['min_price'],
            'max_price' => $request['max_price'],
        ];

        $current_date = date('Y-m-d');
        $seller_vacation_start_date = $id != 0 ? date('Y-m-d', strtotime($shop->vacation_start_date)) : null;
        $seller_vacation_end_date = $id != 0 ? date('Y-m-d', strtotime($shop->vacation_end_date)) : null;
        $seller_temporary_close = $id != 0 ? $shop->temporary_close : false;
        $seller_vacation_status = $id != 0 ? $shop->vacation_status : false;

        $temporary_close = Helpers::get_business_settings('temporary_close');
        $inhouse_vacation = Helpers::get_business_settings('vacation_add');
        $inhouse_vacation_start_date = $id == 0 ? $inhouse_vacation['vacation_start_date'] : null;
        $inhouse_vacation_end_date = $id == 0 ? $inhouse_vacation['vacation_end_date'] : null;
        $inHouseVacationStatus = $id == 0 ? $inhouse_vacation['status'] : false;
        $inhouse_temporary_close = $id == 0 ? $temporary_close['status'] : false;

        if ($request->ajax()) {
            return response()->json([
                'total_product'=>$products->total(),
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products'))->render(),
            ], 200);
        }

        return view(VIEW_FILE_NAMES['shop_view_page'], compact('products', 'shop', 'categories','current_date','seller_vacation_start_date','seller_vacation_status',
            'seller_vacation_end_date','seller_temporary_close','inhouse_vacation_start_date','inhouse_vacation_end_date','inHouseVacationStatus','inhouse_temporary_close',
            'products_for_review','featured_products','followers','follow_status','brands','data','ratings','rattingStatusArray'))
            ->with('seller_id', $id)
            ->with('total_review', $totalReviews)
            ->with('avg_rating', $averageRating)
            ->with('total_order', $totalOrder);
    }

    public function theme_fashion($request, $id){
        $business_mode = getWebConfig(name: 'business_mode');

        if($id!=0 && $business_mode == 'single') {
            Toastr::error(translate('access_denied!!'));
            return back();
        }

        if ($id != 0) {
            $shop = Shop::where('id', $id)->first();
            if (!$shop) {
                Toastr::error(translate('shop_does_not_exist'));
                return back();
            }else {
                $active_seller = Seller::approved()->find($shop['seller_id']);
                if(!$active_seller) {
                    Toastr::warning(translate('not_found'));
                    return redirect('/');
                }
            }
        }

        $id = $id != 0 ? Shop::where('id', $id)->first()->seller_id : $id;


        $product_ids = Product::active()
            ->when($id == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($id != 0, function ($query) use ($id) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $id);
            })
            ->pluck('id')->toArray();
        $reviewData = Review::active()->whereIn('product_id', $product_ids);
        $averageRating = $reviewData->avg('rating');
        $totalReviews = $reviewData->count();

        // color & seller wise review start
        $rattingStatusPositive = 0;
        $rattingStatusGood = 0;
        $rattingStatusNeutral = 0;
        $rattingStatusNegative = 0;
        foreach($reviewData->pluck('rating') as $singleRating)
        {
            ($singleRating >= 4?($rattingStatusPositive++):'');
            ($singleRating == 3?($rattingStatusGood++):'');
            ($singleRating == 2?($rattingStatusNeutral++):'');
            ($singleRating == 1?($rattingStatusNegative++):'');
        }
        $rattingStatusArray = [
            'positive' => $totalReviews != 0 ? ($rattingStatusPositive*100)/ $totalReviews:0,
            'good' => $totalReviews != 0 ?($rattingStatusGood*100)/ $totalReviews:0,
            'neutral' => $totalReviews != 0 ?($rattingStatusNeutral*100)/ $totalReviews:0,
            'negative' => $totalReviews != 0 ?($rattingStatusNegative*100)/ $totalReviews:0,
        ];

        $reviews = $reviewData->take(4)->get();
        $colors_collection = Product::active()->withCount('reviews')->whereIn('id', $product_ids)
            ->where('colors', '!=', '[]')
            ->pluck('colors')
            ->unique()
            ->toArray();

        $colors_in_shop_merge = [];
        foreach ($colors_collection as $color_json) {
            $color_array = json_decode($color_json, true);
            $colors_in_shop_merge = array_merge($colors_in_shop_merge, $color_array);
        }
        $colors_in_shop = array_unique($colors_in_shop_merge);
        // color & seller wise review end

        if($id == 0){
            $total_order = Order::where('seller_is','admin')->where('order_type','default_type')->count();
            $products_for_review = Product::active()->where('added_by', 'admin')->withCount('reviews')->count();
            $featured_products = Product::active()->where(['added_by'=>'admin','featured'=>'1'])
                                    ->with(['wishList'=>function($query){
                                        return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                                    }])->withCount('reviews')->get();
        }else{
            $seller = Seller::find($id);
            $total_order = $seller->orders->where('seller_is','seller')->where('order_type','default_type')->count();
            $products_for_review = Product::active()->where('added_by', 'seller')->where('user_id', $seller->id)->withCount('reviews')->count();
            $featured_products = Product::active()->where(['added_by'=>'seller','user_id'=>$seller->id,'featured'=>'1'])
                                    ->with(['wishList'=>function($query){
                                        return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                                    }])->withCount('reviews')->get();
        }

        // Followers
        $followers = ShopFollower::where('shop_id',$id)->count();
        $follow_status = 0;
        if(auth('customer')->check()){
            $follow_status = ShopFollower::where(['shop_id'=>$id,'user_id'=>auth('customer')->id()])->count();
        }

        //finding category ids
        $products = Product::active()
            ->when($id == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($id != 0, function ($query) use ($id) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $id);
            })->with(['wishList'=>function($query){
                return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
            }, 'compareList'=>function($query){
                return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
            }])->withSum('orderDetails', 'qty', function ($query) {
                $query->where('delivery_status', 'delivered');
            })
            ->withCount('reviews')
            ->get();

            $category_info_for_fashion = [];
            foreach ($products as $product) {
                array_push($category_info_for_fashion, $product['category_id']);
            }

        $categories = [];
        foreach ($category_info_for_fashion as $category_id) {
            $category = Category::withCount(['product'=>function($qc1) use($id){
                $qc1->when($id == 0, function($qc1){
                    $qc1->where(['added_by'=>'admin','status'=>'1']);
                })->when($id != 0, function($qc1) use($id){
                    $qc1->where(['added_by'=>'seller','user_id'=>$id,'status'=>'1']);
                });
            }])->with(['childes' => function ($qc2) {
                $qc2->with(['childes' => function ($qc3) {
                    $qc3->withCount(['subSubCategoryProduct'])->where('position', 2);
                }])->withCount(['subCategoryProduct'])->where('position', 1);
            }, 'childes.childes'])
                ->where('position', 0)
                ->find($category_id);

            if ($category != null) {
                array_push($categories, $category);
            }
        }
        $categories = array_unique($categories);

        //brand start
        $brand_info = [];
        foreach ($products as $product) {
            array_push($brand_info, $product['brand_id']);
        }

        $brands = Brand::active()->whereIn('id', $brand_info)->withCount('brandProducts')->latest()->get();
        foreach($brands as $brand)
        {
            $count = $products->where('brand_id', $brand->id)->count();
            $brand->count = $count;
        }

        if ($id == 0) {
            $shop = ['id' => 0, 'name' => Helpers::get_business_settings('company_name')];
        } else {
            $shop = Shop::where('seller_id', $id)->first();
        }

        $paginate_count = ceil($products->count() / 20);
        $products = $products->paginate(20);

        $current_date = date('Y-m-d');
        $seller_vacation_start_date = $id != 0 ? date('Y-m-d', strtotime($shop->vacation_start_date)) : null;
        $seller_vacation_end_date = $id != 0 ? date('Y-m-d', strtotime($shop->vacation_end_date)) : null;
        $seller_temporary_close = $id != 0 ? $shop->temporary_close : false;
        $seller_vacation_status = $id != 0 ? $shop->vacation_status : false;

        $temporary_close = Helpers::get_business_settings('temporary_close');
        $inhouse_vacation = Helpers::get_business_settings('vacation_add');
        $inhouse_vacation_start_date = $id == 0 ? $inhouse_vacation['vacation_start_date'] : null;
        $inhouse_vacation_end_date = $id == 0 ? $inhouse_vacation['vacation_end_date'] : null;
        $inHouseVacationStatus = $id == 0 ? $inhouse_vacation['status'] : false;
        $inhouse_temporary_close = $id == 0 ? $temporary_close['status'] : false;

        return view(VIEW_FILE_NAMES['shop_view_page'], compact('products', 'shop', 'categories','current_date','seller_vacation_start_date','seller_vacation_status',
            'seller_vacation_end_date','seller_temporary_close','inhouse_vacation_start_date','inhouse_vacation_end_date','inHouseVacationStatus','inhouse_temporary_close',
            'products_for_review','featured_products','followers','follow_status','brands','rattingStatusArray','reviews','colors_in_shop','paginate_count'))
            ->with('seller_id', $id)
            ->with('total_review', $totalReviews)
            ->with('avg_rating', $averageRating)
            ->with('total_order', $total_order);
    }

    /**
     * For Theme fashion, ALl purpose
     */
    public function ajax_filter_products(Request $request)
    {

        $categories = $request->category ?? [];
        $category = [];
        if($request->category)
        {
            foreach($categories as $category)
            {
                $cat_info = Category::where('id', $category)->first();
                $index = array_search($cat_info->parent_id, $categories);
                if ($index !== false) {
                    array_splice($categories, $index, 1);
                }
            }
            $category = Category::whereIn('id', $request->category)
                ->select('id', 'name')
                ->get();
        }

        $brands = [];
        if($request->brand)
        {
            $brands = Brand::whereIn('id', $request->brand)->select('id','name')->get();
        }
        $rating = $request->rating ?? [];

        // products search
        $products = Product::active()->withCount('reviews')->withSum('orderDetails', 'qty', function ($query) {
                $query->where('delivery_status', 'delivered');
            })
            ->with(['wishList'=>function($query){
                return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
            }, 'compareList'=>function($query){
                return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
            }])
            ->when($request->has('shop_id') && $request->shop_id == '0', function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($request->has('shop_id') && $request->shop_id != '0', function ($query) use ($request) {
                return $query->where(['added_by' => 'seller', 'user_id'=> $request->shop_id]);
            })
            ->when(!empty($request->brand), function($query) use($request){
                return $query->whereIn('brand_id', $request->brand);
            })
            ->when($request->has('category'), function($query) use($categories){
                return $query->whereIn('category_id', $categories)
                    ->orWhereIn('sub_category_id', $categories)
                    ->orWhereIn('sub_sub_category_id', $categories);
            })
            ->when($request->has('search_data_form') && $request->search_data_form == 'search', function($query) use($request){
                return $query->when($request->search_category_value == 'all', function($query) use($request){
                    return $query->where('name', 'Like', '%' . $request->name . '%');
                })->when($request->search_category_value != 'all', function($query) use($request){
                    return $query->where('category_id', $request->search_category_value)->where('name', 'Like', '%' . $request->name . '%');
                });
            })
            ->when($request->has('search_data_form') && $request->search_data_form == 'discounted', function($query) use($request){
                return $query->where('discount','!=',0);
            })
            ->when($request->has('search_data_form') && $request->search_data_form == 'featured', function($query) use($request){
                return $query->where('featured', 1);
            })
            ->when($request->has('sort_by'), function($query) use($request){
                    $query->when($request['sort_by'] == 'default', function($query){
                        return $query->orderBy('order_details_sum_qty', 'DESC');
                    })->when($request['sort_by'] == 'latest', function($query){
                        return $query->latest();
                    })
                    ->when($request['sort_by'] == 'low-high', function($query){
                        return $query->orderBy('unit_price', 'ASC');
                    })
                    ->when($request['sort_by'] == 'high-low', function($query){
                        return $query->orderBy('unit_price', 'DESC');
                    })
                    ->when($request['sort_by'] == 'a-z', function($query){
                        return $query->orderBy('name', 'ASC');
                    })
                    ->when($request['sort_by'] == 'z-a', function($query){
                        return $query->orderBy('name', 'DESC');
                    })
                    ->when($request['sort_by'] == '', function($query){
                        return $query->orderBy('order_details_sum_qty', 'DESC');
                    });
            })
            ->when(!empty($request['price_min']) || !empty($request['price_max']), function($query) use($request){
                return $query->whereBetween('unit_price', [Helpers::convert_currency_to_usd((int)$request['price_min']), Helpers::convert_currency_to_usd((int)$request['price_max'])]);
            })
            ->when(!empty($request->colors), function($query) use($request){
                return $query->where(function($query) use ($request) {
                    foreach ($request->colors as $color) {
                        $query->orWhere('colors', 'like', '%'.$color.'%');
                    }
                });
            })
            ->when($request->has('filter_by'), function($query) use($request){
                $query->when($request['filter_by'] == 'default', function($query){
                    return $query->orderBy('order_details_sum_qty', 'DESC');
                })
                ->when($request->filter_by == 'latest', function($query) use($request){
                    $query->latest();
                })->when($request->filter_by == 'discount', function($query) use($request){
                    $query->where('discount', '!=', 0);
                })->when($request->filter_by == 'top_rated', function($query) use($request){
                    $reviews = Review::active()->select('product_id', DB::raw('AVG(rating) as count'))
                        ->groupBy('product_id')
                        ->orderBy("count", 'DESC')->get();
                    $product_ids = [];
                    foreach ($reviews as $review) {
                        array_push($product_ids, $review['product_id']);
                    }
                    $query->whereIn('id', $product_ids);
                })->when($request->filter_by == 'best_selling', function($query) use($request){
                    $details = OrderDetail::with('product')
                        ->select('product_id', DB::raw('COUNT(product_id) as count'))
                        ->groupBy('product_id')
                        ->orderBy("count", 'DESC')
                        ->get();
                    $product_ids = [];
                    foreach ($details as $detail) {
                        array_push($product_ids, $detail['product_id']);
                    }
                    $query->whereIn('id', $product_ids);
                })->when($request->filter_by == 'featured', function($query) use($request){
                    $query->where('featured', 1);
                })->when($request->filter_by == 'most_loved', function($query) use($request){
                    $details = Wishlist::with('product')
                        ->select('product_id', DB::raw('COUNT(product_id) as count'))
                        ->groupBy('product_id')
                        ->orderBy("count", 'desc')
                        ->get();
                    $product_ids = [];
                    foreach ($details as $detail) {
                        array_push($product_ids, $detail['product_id']);
                    }
                    $query->whereIn('id', $product_ids);
                });
            })
            ->when(!empty($request->rating), function($query) use($request){
                $query->with(['rating'])->whereHas('rating', function($query) use($request){
                    return $query;
                });
            });

        if ($request->has('rating')) {
            $products = $products->get()->each(function($item){
                if(isset($item->rating) && count($item->rating) != 0)
                {
                    return $item->rating_avg = (int)$item->rating[0]['average'] ?? [''];
                }else{
                    return $item->rating_avg = [];
                }
            });
            $products = $products->whereIn('rating_avg',$request->rating);
        }

        $products_count = $products->count();
        $paginate_limit = 20;
        $paginate_count = ceil($products_count / $paginate_limit);

        $products = $products->skip(($request->page - 1) * $paginate_limit)
            ->take($paginate_limit)
            ->paginate($paginate_limit);

        return response()->json([
            'html_products'=>view('theme-views.product._ajax-products',['products'=>$products,'paginate_count'=>$paginate_count,'page'=>($request->page??1), 'request_data'=>$request->all()])->render(),
            'html_tags'=>view('theme-views.product._selected_filter_tags',['tags_category'=>$category,'tags_brands'=>$brands,'rating'=>$rating, 'sort_by'=>$request['sort_by']])->render(),
            'products_count'=>$products_count,
        ]);
    }

    public function filterProducts(Request $request)
    {
        $categories = $request->input('categories', []); // Ensure it's an array
    $sellerId = $request->input('shop_id');

    // Validate the input
    if (empty($categories) || empty($sellerId)) {
        return response()->json(['data' => 'No categories or seller ID provided'], 400);
    }

    // Retrieve filtered products with pagination
    $products = Product::whereIn('category_id', $categories)
                ->where('shop_id', $sellerId) // Filter by seller ID
                ->get();

    $decimal_point_settings = getWebConfig(name: 'decimal_point_settings'); // Retrieve decimal point settings

    // Check each product's added_by field and retrieve vacation settings
    foreach ($products as $product) {
        if (isset($product['added_by'])) {
            if ($product['added_by'] == 'seller') {
                $sellerVacationStartDate = isset($product->seller->shop->vacation_start_date) ? date('Y-m-d', strtotime($product->seller->shop->vacation_start_date)) : null;
                $sellerVacationEndDate = isset($product->seller->shop->vacation_end_date) ? date('Y-m-d', strtotime($product->seller->shop->vacation_end_date)) : null;
                $sellerTemporaryClose = isset($product->seller->shop->temporary_close) ? $product->seller->shop->temporary_close : false;
            }
            // Add logic for admin products if needed
            if ($product['added_by'] == 'admin') {
                // Admin specific logic can be added here
            }
        }
    }

    // Return the rendered view for AJAX
    return response()->json([
        'data' => view('web-views.products._ajax-products', compact('products', 'decimal_point_settings', 'sellerVacationStartDate', 'sellerVacationEndDate', 'sellerTemporaryClose'))->render()
    ]);
    }

    public function theme_all_purpose($request, $id){
       
        $business_mode = getWebConfig(name: 'business_mode');

        if($id!=0 && $business_mode == 'single') {
            Toastr::error(translate('access_denied!!'));
            return back();
        }

        if ($id != 0) {
            $shop = Shop::where('id', $id)->first();
            if (!$shop) {
                Toastr::error(translate('shop_does_not_exist'));
                return back();
            }else {
                $active_seller = Seller::approved()->find($shop['seller_id']);
                if(!$active_seller) {
                    Toastr::warning(translate('not_found'));
                    return redirect('/');
                }
            }
        }

        $id = $id != 0 ? Shop::where('id', $id)->first()->seller_id : $id;


        $product_ids = Product::active()->when($id == 0, function ($query) {
            return $query->where(['added_by' => 'admin']);
        })
            ->when($id != 0, function ($query) use ($id) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $id);
            })
            ->pluck('id')->toArray();

        $review_data = Review::active()->whereIn('product_id', $product_ids)->where('status',1);
        $avg_rating = $review_data->avg('rating');
        $total_review = $review_data->count();

        // color & seller wise review start
        $ratting_status_positive = 0;
        $ratting_status_good = 0;
        $ratting_status_neutral = 0;
        $ratting_status_negative = 0;
        foreach($review_data->pluck('rating') as $single_rating)
        {
            ($single_rating >= 4?($ratting_status_positive++):'');
            ($single_rating == 3?($ratting_status_good++):'');
            ($single_rating == 2?($ratting_status_neutral++):'');
            ($single_rating == 1?($ratting_status_negative++):'');
        }
        $ratting_status = [
            'positive' => $total_review != 0 ? ($ratting_status_positive*100)/ $total_review:0,
            'good' => $total_review != 0 ?($ratting_status_good*100)/ $total_review:0,
            'neutral' => $total_review != 0 ?($ratting_status_neutral*100)/ $total_review:0,
            'negative' => $total_review != 0 ?($ratting_status_negative*100)/ $total_review:0,
        ];
        $reviews = $review_data->take(4)->get();
        $colors_collection = Product::active()->whereIn('id', $product_ids)
            ->where('colors', '!=', '[]')
            ->pluck('colors')
            ->unique()
            ->toArray();

        $colors_in_shop_merge = [];
        foreach ($colors_collection as $color_json) {
            $color_array = json_decode($color_json, true);
            $colors_in_shop_merge = array_merge($colors_in_shop_merge, $color_array);
        }
        $colors_in_shop = array_unique($colors_in_shop_merge);
        // color & seller wise review end

        if($id == 0){
            $total_order = Order::where('seller_is','admin')->where('order_type','default_type')->count();
            $products_for_review = Product::active()->where('added_by', 'admin')->withCount('reviews')->count();
            $featured_products = Product::active()->withCount('reviews')->where(['added_by'=>'admin','featured'=>'1'])->get();
        }else{
            $seller = Seller::find($id);
            $total_order = $seller->orders->where('seller_is','seller')->where('order_type','default_type')->count();
            $products_for_review = Product::active()->where('added_by', 'seller')->where('user_id', $seller->id)->withCount('reviews')->count();
            $featured_products = Product::active()->withCount('reviews')->where(['added_by'=>'seller','user_id'=>$seller->id,'featured'=>'1'])->get();
        }

        // Followers
        $followers = ShopFollower::where('shop_id',$id)->count();
        $follow_status = 0;
        if(auth('customer')->check()){
            $follow_status = ShopFollower::where(['shop_id'=>$id,'user_id'=>auth('customer')->id()])->count();
        }
        $categories = [];
        $products = [];
        $brands = [];
        if($request['tab'] == 'all_product'){
            $products = Product::active()
                                ->when($id == 0, function ($query) use($request){
                                    return $query->when($request['search'],function($sub_query)use($request){
                                        $sub_query->where('name', 'like', "%{$request['search']}%");
                                    })->where('added_by', 'admin');
                                })
                                ->when($id != 0, function ($query) use ($id,$request) {
                                            return $query->when($request['search'],function($sub_query)use($request){
                                                $sub_query->where('name', 'like', "%{$request['search']}%");
                                            })->where('added_by', 'seller')
                                              ->where('user_id', $id);
                                })->orderBy('id','desc')->paginate(15)->appends(['tab'=>'all_product','search'=>$request['search']]);
            $category_info_for_fashion = [];
            foreach ($products as $product) {
            array_push($category_info_for_fashion, $product['category_id']);
            }
            foreach ($category_info_for_fashion as $category_id) {
                $category = Category::withCount(['product' => function ($query) use ($id) {
                                    $query->when($id == 0, function ($sub_query) {
                                        $sub_query->where(['added_by' => 'admin', 'status' => '1']);
                                    })->when($id != 0, function ($sub_query) use ($id) {
                                        $sub_query->where(['added_by' => 'seller', 'user_id' => $id, 'status' => '1']);
                                    });
                                }])->where('position', 0)->find($category_id);
                if ($category != null) {
                    array_push($categories, $category);
                }
            }
            $categories = array_unique($categories);
            $brand_info = [];
            foreach ($products as $product) {
                    array_push($brand_info, $product['brand_id']);
                }
                $brands = Brand::active()->whereIn('id', $brand_info)
                                        ->withCount('brandProducts')->latest()->get();
                foreach($brands as $brand)
                {
                    $count = $products->where('brand_id', $brand->id)->count();
                    $brand->count = $count;
                }
        }

        if ($id == 0) {
            $shop = ['id' => 0, 'name' => Helpers::get_business_settings('company_name')];
        } else {
            $shop = Shop::where('seller_id', $id)->first();
        }
        $current_date = date('Y-m-d');
        $seller_vacation_start_date = $id != 0 ? date('Y-m-d', strtotime($shop->vacation_start_date)) : null;
        $seller_vacation_end_date = $id != 0 ? date('Y-m-d', strtotime($shop->vacation_end_date)) : null;
        $seller_temporary_close = $id != 0 ? $shop->temporary_close : false;
        $seller_vacation_status = $id != 0 ? $shop->vacation_status : false;

        $temporary_close = Helpers::get_business_settings('temporary_close');
        $inhouse_vacation = Helpers::get_business_settings('vacation_add');
        $inhouse_vacation_start_date = $id == 0 ? $inhouse_vacation['vacation_start_date'] : null;
        $inhouse_vacation_end_date = $id == 0 ? $inhouse_vacation['vacation_end_date'] : null;
        $inHouseVacationStatus = $id == 0 ? $inhouse_vacation['status'] : false;
        $inhouse_temporary_close = $id == 0 ? $temporary_close['status'] : false;

        $top_rated = [];
        $new_arrival = [];
        $coupons = [];
        if($request['tab']== 'store' || null){
            //top-rated
            $top_rated = Product::active()
                                ->when($id == 0, function($query){
                                    $reviews = Review::active()->select('product_id', DB::raw('AVG(rating) as count'))
                                        ->groupBy('product_id')
                                        ->orderBy("count", 'desc')->get();
                                    $product_ids = [];
                                    foreach ($reviews as $review) {
                                        array_push($product_ids, $review['product_id']);
                                    }
                                    return $query->where('added_by', 'admin')->whereIn('id', $product_ids);
                                })
                                ->when($id != 0, function($query)use($id){
                                    $reviews = Review::active()->select('product_id', DB::raw('AVG(rating) as count'))
                                        ->groupBy('product_id')
                                        ->orderBy("count", 'desc')->get();
                                    $product_ids = [];
                                    foreach ($reviews as $review) {
                                        array_push($product_ids, $review['product_id']);
                                    }
                                    return $query->where(['added_by'=>'seller','user_id'=>$id])->whereIn('id', $product_ids);
                                })->take(12)->get();
            //new arrival
            $new_arrival = Product::active()
                        ->when($id == 0, function ($query) {
                            return $query->where('added_by', 'admin');
                        })
                        ->when($id != 0, function ($query) use($id){
                            return $query->where(['added_by'=>'seller','user_id'=>$id]);
                        })
                        ->latest()->take(6)->get();
            //shop wise coupon
            $coupons = Coupon::when($id == 0, function ($query) {
                                return $query->where('added_by', 'admin')
                                            ->where(function ($subquery) {
                                                $subquery->whereNull('seller_id')
                                                        ->orWhere('seller_id', 0);
                                        });
                                })
                        ->when($id != 0, function ($query) use ($id) {
                            return $query->where('added_by', 'seller')
                                        ->where(function ($subquery) use ($id) {
                                        $subquery->where('seller_id', 0)
                                                ->orWhere('seller_id', $id);
                                    });
                            })
                        ->whereDate('start_date', '<=', date('Y-m-d'))
                        ->whereDate('expire_date', '>=', date('Y-m-d'))
                        ->get();
        }

        return view(VIEW_FILE_NAMES['shop_view_page'], compact('products', 'shop', 'categories','current_date','seller_vacation_start_date','seller_vacation_status',
            'seller_vacation_end_date','seller_temporary_close','inhouse_vacation_start_date','inhouse_vacation_end_date','inHouseVacationStatus','inhouse_temporary_close',
            'products_for_review','featured_products','followers','follow_status','brands','ratting_status','reviews','colors_in_shop','coupons','id','new_arrival','top_rated'))
            ->with('seller_id', $id)
            ->with('total_review', $total_review)
            ->with('avg_rating', $avg_rating)
            ->with('total_order', $total_order);
    }
    public function ajax_shop_vacation_check(Request $request){
        $current_date = date('Y-m-d');
        $vacation_start_date = $current_date;
        $vacation_end_date = $current_date;
        $temporary_close = null;
        $vacation_status = null;

        if($request->added_by == "seller"){
            $shop = Shop::where('seller_id',$request->user_id)->first();
            $vacation_start_date = $shop->vacation_start_date ? date('Y-m-d', strtotime($shop->vacation_start_date)):null;
            $vacation_end_date = $shop->vacation_end_date ? date('Y-m-d', strtotime($shop->vacation_end_date)) : null;
            $temporary_close = $shop->temporary_close ;
            $vacation_status =  $shop->vacation_status ;
        }else{
            $temporary_close = Helpers::get_business_settings('temporary_close');
            $inhouse_vacation = Helpers::get_business_settings('vacation_add');
            $vacation_start_date = $inhouse_vacation['vacation_start_date'];
            $vacation_end_date = $inhouse_vacation['vacation_end_date'];
            $vacation_status = $inhouse_vacation['status'] ;
            $temporary_close =  $temporary_close['status'];
        }

        if($temporary_close  || ($vacation_status  && $current_date >= $vacation_start_date && $current_date <= $vacation_end_date)){
            return response()->json(['status'=>'inactive']);
        }else{
            $product_data = Product::find($request->id);

            unset($request['added_by']);
            $request['quantity'] =  $product_data->minimum_order_qty;

            $cart = \App\Utils\CartManager::add_to_cart($request);
            session()->forget('coupon_code');
            session()->forget('coupon_type');
            session()->forget('coupon_bearer');
            session()->forget('coupon_discount');
            session()->forget('coupon_seller_id');
            return response()->json($cart);
        }
    }
}
