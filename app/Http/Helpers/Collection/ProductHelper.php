<?php

namespace App\Http\Helpers\Collection;

use Illuminate\Support\Facades\Cache;
use App\Models\Collection\Product;

class ProductHelper
{
    /** 
     * Cache Variables used in the current model
     * -> Jobs_frontpage_ . $client_slug (records)
     * -> Jobs_count_ . $client_slug (count of categories)
     * -> Job_ . $slug (one record)
     * -> color_Job_ . $client_slug (colors from settings)
     */

    /**
     * clear Job cache
     */
    public static function cacheRefresh()
    {
        Cache::forget('products_dashboard_' . user('id'));
        Cache::forget('productlist_' . client('slug'));
    }

    /**
     *  load records from cache
     */
    public static function getCacheRecords()
    {
        // data is cached if
        // - request is from public page (only first sheet)
        // - without filter, page and search
        return Cache::remember('products_dashboard_' . user('id'), 10000, function () {
            return Product::getMyRecords();
        });
    }
}
