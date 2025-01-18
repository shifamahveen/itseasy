<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Supercredit extends Model
{
    use HasFactory;
    /**
     * Get the items from database
     */
    public static function getItems()
    {
        $search = request()->get('search');
        $data = Supercredit::where('client_slug', 'LIKE', '%' . $search . '%')->orderBy('id', 'desc')->paginate(30);
        return $data;
    }

    /**
     * Add credits
     */
    public static function addCredits($credits, $client_slug)
    {
        $model = new Self();
        $model->credit = $credits;
        $model->slug = Str::random(5);
        $model->client_slug = $client_slug;
        $model->user_id = 1;
        $model->payment = 'Manual';
        $model->mode = 'client';
        $model->type = 'credit';
        $model->save();

        self::refreshCache();
    }

    /**
     * Get the balance credits
     */
    public static function balance()
    {
        $balance = Supercredit::sum('credit');
        return $balance;
    }

    /**
     * Refresh the items in cache
     */
    public static function refreshCache()
    {
        // update cache for supercredit
        Cache::forget('creditbalance_' . client('slug'));
        Cache::forget('supercredit_balance');
        Cache::forever('supercredit_balance', SuperCredit::balance());
    }
}
