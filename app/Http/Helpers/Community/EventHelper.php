<?php

namespace App\Http\Helpers\Community;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

use App\Models\Community\Event;

class EventHelper
{
    /** 
     * Cache Variables used in the current model
     * -> event_frontpage_ . $client_slug (records)
     * -> event_count_ . $client_slug (count of categories)
     * -> event_ . $slug (one record)
     */

    /**
     * clear event cache
     */
    public static function cacheRefresh($slug = null)
    {
        // frontpage & dashboard public listing
        Cache::forget('events_frontpage_' . client('slug'));
        Cache::forget('events_dashboard_' . client('slug'));
        // category counter
        Cache::forget('events_count_' . client('slug'));
        // current record
        if ($slug)
            Cache::forget('event_' . $slug);
        // remove cache timestamp
        Cache::forget('event_cacheTimestamp_' . client('slug'));
    }

    /**
     *  load records from cache
     */
    public static function getCacheRecords($name = null)
    {
        // data is cached if
        // - request is from public page (only first sheet)
        // - without filter, page and search
        if (request()->get('filter') == null && request()->get('page') == null && request()->get('search') == null) {
            if ($name == 'dashboard') {
                return Cache::remember('events_dashboard_' . client('slug'), 86400, function () {
                    return Event::getRecords(1);
                });
            } else {
                return Cache::remember('events_frontpage_' . client('slug'), 86400, function () {
                    return Event::getRecords(1);
                });
            }
        } else {
            return Event::getRecords(1);
        }
    }

    /**
     *  load count from cache
     */
    public static function getCacheCount()
    {
        return Cache::remember('events_count_' . client('slug'), 86400, function () {
            return Event::getCount();
        });
    }

    /**
     * Load the record from cache
     */
    public static function getCacheRecord($slug)
    {
        return Cache::remember('event_' . $slug, 86400, function () use ($slug) {
            return Event::getRecord($slug);
        });
    }



    /**
     * load timestamp from cache
     */
    public function getCacheTimestamp()
    {
        return Cache::remember('event_cacheTimestamp_' . client('slug'), 86400, function () {
            $date = now();
            $timestamp = \Carbon\Carbon::parse($date);
            return $timestamp->format('d M Y g:i A');
        });
    }
}
