<?php

namespace App\Http\Helpers\Community;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
// import the model
use App\Models\Community\Feed;

class FeedHelper
{
    /** 
     * Cache Variables used in the current model
     * -> feeds_frontpage_ . $client_slug (records)
     * -> feeds_count_ . $client_slug (count of categories)
     * -> feed_ . $slug (one record)
     * -> color_feed_ . $client_slug (colors from settings)
     */

    /**
     * clear feed cache
     */
    public static function cacheRefresh($slug = null)
    {
        // frontpage & dashboard public listing
        Cache::forget('feeds_frontpage_' . client('slug'));
        Cache::forget('feeds_dashboard_' . client('slug'));
        // category counter
        Cache::forget('feeds_count_' . client('slug'));
        // current record
        if ($slug)
            Cache::forget('feed_' . $slug);
        // remove cache timestamp
        Cache::forget('feed_cacheTimestamp_' . client('slug'));
    }

    /**
     * settingsCacheRefresh
     */
    public static function settingsCacheRefresh()
    {
        //delete settings from cache
        Cache::forget('settings_feed_' . client('slug'));
        // delete category colors from cache
        Cache::forget('color_feed_' . client('slug'));
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
                return Cache::remember('feeds_dashboard_' . client('slug'), 86400, function () {
                    return Feed::getRecords(1);
                });
            } else {
                return Cache::remember('feeds_frontpage_' . client('slug'), 86400, function () {
                    return Feed::getRecords(1);
                });
            }
        } else {
            return Feed::getRecords(1);
        }
    }

    /**
     *  load count from cache
     */
    public static function getCacheCount()
    {
        return Cache::remember('feeds_count_' . client('slug'), 86400, function () {
            return Feed::getCount();
        });
    }

    /**
     * Load the record from cache
     */
    public static function getCacheRecord($slug)
    {
        return Cache::remember('feed_' . $slug, 86400, function () use ($slug) {
            return Feed::getRecord($slug);
        });
    }

    /**
     * load default settings
     */
    public function defaultSettings()
    {
        // load the data from cloud
        $settingsUrl = client('slug') . "/feed/settings/settings.json";
        $settings = Storage::get($settingsUrl);
        //create settings if not created
        // if settings are null then use the default
        if (!$settings || $settings == "null") {
            $settings = file_get_contents('default_settings.json');
            $settings = json_encode(json_decode($settings), JSON_PRETTY_PRINT);
            Storage::put($settingsUrl, $settings);
        }
    }

    /**
     * Load color for categories
     */
    public static function categoryColors()
    {
        //load colors from the global settings
        $colors = Cache::remember('color_feed_' . client('slug'), 86400, function () {
            $settingsUrl = client('slug') . "/feed/settings/settings.json";
            $settings = json_decode(Storage::get($settingsUrl));
            $colors = [];
            if (isset($settings->category)) {
                foreach ($settings->category as $c) {
                    $colors[$c->name] = $c->bgcolor;
                }
            }
            return $colors;
        });
        return $colors;
    }

    /**
     * load timestamp from cache
     */
    public function getCacheTimestamp()
    {
        // record the timestamp
        return Cache::remember('feed_cacheTimestamp_' . client('slug'), 86400, function () {
            $date = now();
            $timestamp = \Carbon\Carbon::parse($date);
            return $timestamp->format('d M Y g:i A');
        });
    }

    /**
     * get meta info and ads
     */
    public static function getSettingsData($name = null)
    {
        //load settings from cloud or cache
        $settingsUrl = client('slug') . "/feed/settings/settings.json";
        $settings = Cache::remember('settings_feed_' . client('slug'), 86400, function () use ($settingsUrl) {
            return json_decode(Storage::get($settingsUrl), 1);
        });

        //if not key value, return all settings
        if (!$name)
            return $settings;
        // if key is defined the return according to the name
        if ($name == 'name') {
            if (isset($settings['name']))
                return $settings['name'];
            else
                return "";
        }
        //meta title
        if ($name == 'meta_title') {
            if (isset($settings['meta_title']))
                return $settings['meta_title'];
            else
                return "";
        }
        // ads array
        if ($name == 'ads') {
            if (isset($settings['ads'][0]))
                return  $settings['ads'][0];
            else
                return [];
        }
    }

    /**
     * Validation rules
     */
    public static function validation($update = null)
    {
        $request = request();
        // initialize errors as empty array
        $errors = [];
        /* validation */
        if ($update) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255',
                'image' => 'file|mimes:jpg,png'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255',
                'slug' => 'required|unique:feeds',
                'image' => 'file|mimes:jpg,png'
            ]);
        }


        if ($validator->fails()) {
            $errors = $validator->errors();
        }
        // return error if video url is invalid
        if ($request->get('type') == 'video' && !isYoutubeUrl($request->video)) {
            $validator->errors()->add('Invalid Video', 'Invalid video url. Kindly re-verify url.');
            $errors = $validator->errors();
        }
        // return error if link url is invalid
        if ($request->get('type') == 'link' && !filter_var($request->link, FILTER_VALIDATE_URL)) {
            $validator->errors()->add('Invalid Link', 'Invalid link url given.');
            $errors = $validator->errors();
        }

        //return errors
        return  $errors;
    }
}
