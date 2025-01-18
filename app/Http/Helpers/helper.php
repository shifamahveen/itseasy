<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Models\Core\Client;


// import models
use App\Models\Community\College as Model;

if (!function_exists('user')) {
    function user($key = null)
    {
        $sess_data = session()->all();
        $id = null;
        foreach ($sess_data as $k => $v) {
            if (strpos($k, 'login_web_') === 0) {
                $id = $v;
            }
        }
        $keys  = ['id', 'name', 'email', 'role', 'status', 'email_verified_at', 'slug', 'phone'];
        if ($id)
            if ($key)
                if (in_array($key, $keys))
                    return $sess_data['user_' . $id][0][$key];
                else
                    return null;
            else
                return $sess_data['user_' . $id][0];

        else
            return null;
    }
}
if (!function_exists('phoneWithoutClientID')) {
    function phoneWithoutClientID($phone)
    {
        if (strpos($phone, '-') !== false) {
            $pieces = explode('-', $phone);
            return $pieces[1];
        } else
            return $phone;
    }
}

if (!function_exists('theme')) {
    function theme($key)
    {
        if (request()->get($key))
            return request()->get($key);
        else
            return null;
    }
}

if (!function_exists("cities")) {
    function cities()
    {
        $cities = Cache::get('cities');
        if (request()->get('refresh')) {
            cache::forget('cities');
        }
        if (!$cities) {
            $data = file_get_contents('cities.json');
            $data = str_replace("\"", "", $data);
            $data = str_replace("\n", "", $data);
            $cities = explode(",", $data);
            Cache::forever('cities', $cities);
        }

        return $cities;
    }
}

if (!function_exists("districts")) {
    function districts()
    {
        $districts = Cache::get('districts');
        if (request()->get('refresh')) {
            cache::forget('districts');
        }
        if (!$districts) {
            $data = json_decode(file_get_contents('districts.json'), true);
            $states = $districts = [];
            foreach ($data as $a => $b) {
                foreach ($b as $m => $n) {
                    foreach ($n as $p => $q)
                        if ($p == 'state') {
                            array_push($states, $q);
                        } else {
                            foreach ($q as $r => $s)
                                array_push($districts, $s);
                        }
                }
            }
            sort($districts);
            Cache::forever('districts', $districts);
        }

        return $districts;
    }
}

if (!function_exists("states")) {
    function states()
    {
        $states = Cache::get('states');
        if (request()->get('refresh')) {
            cache::forget('states');
        }
        if (!$states) {
            $data = json_decode(file_get_contents('districts.json'), true);
            $states = $districts = [];
            foreach ($data as $a => $b) {
                foreach ($b as $m => $n) {
                    foreach ($n as $p => $q)
                        if ($p == 'state') {
                            array_push($states, $q);
                        } else {
                            foreach ($q as $r => $s)
                                array_push($districts, $s);
                        }
                }
            }
            sort($states);
            cache::forever('states', $states);
        }
        return $states;
    }
}

if (!function_exists("colleges")) {
    function colleges()
    {
        $data = Cache::get('colleges');
        $colleges = $college_zones = $college_type = [];
        if (request()->get('refresh')) {
            cache::forget('colleges');
            cache::forget('college_zones');
            cache::forget('college_type');
        }
        if (!$data) {
            $data = Model::get();
            foreach ($data as $k) {
                array_push($colleges, $k->name);
                $college_zones[$k->name] = $k->zone_code;
                $college_type[$k->name] = $k->type;
            }
            sort($colleges);
            cache::forever('colleges', $colleges);
            cache::forever('college_zones', $college_zones);
            cache::forever('college_type', $college_type);
            if (request()->get('d')) {
                dd($colleges);
            }
            return $colleges;
        }
        return $data;
    }
}

if (!function_exists("college_zones")) {
    function college_zone($college)
    {
        $zones = Cache::get('college_zones');
        if (isset($zones[$college]))
            return $zones[$college];
        else
            return null;
    }
}

