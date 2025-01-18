<?php

namespace App\Http\Helpers\Core;

use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Core\Credit;
use App\Models\Test\Test;
//dashboard apps helpers
use App\Http\Helpers\Community\FeedHelper;
use App\Http\Helpers\Hire\JobHelper;
use App\Http\Helpers\Collection\ProductHelper;
use App\Http\Helpers\Blog\PostHelper;
use App\Http\Helpers\Test\TestHelper;
use Illuminate\Support\Facades\Storage;


class RootHelper
{

    /**
     * Refresh Cache
     */
    public function cacheRefresh()
    {
        // remove data from cache on refresh
        if (request()->get('cache_refresh')) {
            Cache::forget('admin_cacheTimestamp_' . 'super');
            Cache::forget('usercount_' . 'super');
            Cache::forget('creditbalance_' . 'super');
            Cache::forget('getLastPost_' . 'super');
            JobHelper::cacheRefresh();
            ProductHelper::cacheRefresh();
            PostHelper::cacheRefresh();
            TestHelper::cacheRefresh();
        }
    }

    /**
     * Dashboard Apps Registration
     */
    public function getDashboardApps($pos = null)
    {
        $Apps = [];
        $main = [];
        $side = [];
        $dapps = client('dapps');
        if ($dapps)
            foreach ($dapps as $a) {
                if ($a->position == 'main' && $a->status) {
                    $main[$a->sequence] = $a->name;
                }
                if ($a->position == 'side' && $a->status) {
                    $side[$a->sequence] = $a->name;
                }
            }
        if ($pos == null)
            $pos = 'main';

        if ($pos == 'main') {
            ksort($main);
            foreach ($main as $k => $m) {
                $Apps[$m] = 1;
            }
            return $Apps;
        } else {
            sort($side);
            foreach ($side as $k => $m) {
                $Apps[$m] = 1;
            }
            return $Apps;
        }
    }

    /**
     * Admin Apps Registration
     */
    public function getAdminApps()
    {
        /* list of voilaco apps */
        $apps = [
            'client' => 0,
            'setting' => 0,
            'supercredit' => 0,
            'credit' => 0,
            'user' => 0,
            'test' => 0,
            'feed' => 0,
            'dummy' => 0,
            'application' => 0,
            'event' => 0,
            'page' => 0,
            'job' => 0,
            'college' => 0,
            'product' => 0,
            'blog' => 0,
            'coupon' => 0,
            'training' => 0
        ];

        /* get the admin apps from env folder or client settings */
        if ('super' == config('services.superadmin.clientslug'))
            $adminApps = explode(",", config('services.admin_apps'));
        else
            $adminApps = explode(",", client('admin_apps'));


        /* register the admin apps */
        foreach ($apps as $a => $b) {
            foreach ($adminApps as $adApp) {
                if ($a == $adApp)
                    $apps[$a] = 1;
            }
        }

        return $apps;
    }

    /** 
     * Get jobs for dashboard
     */
    public function getJobs()
    {
        $jobhelper = new JobHelper();
        return $jobhelper->getCacheRecords('dashboard');
    }

    public function getJobSettings()
    {
        $settingsUrl = 'super' . "/job/settings/settings.json";
        if (Storage::exists($settingsUrl)) {
            $settings = Storage::get($settingsUrl);
            return json_decode($settings);
        }
        return null;
    }

    /** 
     * Get products for dashboard
     */
    public function getProducts()
    {
        $phelper = new ProductHelper();
        return $phelper->getCacheRecords();
    }

    /** 
     * Get tests for dashboard
     */
    public function getTestLog()
    {
        $testhelper = new TestHelper();
        return $testhelper->getTestLogCacheRecords();
    }

    /** 
     * Get feeds for dashboard
     */
    public function getFeeds()
    {
        $feedhelper = new FeedHelper();
        return $feedhelper->getCacheRecords();
    }

    public function getFeedSettings()
    {
        $settingsUrl = 'super' . "/feed/settings/settings.json";
        if (Storage::exists($settingsUrl)) {
            $settings = Storage::get($settingsUrl);
            return json_decode($settings);
        }
        return null;
    }

    /** 
     * Get feeds for dashboard
     */
    public function getBlogs()
    {
        $posthelper = new PostHelper();
        return $posthelper->getCacheRecords();
    }

    /** 
     * Get latest post
     */
    public function getPost()
    {
        return Cache::remember('getLastPost_' . 'super', 20000, function () {
            $posthelper = new PostHelper();
            return $posthelper->getLatestPost();
        });
    }

    /** 
     * Get creditlog
     */
    public function getCreditLog()
    {
        return Credit::where('client_slug', 'super')->orderBy('id', 'desc')->limit(5)->get();
    }

    /** 
     * Get Tests
     */
    public function getTests()
    {
        $tests = Test::where('client_slug', 'super')->where('settings', 'LIKE', '%"featured":"1"%')->orderBy('id', 'desc')->limit(15)->get();
        return $tests;
    }

    /** 
     * Get Category Colors
     */
    public function getCategoryColors()
    {
        $feedhelper = new FeedHelper();
        return $feedhelper->categoryColors();
    }

    /**
     * load timestamp from cache
     */
    public function getCacheTimestamp()
    {
        return Cache::remember('admin_cacheTimestamp_' . 'super', 86400, function () {
            $date = now();
            $timestamp = \Carbon\Carbon::parse($date);
            return $timestamp->format('d M Y g:i A');
        });
    }

    /**
     * User count for the client
     */
    public function getUserCount()
    {
        return Cache::remember('usercount_' . 'super', 86400, function () {
            return User::where('client_slug', 'super')->count();
        });
    }

    /**
     * Credit Balance for client
     */
    public static function getCreditBalance()
    {
        return Cache::remember('creditbalance_' . 'super', 86400, function () {
            return Credit::balance('super');
        });
    }
}