<?php

namespace App\Http\Helpers\Blog;

use Illuminate\Support\Facades\Cache;
use App\Models\Blog\Post;

class PostHelper
{

    /**
     * clear Blog cache
     */
    public static function cacheRefresh()
    {
        Cache::forget('blogs_dashboard_' . client('slug'));
    }

    /**
     *  load records from cache
     */
    public static function getCacheRecords()
    {
        // data is cached if
        // - request is from public page (only first sheet)
        // - without filter, page and search
        return Cache::remember('blogs_dashboard_' . client('slug'), 10000, function () {
            return Post::getLatestRecords();
        });
    }

    /**
     *  latest blog
     */
    public static function getLatestPost()
    {
        return Post::getLatestRecord();
    }
}