if (!function_exists("college_type")) {
    function college_type($college)
    {
        $types = Cache::get('college_type');
        if (isset($types[$college]))
            return $types[$college];
        else
            return null;
    }
}
if (!function_exists('testTypeCredits')) {
    function testTypeCredits($key = null)
    {
        $credits = 0;
        if ($key == 'basic') {
            $credits = 0;
        }
        if ($key == 'pro') {
            $credits = 60;
        }
        if ($key == 'advance') {
            $credits = 100;
        }
        if ($key == 'basic-plus') {
            $credits = 40;
        }
        if ($key == 'pro-plus') {
            $credits = 100;
        }
        if ($key == 'advance-plus') {
            $credits = 120;
        }
        return $credits;
    }
}
if (!function_exists('testTypeCreditsAddon')) {
    function testTypeCreditsAddon($test)
    {
        if (is_object($test->settings))
            $settings = $test->settings;
        else
            $settings = json_decode($test->settings);

        $credits = 0;
        if (isset($settings->random_qb) && $settings->random_qb) {
            $credits += 5;
        }
        if (isset($settings->email_invite) && $settings->email_invite) {
            $credits += 5;
        }
        if (isset($settings->video_questions) && $settings->video_questions) {
            $credits += 10;
        }
        if (isset($settings->ai_feedback) && $settings->ai_feedback) {
            $credits += 10;
        }
        if (isset($settings->video_snaps) && $settings->video_snaps) {
            $credits += 20;
        }
        if (isset($settings->face_detection) && $settings->face_detection) {
            $credits += 20;
        }
        return $credits;
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin($key = null)
    {
        $sess_data = session()->all();
        $id = null;
        foreach ($sess_data as $k => $v) {
            if (strpos($k, 'login_web_') === 0) {
                $id = $v;
            }
        }
        $adminroles = ['superadmin', 'rootadmin', 'clientadmin', 'clientmoderator', 'clientviewer'];

        if ($id)
            if ($key) {
                if ($sess_data['user_' . $id][0]['role'] == $key)
                    return true;
                else
                    return null;
            } else {
                if (in_array($sess_data['user_' . $id][0]['role'], $adminroles))
                    return true;
                else
                    return null;
            }

        else
            return null;
    }
}



if (!function_exists('client')) {
    function client($key,$slug=null)
    {
        if($slug){
            $client = Client::where('slug',$slug)->first();
        }else{
            // get the domain name
            $domain = request()->getHttpHost();
            $client = Cache::get('client_' . $domain);
        }
        
        $settings = json_decode($client->settings);
        $value = null;
        if ($key == 'slug')
            return $client->slug;
        if ($key == 'name')
            return $client->name;
        if ($key == 'id')
            return $client->id;

        //check if the settings json has the direct key and value pair
        if (isset($settings->$key))
            $value = $settings->$key;
        elseif (!isset($settings->layout) && $key == 'layout') {
            $value = 'default';
        } else {
            $value = null;
        }

        return $value;
    }
}

if (!function_exists('isHTML')) {
    function isHTML($string)
    {
        return $string !== strip_tags($string) ? true : false;
    }
}
if (!function_exists('processSettings')) {
    function processSettings()
    {
        $data = request()->all();
        $items = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'settings_') === 0) {
                $k = str_replace("settings_", "", $key);
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $items[$k] = $value;
            }
        }

        return json_encode($items);
    }
}

if (!function_exists('getSettings')) {
    function getSettings($settingsUrl)
    {
        //load settings from cloud
        $settings = Storage::get($settingsUrl);
        // if settings are null then use the default
        if (!$settings || $settings == "null") {
            $settings = file_get_contents('default_settings.json');
            $settings = json_encode(json_decode($settings), JSON_PRETTY_PRINT);
        }
        return $settings;
    }
}

if (!function_exists('saveSettings')) {
    function saveSettings($settingsUrl)
    {
        // store it in cloud s3
        $data = json_encode(json_decode(request()->get('settings')), JSON_PRETTY_PRINT);
        Storage::put($settingsUrl, $data);
    }
}

if (!function_exists('loadCache')) {
    function loadCache($key)
    {
        return Cache::get($key);
    }
}

if (!function_exists('refreshCache')) {
    function refreshCache($key, $value)
    {
        Cache::forget($key);
        Cache::put($key, $value, 3600);
    }
}

if (!function_exists('resize_png')) {
    // Image = image retrieved from request object directly
    // Size = size in px
    // Filename = filename with extension
    function resize_png($image, $size, $fpath)
    {
        // png version of the image
        $img_png = Image::make($image)->encode('png');
        $img_png->resize($size, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img_png = $img_png->stream();
        Storage::put($fpath, $img_png->__toString(), 'public');
    }
}

if (!function_exists('isYoutubeUrl')) {
    function isYoutubeUrl($url)
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        if (isset($match[1]))
            return $match[1];
        else {
            $url = str_replace('https://www.youtube.com/shorts', '', $url);
            if (strlen($url) < 12)
                return $url;
            else
                return false;
        }
    }
}


