<?php

namespace App\Http\Helpers\Test;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Test\Test;
use App\Models\Test\Report;

class TestHelper
{
    /** 
     * Cache Variables used in the current model
     * -> tests_frontpage_ . $client_slug (records)
     * -> tests_count_ . $client_slug (count of categories)
     * -> test_ . $slug (one record)
     * -> color_test_ . $client_slug (colors from settings)
     */

    /**
     * clear test cache
     */
    public static function cacheRefresh($slug = null)
    {
        // frontpage & dashboard public listing
        Cache::forget('tests_frontpage_' . client('slug'));
        Cache::forget('tests_dashboard_' . client('slug'));
        // category counter
        Cache::forget('tests_count_' . client('slug'));

        Cache::forget('mytests_' . client('slug') . '_' . user('phone'));
        // current record
        if ($slug)
            Cache::forget('test_' . $slug);
        // remove cache timestamp
        Cache::forget('test_cacheTimestamp_' . client('slug'));
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
                return Cache::remember('tests_dashboard_' . client('slug'), 86400, function () {
                    return Test::getRecords(1);
                });
            } else {
                return Cache::remember('tests_frontpage_' . client('slug'), 86400, function () {
                    return Test::getRecords(1);
                });
            }
        } else {
            return test::getRecords(1);
        }
    }

    /**
     *  load records from cache
     */
    public static function getTestLogCacheRecords($name = null)
    {
        return Cache::remember('mytests_' . client('slug') . '_' . user('phone'), 86400, function () {
            return Test::mytestlog();
        });
    }

    /**
     *  load count from cache
     */
    public static function getCacheCount()
    {
        return Cache::remember('tests_count_' . client('slug'), 86400, function () {
            return Test::getCount();
        });
    }

    /**
     * Load the record from cache
     */
    public static function getCacheRecord($slug)
    {
        return Cache::remember('test_' . $slug, 86400, function () use ($slug) {
            return Test::getRecord($slug);
        });
    }

    /**
     * load default settings
     */
    public function defaultSettings($client_slug = null)
    {
        if (!$client_slug)
            $settingsUrl = client('slug') . "/test/settings/settings.json";
        else
            $settingsUrl = $client_slug . "/test/settings/settings.json";
        // load the data from cloud
        $settings = Storage::get($settingsUrl);
        //create settings if not created
        // if settings are null then use the default
        //$settings = Storage::get($settingsUrl);

        if (!$settings || $settings == "null") {
            $settings = file_get_contents('settings/default_test.json');
            $settings = json_encode(json_decode($settings), JSON_PRETTY_PRINT);
            Storage::put($settingsUrl, $settings);
        }

        $settings = json_decode(Storage::get($settingsUrl));

        if (!isset($settings->client_code)) {
            $settings->client_code = strtolower(Str::random(2));
            $settings = json_encode($settings, JSON_PRETTY_PRINT);
            Storage::put($settingsUrl, $settings);
            Cache::forget('settings_test_' . client('slug'));
            $settings = json_decode(Storage::get($settingsUrl));
        }

        return $settings->client_code;
    }

    /**
     * Generate Testslug specific to roles
     */
    public function generateTestSlug($str = null)
    {
        // vendor code
        if (client('slug') == 'super' && user('role') == 'superadmin') {
            $vendor_code = 'vc';
        } else {
            $vendor_code = substr(config('services.app_slug'), 0, 2);
        }

        // library code
        if (client('slug') == 'super' && user('role') == 'superadmin') {
            $library_code = 'gt';
        } elseif (client('slug') == 'super' && user('role') == 'rootadmin') {
            $library_code = 'vt';
        } else {
            $library_code = 'ct';
        }
        //client code
        $settings = self::getSettingsData();
        $client_code = $settings['client_code'];

        //random code
        $random_code = strtolower(substr(md5(rand()), 0, 5));
        //if random code is already there in str then use it
        if ($str)
            return strtolower($vendor_code . $library_code . $client_code . 'w' . $str);
        // generated Slug
        if (user('role') == 'rootadmin' || user('role') == 'superadmin')
            return  strtolower($vendor_code . $library_code . 'w' . $random_code);
        else
            return  strtolower($vendor_code . $library_code . $client_code . '2' . $random_code);
    }

    /**
     * Generate Testslug specific to roles
     */
    public function allocateTestSlug($str, $client_code)
    {
        // vendor code
        $vendor_code = substr(config('services.app_slug'), 0, 2);

        // library code
        $library_code = 'ct';

        return strtolower($vendor_code . $library_code . $client_code . 'w' . $str);
    }

    /**
     * Load color for categories
     */

    public static function categoryColors()
    {
        //load colors from the global settings
        $colors = Cache::remember('color_test_' . client('slug'), 86400, function () {
            $settingsUrl = client('slug') . "/test/settings/settings.json";
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
        return Cache::remember('test_cacheTimestamp_' . client('slug'), 86400, function () {
            $date = now();
            $timestamp = \Carbon\Carbon::parse($date);
            return $timestamp->format('d M Y g:i A');
        });
    }

    /**
     * get meta info and ads
     */
    public function getSettingsData($name = null)
    {
        $settingsUrl = client('slug') . "/test/settings/settings.json";

        $settings = Cache::remember('settings_test_' . client('slug'), 86400, function () use ($settingsUrl) {
            return json_decode(Storage::get($settingsUrl), 1);
        });
        return $settings;
    }
}
