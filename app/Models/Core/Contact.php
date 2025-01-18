<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use carbon\Carbon;

class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'message',
        'category',
        'tags',
        'comment',
        'client_id',
        'agency_id',
        'user_id',
        'valid_email',
        'json',
        'status',
    ];


    /**
     * Get the list of records from database
     *
     * @var array
     */
    public function getRecords($item, $limit, $user, $status)
    {

        /**
         *  filter data 
         * */

        //load the filters in array format
        $user_array = $this->filter_array('user_id', $user);
        $category_array = $this->filter_array('category', $user);
        $date_range = $this->filter_array('date_filter', $user);
        $status_array = $this->filter_array('status', $user);

        //load user_id if any
        $user_id = request()->get('user_id');

        //tag filter
        if (request()->get('tag')) {
            $field = 'tags';
            $item = request()->get('tag');
        }

        // if the filter is numeric check phone filed
        // else email or name
        if (is_numeric($item)) {
            $field = 'phone';
        } else if (strpos($item, '@') !== false) {
            $field = 'email';
        } else {
            $field = 'name';
        }


        // for open leads we do not send and user array, as the users are not assigned
        if ($status == 1 || !$user_id) {
            return $this->where($field, 'LIKE', "%{$item}%")
                ->whereIn('status', $status_array)
                ->whereIn('category', $category_array)
                ->where('client_id', $user->client_id)
                ->whereBetween('updated_at', $date_range)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        } else {
            return $this->where($field, 'LIKE', "%{$item}%")
                ->whereIn('status', $status_array)
                ->whereIn('category', $category_array)
                ->where('client_id', $user->client_id)
                ->whereIn('user_id', $user_array)
                ->whereBetween('updated_at', $date_range)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        }
    }




    /**
     * Load the filter tags
     * 
     */
    public function filter_array($key, $user)
    {
        /**
         *  filter data 
         * */

        //user filter
        if ($key == 'user_id') {
            $user_id = request()->get('user_id');
            $user_array = [];
            if ($user_id) {
                $user_array = [$user_id];
            } else {
                $users = \Auth::user()->whereIn('role', ['clientadmin', 'clientdeveloper', 'clientmanager', 'clientmoderator'])->where('client_slug', $user->client_id)->get();
                foreach ($users as $u) {
                    if ($u)
                        array_push($user_array, $u->id);
                }
            }
            return $user_array;
        }


        //category filter
        if ($key == 'category') {
            $category_array = [];
            if (request()->get('category')) {
                $category_array = [request()->get('category')];
            } else {
                $categories =  $this->select(['category'])
                    ->where('client_id', $user->client_id)
                    ->distinct()
                    ->get();
                foreach ($categories as $c) {
                    array_push($category_array, $c->category);
                }
            }
            return $category_array;
        }


        // status filter
        if ($key == 'status') {
            $status = request()->get('status');
            if ($status) {
                $status_array = [$status];
            } else if ($status === "0") { // attach explicitly '0' 
                $status_array = [$status];
            } else {
                $status_array = ['0', '1', '2', '3', '4', '5'];
            }
            return $status_array;
        }

        //date range filter
        if ($key == 'date_filter') {
            $settings = json_decode($this->settings);
            if (request()->get('date_filter')) {
                $date_filter = request()->get('date_filter');
            } else if (isset($settings->date_filter)) {
                $date_filter = $settings->date_filter;
            } else {
                $date_filter = 'thisyear';
            }
            $date_range = $this->date_filter($date_filter);
            return $date_range;
        }

        // return empty array if nothing matches
        return [];
    }

    /**
     * Get the list of records from database
     *
     * @var array
     */
    public function getData($item, $limit, $user, $status)
    {

        // load userid and client id
        $user_id = request()->get('user_id');
        $client_id = $user->client_id;

        /**
         *  filter data 
         * */

        //load the filters in array format
        $user_array = $this->filter_array('user_id', $user);
        $category_array = $this->filter_array('category', $user);
        $date_range = $this->filter_array('date_filter', $user);
        $status_array = $this->filter_array('status', $user);


        //check for tags
        $field = 'name';
        if (request()->get('tag')) {
            $field = 'tags';
            $item = request()->get('tag');
        }


        if ($status == 1) {
            // for open leads we do not send and user array, as the users are not assigned
            $records = $this->where($field, 'LIKE', "%{$item}%")
                ->whereIn('status', $status_array)
                ->whereIn('category', $category_array)
                ->where('client_id', $user->client_id)
                ->whereBetween('updated_at', $date_range)
                ->with('user')
                ->orderBy('updated_at', 'desc')
                ->get();
        } else {
            if (!$user_id) {
                $records = $this->where($field, 'LIKE', "%{$item}%")
                    ->whereIn('status', $status_array)
                    ->whereIn('category', $category_array)
                    ->where('client_id', $user->client_id)
                    ->whereBetween('updated_at', $date_range)
                    ->with('user')
                    ->orderBy('updated_at', 'desc')
                    ->get();
            } else {
                $records = $this->where($field, 'LIKE', "%{$item}%")
                    ->whereIn('status', $status_array)
                    ->whereIn('category', $category_array)
                    ->where('client_id', $user->client_id)
                    ->whereIn('user_id', $user_array)
                    ->whereBetween('updated_at', $date_range)
                    ->with('user')
                    ->orderBy('updated_at', 'desc')
                    ->get();
            }
        }


        //export data if the request is invoked
        if (request()->get('export')) {
            //load form fields from settings json stored in cloud
            // based on the cateogry or default 
            if (Storage::disk('s3')->exists('settings/contact/' . $client_id . '.json')) {
                $data = json_decode(Storage::disk('s3')->get('settings/contact/' . $client_id . '.json'));

                if (request()->get('category'))
                    $category = request()->get('category');
                else
                    $category = 'contact';
                $field_name = $category . '_form';
                if (isset($data->$field_name)) {
                    $form = $this->processForm($data->$field_name);
                } else {
                    $field_name = 'contact_form';
                    $form = $this->processForm($data->$field_name);
                }
            }

            $client_users = User::where('client_id', $user->client_id)->whereIn('role', ['clientmoderator', 'clientdeveloper', 'clientmanager', 'clientadmin'])
                ->orderBy('updated_at', 'desc')
                ->get()->keyBy('id');


            //load data with grouping
            $full_data = $this->select(['id', 'user_id', 'phone', 'updated_at'])->where('client_id', $user->client_id)
                ->orderBy('updated_at', 'desc')
                ->get()->groupBy('phone');



            //default columns names
            $columnNames = ['sno', 'timestamp', 'name', 'email', 'phone', 'status', 'message', 'category', 'comment', 'valid_email'];
            $jsonNames = [];
            //load new form fileds as columns
            foreach ($form as $f) {
                array_push($columnNames, str_replace(' ', '_', $f['name']));
                array_push($jsonNames, str_replace(' ', '_', $f['name']));
            }
            array_push($columnNames, 'Interaction');
            array_push($columnNames, 'link');

            $rows = [];

            //Replace the status codes with names for clarity in excel download
            $status = ['0' => 'Customer', '1' => 'Open Lead', '2' => 'Cold Lead', '3' => 'Warm Lead', '4' => 'Prospect', '5' => 'Not Responded'];
            foreach ($records as $k => $r) {
                //load the data
                $row = [($k + 1), $r->updated_at, $r->name, $r->email, $r->phone, $status[$r->status], $r->message, $r->category, $r->comment, $r->valid_email];
                $data  = json_decode($r->json);
                //dd($data);
                foreach ($jsonNames as $f) {
                    $f1 = str_replace('.', '_', $f);
                    $f2 = str_replace('.', '', $f);
                    if (isset($data->$f)) {
                        array_push($row, $data->$f);
                    } elseif (isset($data->$f1)) {
                        array_push($row, $data->$f1);
                    } elseif (isset($data->$f2)) {
                        array_push($row, $data->$f2);
                    } else {
                        array_push($row, '-');
                    }
                }
                $link = route('Contact.show', $r->id);
                //check for previous interaction
                if (isset($full_data[$r->phone])) {
                    if (count($full_data[$r->phone]) > 1) {
                        $name = '';
                        foreach ($full_data[$r->phone] as $fr) {
                            if ($fr->user_id) {
                                if (isset($client_users[$fr->user_id]->name)) {
                                    if ($name != '')
                                        $name = $name . ', ' . $client_users[$fr->user_id]->name;
                                    else
                                        $name = $name . $client_users[$fr->user_id]->name;
                                }
                            }
                        }
                        array_push($row, $name);
                    } else {
                        array_push($row, '');
                    }
                }

                array_push($row, $link);
                array_push($rows, $row);
            }


            //name the excel sheet based on tag/category/status/datefilter/user name
            $name_suffix = '';
            if (request()->get('category'))
                $name_suffix = $name_suffix . '_' . request()->get('category');
            if (request()->get('tag'))
                $name_suffix = $name_suffix . '_' . request()->get('tag');
            if (request()->get('status')) {
                $status = ['0' => 'Customer', '1' => 'Open Lead', '2' => 'Cold Lead', '3' => 'Warm Lead', '4' => 'Prospect', '5' => 'Not Responded'];
                $name_suffix = $name_suffix . '_' . $status[request()->get('status')];
            }
            if (request()->get('date_filter'))
                $name_suffix = $name_suffix . '_' . request()->get('date_filter');
            if (request()->get('user_id')) {
                $username = User::where('id', request()->get('user_id'))->first()->name;
                $name_suffix = $name_suffix . '_' . $username;
            }



            return $this->getCsv($columnNames, $rows, 'data_' . request()->get('client.name') . '_' . strtotime("now") . $name_suffix . '.csv');
        }


        // load tags
        $settings = null;
        if (Storage::disk('s3')->exists('settings/contact/' . $client_id . '.json'))
            $settings = json_decode(Storage::disk('s3')->get('settings/contact/' . $client_id . '.json'));

        //load tags, category, overall data and users info
        $data['tags'] = $this->load_tag_data($settings, $user_array, $client_id, $date_range, $status_array, $category_array);
        $data['category'] = $records->groupBy('category');
        $data['overall'] = $records->groupBy('status');
        $data['users'] = $records->groupBy('user_id');


        //load the open lead data that is skipped because of bad logic
        if (!isset($data['overall'][1]) && !$user_id) {
            $data['overall'][1] = $this->select(['status', 'user_id'])->where($field, 'LIKE', "%{$item}%")
                ->where('client_id', $user->client_id)
                ->whereIn('status', ["1"])
                ->whereBetween('updated_at', $date_range)
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        //load empty array into data array
        for ($i = 0; $i < 6; $i++) {
            if (!isset($data['overall'][$i]))
                $data['overall'][$i] = [];
        }

        return $data;
    }

    public function date_filter($query)
    {
        $range = [date('2020-04-04'), Carbon::tomorrow()->toDateTimeString()];

        switch ($query) {
            case 'today':
                $range[0] = Carbon::today()->toDateTimeString();
                $range[1] = Carbon::tomorrow()->toDateTimeString();
                break;

            case 'yesterday':
                $range[0] = Carbon::yesterday()->toDateTimeString();
                $range[1] = Carbon::today()->toDateTimeString();
                break;
            case 'lastsevendays':
                $range[0] = Carbon::now()->subDays(7)->toDateTimeString();
                $range[1] = Carbon::tomorrow()->toDateTimeString();
                break;
            case 'lastfifteendays':
                $range[0] = Carbon::now()->subDays(15)->toDateTimeString();
                $range[1] = Carbon::tomorrow()->toDateTimeString();
                break;
            case 'thisweek':
                $range[0] = Carbon::now()->startOfWeek()->toDateTimeString();
                $range[1] = Carbon::tomorrow()->toDateTimeString();
                break;
            case 'lastweek':
                $range[0] = Carbon::now()->startOfWeek()->subDays(7)->toDateTimeString();
                $range[1] = Carbon::now()->startOfWeek()->toDateTimeString();
                break;
            case 'thismonth':
                $range[0] = Carbon::now()->startOfMonth()->toDateTimeString();
                $range[1] = Carbon::tomorrow()->toDateTimeString();
                break;
            case 'lastmonth':
                $range[0] = Carbon::now()->startOfMonth()->subMonth()->toDateTimeString();
                $range[1] = Carbon::now()->startOfMonth()->toDateTimeString();
                break;
            case 'thisyear':
                $range[0] = Carbon::now()->startOfYear()->toDateTimeString();
                $range[1] = Carbon::tomorrow()->toDateTimeString();
                break;
            case 'lastyear':
                $range[0] = Carbon::now()->startOfYear()->subYear()->toDateTimeString();
                $range[1] = Carbon::now()->startOfYear()->toDateTimeString();
                break;
            case 'generic':
                $range[0] = date(request()->get('from') . ' 00:00:00');
                $range[1] = date(request()->get('to') . ' 00:00:00');
                break;
            default:
                $range[0] = date('2020-04-01');
                $range[1] = Carbon::tomorrow()->toDateTimeString();
                break;
        }

        return $range;
    }

    public function load_tag_data($settings, $user_array, $client_id, $date_range, $status_array, $category_array)
    {
        $settings = $settings;

        $data = [];
        if (isset($settings->tags)) {
            $tags = explode(',', $settings->tags);

            foreach ($tags as $t) {
                $data[$t] = $this->select(['status', 'user_id'])->where('tags', 'LIKE', "%{$t}%")
                    ->where('client_id', $client_id)
                    ->whereIn('status', $status_array)
                    ->whereIn('category', $category_array)
                    ->whereBetween('updated_at', $date_range)
                    ->count();
            }
        }
        return $data;
    }




    public function getSettings()
    {
        $client_id = \Auth::user()->client_id;

        $settings = null;
        if (Storage::disk('s3')->exists('settings/contact/' . $client_id . '.json'))
            $settings = json_decode(Storage::disk('s3')->get('settings/contact/' . $client_id . '.json'));



        return json_decode($settings);
    }

    public function getSettingsTags()
    {
        $client_id = \Auth::user()->client_id;

        $settings = null;
        if (Storage::disk('s3')->exists('settings/contact/' . $client_id . '.json'))
            $settings = json_decode(Storage::disk('s3')->get('settings/contact/' . $client_id . '.json'));

        if (!isset($settings->tags))
            return null;
        return $settings->tags;
    }

    public function tags()
    {
        return explode(',', $this->tags);
    }


    public function processForm($data)
    {
        $d = [];
        $form = explode(',', $data);
        foreach ($form as $k => $f) {
            $item = ["name" => $f, "type" => "input", "values" => ""];
            if (preg_match_all('/<<+(.*?)>>/', $f, $regs)) {
                foreach ($regs[1] as $reg) {
                    $variable = trim($reg);
                    $item['name'] = str_replace($regs[0], '', $f);


                    if (is_numeric($variable)) {
                        $item['type'] = 'textarea';
                        $item['values'] = $variable;
                    } else if ($variable == 'file') {
                        $item['type'] = 'file';
                        $item['values'] = $variable;
                    } else {
                        $options = explode('/', $variable);
                        $item['values'] = $options;
                        $item['type'] = 'checkbox';
                    }
                }
            }

            if (preg_match_all('/{{+(.*?)}}/', $f, $regs)) {

                foreach ($regs[1] as $reg) {
                    $variable = trim($reg);
                    $item['name'] = str_replace($regs[0], '', $f);
                    $options = explode('/', $variable);
                    $item['values'] = $options;
                    $item['type'] = 'radio';
                }
            }

            $d[$k] = $item;
        }

        return $d;
    }


    public function uploadFile($file)
    {

        $client_id = request()->get('client.id');


        $fname = str_replace(' ', '', $file->getClientOriginalName());
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
            $type = 'files';
        else if (in_array($extension, ['pdf', '', 'doc', 'txt', 'docx', 'xls', 'xlsx']))
            $type = 'files';
        else
            $type = $extension;

        $filename = 'file_' . $fname;

        $path = 'https://' . request()->get('domain.name') . '/' . Storage::disk('public')->putFileAs('files/' . $client_id, $file, $filename, 'public');

        return [$path, $filename];
    }

    function debounce_valid_email($email)
    {
        $api = '6075b8772c316';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, 'https://api.debounce.io/v1/?api=' . $api . '&email=' . strtolower($email));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($data, true);

        if ($json['debounce']['code'] == 5)
            return 1;
        else
            return 0;
    }

    public static function variableReplace($content, $settings)
    {


        if (preg_match_all('/{{+(.*?)}}/', $content, $regs)) {
            foreach ($regs[1] as $reg) {
                $variable = trim($reg);


                $pos_0 = substr($variable, 0, 1);

                //varaibles in the current page settings
                if ($pos_0 == '$') {
                    $variable_name = str_replace('$', '', $variable);

                    $data = (isset($settings->$variable_name)) ? $settings->$variable_name : '';

                    $content = str_replace('{{' . $reg . '}}', $data, $content);
                }
            }
        }
        return $content;
    }


    public static function getCsv($columnNames, $rows, $fileName = 'file.csv')
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $fileName,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $callback = function () use ($columnNames, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columnNames);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }


    public function urlSuffix()
    {
        $r = request();
        $url = '';
        if ($r->get('status')) {
            $url = $url . '&status=' . $r->get('status');
        }
        if ($r->get('user_id')) {
            $url = $url . '&user_id=' . $r->get('user_id');
        }
        if ($r->get('category')) {
            $url = $url . '&category=' . $r->get('category');
        }
        if ($r->get('tag')) {
            $url = $url . '&tag=' . $r->get('tag');
        }
        if ($r->get('date_filter')) {
            $url = $url . '&date_filter=' . $r->get('date_filter');
        }

        return $url;
    }

    /**
     * Get the client that owns the page.
     *
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user that who contacted the person.
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}