if (!function_exists('getYoutubeEmbed')) {
    function getYoutubeEmbed($url)
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        if (isset($match[1])) {
            $html = '<div class="flex flex-col items-center w-full mx-auto ">
                <div class="relative w-full h-0 max-w-full overflow-hidden" style="padding-bottom: 56.25%">
                ';
            $iframe = $html . '<iframe src="https://youtube.com/embed/' . $match[1] . '?si=IDfAQYnXDSzHS9Og" class="absolute top-0 left-0 w-full h-full" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
            $html = $iframe . '</div></div>';
            return $html;
        } else {
            $url = str_replace('https://www.youtube.com/shorts/', '', $url);
            if (strlen($url) < 12) {
                return  '<iframe src="https://youtube.com/embed/' . $url . '?si=IDfAQYnXDSzHS9Og" width="285" height="506"  title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
            } else
                return false;
        }
    }
}

if (!function_exists('editorImageUpload')) {
    function editorImageUpload($editor_data)
    {
        $detail = $editor_data;
        if ($detail) {
            $dom = new \DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHtml(mb_convert_encoding($detail, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $images = $dom->getElementsByTagName('img');
            $data = null;
            //process images
            foreach ($images as $k => $img) {
                $data = $img->getAttribute('src');
                if (strpos($data, ';')) {
                    list(, $data) = explode(';', $data);
                    list(, $data) = explode(',', $data);
                    $data = trim(base64_decode($data));
                    $base_folder = '';
                    $image_name =  time() . '_' . $k . '_' . rand() . '.png';
                    $temp_path = storage_path() . $base_folder . 'temp_' . $image_name;
                    file_put_contents($temp_path, $data);
                    //resize
                    $imgr = Image::make($temp_path);
                    $imgr->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img_png = $imgr->stream();
                    $fpath = 'editor/images/' . $image_name;
                    Storage::put($fpath, $img_png->__toString(), 'public');
                    $url = Storage::url($fpath);
                    unlink(trim($temp_path));

                    $img->removeAttribute('src');
                    $img->setAttribute('src', $url);
                    $img->setAttribute('class', 'image py-2');
                }
            }
            //process tables
            $tables = $dom->getElementsByTagName('table');
            foreach ($tables as $k => $table) {
                $table->setAttribute('class', 'min-w-full my-2 text-left');
            }

            $trs = $dom->getElementsByTagName('tr');
            foreach ($trs as $k => $tr) {
                $tr->setAttribute('class', 'border');
            }
            $tds = $dom->getElementsByTagName('td');
            foreach ($tds as $k => $td) {
                $td->setAttribute('class', 'px-4 py-2 border');
            }

            //process html tags
            $uls = $dom->getElementsByTagName('ul');
            foreach ($uls as $k => $ul) {
                $ul->setAttribute('class', 'list-disc pl-3 ml-1');
            }

            $ols = $dom->getElementsByTagName('blockquote');
            foreach ($ols as $k => $ol) {
                $ol->setAttribute('class', 'border-l-2 border-yellow-500 bg-yellow-100 p-3 my-2');
            }

            $detail = $dom->saveHTML();
        }
        return $detail;
    }
}
if (!function_exists('curl')) {
    function curl($url)
    {
        //curl
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "X-Custom-Header: header-value",
            "Content-Type: application/json"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
if (!function_exists('editorImageRemove')) {
    function editorImageRemove($editor_data)
    {
        $detail = $editor_data;
        if ($detail) {
            $dom = new \DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHtml(mb_convert_encoding($detail, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $images = $dom->getElementsByTagName('img');

            foreach ($images as $img) {
                $data = $img->getAttribute('src');
                $imgr = parse_url($data);
                Storage::delete(ltrim($imgr['path'], '/' . config('services.app_slug')));
            }
            $detail = $dom->saveHTML();
        }
        return $detail;
    }
}

if (!function_exists('generateSlug')) {
    function generateSlug()
    {
        return strtolower(substr(md5(rand()), 0, 10));
    }
}