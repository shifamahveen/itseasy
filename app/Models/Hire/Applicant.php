<?php

namespace App\Models\Hire;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class Applicant extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_user';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship with the 'jobs' table
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }

    public function getRecords($jobId, $status = null, $code = null)
    {
        // $client_slug = request()->get('client.slug');
        $role = user('role');
        $search = request()->get('search');

        if ($search) {
            $column = request()->get('column');

            return Applicant::where('data', 'LIKE', "%" . $search . "%")->where('job_id', $jobId)->orderBy('applied_at', 'desc')->paginate(10);
        }

        $query = $this->newQuery()->where('job_id', $jobId)->orderBy('applied_at', 'desc');
        $perPage = 20;

        // Filter by status
        if ($status) {
            if ($role == 'superadmin' || $role == 'rootadmin')
                $query->where('status', $status);
        }

        // Filter by access code
        if ($code) {
            $query->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.accesscode")) = ?', [$code]);
        }

        return $query->paginate($perPage);
    }

    // college search
    public function getCollege()
    {
        $client_slug = request()->get('client.slug');
        $role = user('role');
        $search = request()->get('search');
        $client_slug = 'super';

        if ($search) {
            $column = request()->get('column');

            if ($role == 'superadmin' || $role == 'rootadmin')
                return User::where($column, 'LIKE', "%" . $search . "%")->orderBy('id', 'desc')->paginate(15);
        }
    }

    // public function getCount()
    public function getCount()
    {
        $client_slug = request()->get('client.slug');
        $role = user('role');
        $filter = request()->get('filter');
        $count = [
            '2024' => 0,
            "2023" => 0,
            "2022" => 0,
            "2021" => 0,
            "2020" => 0,
            "all" => 0
        ];

        if ($role == 'superadmin' || $role == 'rootadmin')
            $data =  Applicant::select('data->year_of_passing as field')->get()->groupBy('field');
        else
            $data = Applicant::select('data->year_of_passing as field')->where('data', $filter)->get()->groupBy('field');


        $data = $data->toArray();
        if (count($data)) {
            $keys = array_keys($data);
            // dd($keys);
            foreach ($keys as $key) {
                $count[$key] = count($data[$key]);
                $count['all'] += $count[$key];
            }
        }

        return $count;
    }

    /**
     * Load the record
     */
    public static function getRecord($id, $slug)
    {
        $record = Self::where('job_id', $id)->where('data', 'LIKE', "%" . $slug . "%")->first();
        if ($record) {
            $record->data = json_decode($record->data);
            return $record;
        } else
            return null;
    }
    protected $fillable = ['data'];

    public static function getCountByYearOfPassing($year)
    {
        return self::whereRaw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.details.year_of_passing")) = ?', [$year])
            ->count();
    }


    public static function getCountsByYear()
    {
        $yearCounts = [];

        for ($year = 2020; $year <= 2027; $year++) {
            $counts = self::getCountByYearOfPassing($year);
            $yearCounts[$year] = $counts;
        }

        return $yearCounts;
    }


    public static function getBranchCounts()
    {
        return self::select(
            DB::raw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.details.branch")) AS branch'),
            DB::raw('COUNT(*) AS count_of_records')
        )
            ->groupBy('branch')
            ->get();
    }
}
