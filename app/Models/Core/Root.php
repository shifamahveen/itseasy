<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Core\Credit;

class Root extends Model
{
    use HasFactory;

    /**
     * Refresh Cache
     */
    public function cacheRefresh()
    {
        // remove data from cache on refresh
        if (request()->get('cache_refresh')) {
            Cache::forget('root_cacheTimestamp_' . client('slug'));
            Cache::forget('usercount_' . client('slug'));
            Cache::forget('creditbalance_' . client('slug'));
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
            'event' => 0
        ];

        /* get the admin apps from env folder or client settings */
        if (client('slug') == config('services.admin_apps'))
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
     * load timestamp from cache
     */
    public function getCacheTimestamp()
    {
        return Cache::remember('root_cacheTimestamp_' . client('slug'), 86400, function () {
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
        return Cache::remember('usercount_' . client('slug'), 86400, function () {
            return User::where('client_slug', client('slug'))->count();
        });
    }

    /**
     * Credit Balance for client
     */
    public function getCreditBalance()
    {
        return Cache::remember('creditbalance_' . client('slug'), 86400, function () {
            return Credit::balance(client('slug'));
        });
    }
}
