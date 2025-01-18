<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;

class Client extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->belongsToMany('App\Models\Collection\Product');
    }

    /**
     * Get the items from database
     */
    public static function getItems()
    {
        $search = request()->get('search');
        $data = Client::where('name', 'LIKE', '%' . $search . '%')
            ->whereNotIn('slug', ['super', 'deploy1', 'deploy2'])
            ->orderBy('id', 'desc')->paginate(30);
        foreach ($data as $k => $d) {
            $data[$k]->credit_balance = Cache::get('clientcredit_balance_' . $d->slug);
        }
        return $data;
    }

    /**
     * Refresh the items in cache
     */
    public function refreshCache()
    {
        //update logo path
        if (Storage::exists($this->slug . '/images/logo/logo.png'))
            $this->logo = Storage::url($this->slug . '/images/logo/logo.png');
        else
            $this->logo = null;


        Cache::forget('client_' . $this->domain);
        // update cache for one object
        Cache::forever('client_' . $this->domain, $this);


        //dd(Cache::get('client_' . $this->domain));
        //update all items of clients
        $clientlist = Client::select('name', 'slug', 'status')->whereNotIn('slug', ['super', 'deploy1', 'deploy2'])->get()->keyBy('slug')->toArray();
        Cache::forever('clientlist', $clientlist);
    }

    /**
     * For every client, we generate accounts for
     * clientadmin, clientmoderator, clientviewer, user
     */
    public function defaultLogins($slug)
    {

        $roles = ['clientadmin', 'clientmoderator', 'clientviewer', 'user'];

        foreach ($roles as $r) {
            $user = new User();
            $user->name = ucfirst($slug) . " " . $r;
            $user->email = $r . "@" . $slug . ".dummy.com";
            $user->phone = rand(2222222222, 3333333333);
            $user->password = $user->phone;
            $user->email_verified_at = now();
            $user->slug = Str::random(6);
            $user->client_slug = $slug;
            $user->role = $r;
            $user->save();

            $user->attachDefaultProducts();
        }
    }
}
