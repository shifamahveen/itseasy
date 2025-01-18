<?php

namespace App\Http\Helpers\Hire;

use Illuminate\Support\Facades\Cache;
use App\Models\Hire\Job;

class JobHelper
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
    public static function cacheRefresh($slug = null)
    {
        //as jobs are posted to core website, we refresh the core site only
        $client_slug = config('services.jobclient_slug');
        // frontpage & dashboard public listing
        Cache::forget('jobs_frontpage_' . $client_slug);
        Cache::forget('jobs_dashboard_' . $client_slug);
        // category counter
        Cache::forget('jobs_count_' . $client_slug);
        // current record
        if ($slug)
            Cache::forget('job_' . $slug);
        // remove cache timestamp
        Cache::forget('job_cacheTimestamp_' . $client_slug);
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
                return Cache::remember('jobs_dashboard_' . client('slug'), 86400, function () {
                    return Job::getRecords(1);
                });
            } else {
                return Cache::remember('jobs_frontpage_' . client('slug'), 86400, function () {
                    return Job::getRecords(1);
                });
            }
        } else {
            return Job::getRecords(1);
        }
    }

    /**
     *  load count from cache
     */
    public static function getCacheCount()
    {
        return Cache::remember('jobs_count_' . client('slug'), 86400, function () {
            return Job::getCount();
        });
    }

    /**
     * Load the record from cache
     */
    public static function getCacheRecord($slug)
    {
        return Cache::remember('job_' . $slug, 86400, function () use ($slug) {
            return Job::getRecord($slug);
        });
    }


    /**
     * load timestamp from cache
     */
    public function getCacheTimestamp()
    {
        return Cache::remember('job_cacheTimestamp_' . client('slug'), 86400, function () {
            $date = now();
            $timestamp = \Carbon\Carbon::parse($date);
            return $timestamp->format('d M Y g:i A');
        });
    }
}
