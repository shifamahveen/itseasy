<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Core\Supercredit;
use App\Models\Core\Client;
use Illuminate\Support\Str;
use App\Http\Helpers\Core\RootHelper;


class Credit extends Model
{
    use HasFactory;
    /**
     * Get the items from database
     */
    public static function getItems()
    {
        $search = request()->get('search');
        if (user('role') == 'superadmin' || user('role') == 'rootadmin')
            return Credit::where('client_slug', 'LIKE', '%' . $search . '%')->orderBy('id', 'desc')->paginate(30);
        else
            return Credit::where('client_slug', client('slug'))->orderBy('id', 'desc')->paginate(30);
    }

    /**
     * List of all clients and their credit balance
     */
    public static function loadClientCreditBalance()
    {
        $clientlist = Cache::get('clientlist');
        $balance = 0;
        if ($clientlist)
            foreach ($clientlist as $client_slug => $client) {
                $balance[$client_slug] = Cache::get('clientcredit_balance_' . $client_slug);
            }
        return $balance;
    }

    /**
     * Add credits
     */
    public static function addCredits($credits, $client_slug)
    {

        $record = new Self();
        $record->credit = $credits;
        $record->slug = Str::random(5);
        $record->client_slug = $client_slug;
        $record->user_id = user()['id'];
        $record->payment = 'Manual';
        $record->mode = 'client';
        $record->description = "Credits added by " . User('name');
        $record->reference = '';
        $record->type = 'credit';
        $record->save();

        $record->refreshCache();
    }

    /**
     * Use credits
     */
    public static function useCredits($credit, $description, $mode = null, $ref = null, $client_slug = null)
    {
        //save record
        $record = new Credit();
        if (!$client_slug)
            $record->client_slug = client('slug');
        else
            $record->client_slug = $client_slug;

        $record->user_id = (user()) ? user('id') : 1;
        $record->slug = Str::random(5);
        $record->payment = 'Manual';
        if ($mode)
            $record->mode = $mode;
        else
            $record->mode = 'client';
        $record->type = 'debit';
        $record->credit = '-' . $credit;
        $record->description = $description;
        if ($ref)
            $record->reference = $ref;
        else
            $record->reference = "";

        $record->save();
        //forget cache
        $record->refreshCache();
    }
    /**
     * Get the balance credits
     */
    public static function balance($client_slug = null)
    {
        if ($client_slug == 'super') {
            $balance = Supercredit::sum('credit');
        } else if ($client_slug) {
            $balance = Credit::where('client_slug', $client_slug)->sum('credit');
        } else {
            $balance = Supercredit::sum('credit');
        }

        return $balance;
    }

    /**
     * Refresh the items in cache
     */
    public function refreshCache()
    {
        // update the client list
        $clientlist = Cache::get('clientlist');
        if ($clientlist)
            foreach ($clientlist as $client_slug => $client) {
                Cache::forget('clientcredit_balance_' . $client_slug);
                Cache::forever('clientcredit_balance_' . $client_slug, $this->balance($client_slug));
            }

        Cache::forget('creditbalance_' . $this->client_slug);
        // update cache for credit & supercredit
        Cache::forget('supercredit_balance');
        Cache::forever('supercredit_balance', $this->balance());
        $rhelper = new RootHelper();
        $credit_balance = $rhelper->getCreditBalance();
    }
}