<?php

namespace App\Http\Controllers\Hire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

// load the model
use App\Models\Hire\Applicant as Model;
use App\Models\Hire\Job;
use App\Models\User;
use App\Models\Community\College;

class ApplicantController extends Controller
{
    // define app and module and view folder
    public $app = 'hire';
    public $jobapp = 'job';
    public $module = 'applicant';
    public $jobslug = null;
    public $viewFolder = 'apps.hire.applicant.';

    // define other class variables
    public $record = null; // one record from db
    public $records = []; // for multiple records from db
    public $count = []; // to store count of records based on status
    public $alert = null;
    public $errors = null;

    /**
     * load the defaults into the current object
     */
    public function __construct()
    {
        // load the alert if any
        $this->alert = session('alert');
        // load the errors if any
        $this->errors = session('errors');
        // add testslug
        $this->jobslug = request()->segment(3);
    }

    /**
     * Display the specified resource.
     */
    public function index(Request $request)
    {
        $model = new Model();
        $job = Job::where('slug', $this->jobslug)->first();

        // defining status and get their counts
        $allCount = Model::where('job_id', $job->id)->count();
        $pendingCount = Model::where('job_id', $job->id)->where('status', 'pending')->count();
        $shortlistedCount = Model::where('job_id', $job->id)->where('status', 'shortlisted')->count();
        $notShortlistedCount = Model::where('job_id', $job->id)->where('status', 'not shortlisted')->count();

        // Get distinct access code values and their counts
        $accessCodeCounts = Model::select(
            DB::raw('UPPER(JSON_UNQUOTE(JSON_EXTRACT(data, "$.accesscode"))) as accesscode'),
            DB::raw('COUNT(*) as count')
        )
            ->where('job_id', $job->id)
            ->whereNotNull('data->accesscode')
            ->groupBy(DB::raw('UPPER(JSON_UNQUOTE(JSON_EXTRACT(data, "$.accesscode")))'))
            ->get()
            ->pluck('count', 'accesscode');

        // Use the getRecords method with filters
        $this->records = $model->getRecords($job->id, $request->query('status'), $request->query('code'));
        $this->count = $model->getCount();

        foreach ($this->records as $k => $record) {
            $this->records[$k]->data = json_decode($record->data);
        }

        // return the view
        return view($this->viewFolder . 'show', compact('allCount', 'pendingCount', 'shortlistedCount', 'notShortlistedCount', 'accessCodeCounts'))
            ->with('data', $this);
    }

    /* View single record*/

    public function show(string $jobslug, string $slug)
    {
        $model = new Model();
        $job = Job::where('slug', $jobslug)->first();

        // for searchbar
        $this->record = $model->getRecord($job->id, $slug);

        return view($this->viewFolder . 'showRecord')
            ->with('record', $this->record)
            ->with('data', $this);
    }

    public function analytics(Request $request)
    {
        $job = Job::where('slug', $this->jobslug)->first();

        // Find job data by job_id
        $collegeCounts = DB::table('job_user')
            ->select(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.details.college")) as college'), DB::raw('COUNT(*) as count'))
            ->where('job_id', $job->id)
            ->groupBy('college')
            ->orderBy('count', 'desc')
            ->paginate(20);

        //update college if not present
        if (request()->get('college_update')) {
            $data = Model::where('job_id', $job->id)
                ->get();
            foreach ($data as $d) {
                $item = json_decode($d->data);
                if (!$item)
                    dd($d);
                $user = User::where('phone', $item->details->phone)->first();
                if (!isset($item->details->college)) {
                    $item->details->college = $user->c1;
                    unset($item->questions);
                    $d->data = json_encode($item);
                    $e = DB::statement("update job_user set data='" . $d->data . "' where job_id=" . $jobId . " and  user_id=" . $user->id);
                }
            }
            dd('done');
        }

        $totalApplicants = DB::table('job_user')
            ->where('job_id', $job->id)
            ->count();

        $genderCounts = DB::table('job_user')
            ->select(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.details.gender")) as gender'), DB::raw('COUNT(*) as count'))
            ->where('job_id', $job->id)
            ->groupBy('gender')
            ->get();

        // Initialize counts
        $maleCount = 0;
        $femaleCount = 0;

        // Update counts based on gender
        foreach ($genderCounts as $genderCount) {
            if ($genderCount->gender == 'male') {
                $maleCount = $genderCount->count;
            } elseif ($genderCount->gender == 'female') {
                $femaleCount = $genderCount->count;
            }
        }

        // Calculate total count
        $totalCount = $maleCount + $femaleCount;

        // Calculate percentage for progress bars
        $malePercentage = ($totalCount > 0) ? ($maleCount / $totalCount) * 100 : 0;
        $femalePercentage = ($totalCount > 0) ? ($femaleCount / $totalCount) * 100 : 0;

        // Count of applicants for each branch
        $branchCounts = DB::table('job_user')
            ->select(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.details.branch")) as branch'), DB::raw('COUNT(*) as count'))
            ->where('job_id', $job->id)
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.details.branch")) != "-"')
            ->groupBy('branch')
            ->get();

        // Initialize branch counts
        $branchData = [];
        foreach ($branchCounts as $branchCount) {
            $branchData[$branchCount->branch] = $branchCount->count;
        }
        // Decode the JSON string in the "settings" attribute to an associative array
        $settings = json_decode($job->settings, true);

        // Access the "deadline" value from the decoded array
        $deadline = $settings['deadline'];

        // zone wise applicant count
        $jobTitle = $job->title;
        $jobId = $job->id;

        // Get all records from job_user table where job_id equals the value we got from the job title
        $jobUserRecords = Model::where('job_id', $jobId)->get();

        $zoneCounts = [];

        // Loop through each job_user record
        foreach ($jobUserRecords as $record) {
            // Decode the 'data' column to extract college name
            $data = json_decode($record->data, true);
            $collegeName = $data['details']['college'] ?? null;

            // If college name exists, find respective 'zone_code' in the 'colleges' table
            if ($collegeName) {
                $college = College::where('name', $collegeName)->first();
                if ($college) {
                    $zoneCode = $college->zone_code;

                    // Increment the count for the respective zone_code
                    if (isset($zoneCounts[$zoneCode])) {
                        $zoneCounts[$zoneCode]++;
                    } else {
                        $zoneCounts[$zoneCode] = 1;
                    }
                }
            }
        }

        // Prepare the data to pass to the view
        $analyticsData = collect($zoneCounts)->map(function ($count, $zoneCode) {
            return ['zone_code' => $zoneCode, 'count' => $count];
        });

        // Pass branch counts to the view
        return view($this->viewFolder . 'analytics', compact('analyticsData'), [
            'collegeCounts' => $collegeCounts,
            'data' => $this,
            'jobId' => $job->id,
            'job' => $job,
            'deadline' => $deadline,
            'maleCount' => $maleCount,
            'femaleCount' => $femaleCount,
            'branchData' => $branchData,
            'totalApplicants' => $totalApplicants,
            'malePercentage' => $malePercentage,
            'femalePercentage' => $femalePercentage,
        ]);
    }

    /**
     * Download college students job wise
     */
    public function download($job_slug, $college)
    {
        $job = DB::table('jobs')->where('id', $job_slug)->first();

        if (!$job) {
            abort(404);
        }

        $title = $job->title;

        $filename = 'applicants_' . $college . '_' . $title . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        );

        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, [
            "SNO",
            "Name",
            "Phone",
            "Email",
            "College",
            "Branch",
            "YOP",
            "Applied Time"
        ]);

        // getting details 
        $applicants = DB::table('job_user')
            ->where('job_id', $job_slug)
            ->whereJsonContains('data->details->college', $college)
            ->get();

        $sno = 1; // Initialize serial number

        foreach ($applicants as $row) {
            $data = json_decode($row->data);

            if ($data) {

                // Access the name and email fields from the decoded JSON
                $name = $data->details->name;
                $email = $data->details->email;
                $phone = $data->details->phone;
                $college = $data->details->college;
                $branch = $data->details->branch;
                $year_of_passing = $data->details->year_of_passing;

                // Add a new row with data including the serial number
                fputcsv($handle, [
                    $sno, // Add Serial Number
                    $name,
                    $phone,
                    $email,
                    $college,
                    $branch,
                    $year_of_passing,
                    $row->applied_at
                ]);

                $sno++; // Increment the serial number
            }
        }

        rewind($handle);
        $csv = stream_get_contents($handle);

        // Close the file handle
        fclose($handle);

        return response($csv, 200, $headers);
    }

    /**
     * Download via access code
     */
    public function downloadByAccessCode($job_slug, $accessCode)
    {
        $job = DB::table('jobs')->where('id', $job_slug)->first();

        if (!$job) {
            abort(404);
        }

        $title = $job->title;

        $filename = 'applicants_' . $accessCode . '_' . $title . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        );

        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, [
            "SNO",
            "Name",
            "Phone",
            "Email",
            "College",
            "Branch",
            "YOP",
            "Applied Time"
        ]);

        // getting details 
        $applicants = DB::table('job_user')
            ->where('job_id', $job_slug)
            ->where('accesscode', $accessCode)
            ->get();

        $sno = 1; // Initialize serial number

        foreach ($applicants as $row) {
            $data = json_decode($row->data);

            if ($data) {

                // Access the name and email fields from the decoded JSON
                $name = $data->details->name;
                $email = $data->details->email;
                $phone = $data->details->phone;
                $college = $data->details->college;
                $branch = $data->details->branch;
                $year_of_passing = $data->details->year_of_passing;

                // Add a new row with data including the serial number
                fputcsv($handle, [
                    $sno, // Add Serial Number
                    $name,
                    $phone,
                    $email,
                    $college,
                    $branch,
                    $year_of_passing,
                    $row->applied_at
                ]);

                $sno++; // Increment the serial number
            }
        }

        rewind($handle);
        $csv = stream_get_contents($handle);

        // Close the file handle
        fclose($handle);

        return response($csv, 200, $headers);
    }

    /*Edit the Record*/
    public function edit(string $slug)
    {
        $user = Auth::user();

        $this->record = Model::where('slug', $slug)->first();
        $this->authorize('update', $this->record);

        return view($this->viewFolder . 'edit')
            ->with('editor', 1)
            ->with('data', $this)
            ->with('user', $user);
    }

    public function update(Request $request, string $id)
    {
        /* validation */
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()
                ->with('errors', $errors->messages())
                ->withInput();
        }

        $user = Auth::user();

        $model = Model::where('id', $id)->first();
        $alert = $model->updateData($id);

        $this->authorize('update', $model);

        return redirect()->route($this->module . '.show', $id)
            ->with('alert', $alert)
            ->with('user', $user);
    }

    /**
     * Delete a record
     */
    public function destroy(string $id)
    {
        // retrieve the resource
        $record = Model::where('id', $id)->first();
        // authorize the request
        $this->authorize('delete', $record);

        // reconfirm the delete by matching the name
        if ($record->name != request()->get('name')) {
            $alert = 'Name [' . request()->get('name') . '] is incorrect! Retry with correct details.';
            return redirect()->route($this->module . '.show', $record->id)
                ->with('alert', $alert)
                ->withInput();
        }

        //delete resource
        $record->delete();
        // alert message
        $alert = 'Item(' . $record->name . ') successfully deleted!';
        //redirect to index
        return redirect()->route($this->module . '.index')
            ->with('alert', $alert);
    }

    public function export(Request $request, $jobSlug)
    {
        $job = Job::where('slug', $jobSlug)->first();

        if (!$job) {
            abort(404, 'Job not found');
        }

        $applicants = Model::where('job_id', $job->id)->get();

        $data = [];
        $questionKeys = []; // Array to hold all unique question keys

        foreach ($applicants as $applicant) {
            $jsonData = json_decode($applicant->data, true);
            $details = $jsonData['details'] ?? [];
            $questions = $jsonData['questions'] ?? [];
            $accessCode = $jsonData['accesscode'] ?? null;

            // Collect all unique question keys
            $questionKeys = array_merge($questionKeys, array_keys($questions));

            // Fetch zone_code from colleges table based on college name
            $collegeName = $details['college'] ?? null;
            $zoneCode = null; // Default value if no college name is provided
            if ($collegeName) {
                $college = College::where('name', $collegeName)->first();
                if ($college) {
                    $zoneCode = $college->zone_code;
                }
            }

            $rowData = [
                'Job Id' => $applicant->job_id,
                'Id' => $applicant->user_id,
                'Name' => $details['name'] ?? null,
                'Phone' => $details['phone'] ?? null,
                'Email' => $details['email'] ?? null,
                'College' => $details['college'] ?? null,
                'Branch' => $details['branch'] ?? null,
                'YOP' => $details['year_of_passing'] ?? null,
                'Gender' => $details['gender'] ?? null,
                'Current City' => $details['current_city'] ?? null,
                'District' => $details['district'] ?? null,
                'State' => $details['state'] ?? null,
                'Class 10 Percentage' => $details['class_10_percentage'] ?? null,
                'Class 12 Percentage' => $details['class_12_percentage'] ?? null,
                'Graduation Percentage' => $details['graduation_percentage'] ?? null,
                'Backlogs' => $details['backlogs'] ?? null,
                'Trained At' => $details['trained-at'] ?? null,
                'Access Code' => $accessCode,
                'Zone Code' => $zoneCode,
                'Applied At' => $applicant->applied_at,
            ];
            // Merge questions with rowData, ensuring empty fields are handled
            foreach ($questionKeys as $key) {
                // Check if the key exists in the response data
                $rowData[$key] = array_key_exists($key, $questions) ? $questions[$key] : null;
            }

            $data[] = $rowData;
        }

        // Remove duplicates from question keys
        $questionKeys = array_unique($questionKeys);

        // Create a CSV file with the data
        $csvFileName = 'applicants_' . $jobSlug . '_export.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        // Merge question keys with existing header
        $header = array_merge([
            'Job Id',
            'Id',
            'Name',
            'Phone',
            'Email',
            'College',
            'Branch',
            'YOP',
            'Gender',
            'Current City',
            'District',
            'State',
            'Class 10 Percentage',
            'Class 12 Percentage',
            'Graduation Percentage',
            'Backlogs',
            'Trained At',
            'Access Code',
            'Zone Code',
            'Applied At'
        ], $questionKeys);

        // Use Laravel's response helper to generate the CSV file
        return response()->stream(
            function () use ($data, $header) {
                $file = fopen('php://output', 'w');

                // Add CSV header
                fputcsv($file, $header);

                // Add data rows
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            },
            200,
            $headers
        );
    }

    public function downloadApplicants(College $college)
    {
        $applicants = Applicant::where('college_id', $college->id)->get();

        return Excel::download(new ApplicantsExport($applicants), 'applicants.csv');
    }

    public function updateJsonData()
    {
        $jobUsers = Model::all();

        foreach ($jobUsers as $jobUser) {
            $userData = json_decode($jobUser->data, true); // Decode JSON to array

            // Find the user in the users table by phone
            $user = User::where('phone', $userData['details']['phone'])->first();

            if ($user) {
                // Add the college property to the JSON data
                $userData['details']['college'] = $user->c1;

                // Encode the updated data back to JSON
                $updatedData = json_encode($userData);

                // Update the data column in the job_user table
                $jobUser->data = $updatedData;
                $jobUser->save();
            }
        }
    }

    /**
     * Count of colleges
     */

    public function colleges($slug)
    {
        // Retrieve the job ID based on the provided slug
        $job = Job::where('slug', $slug)->first();

        if (!$job) {
            abort(404); // Handle job not found
        }

        $jobId = $job->id;

        // Count the records in job_user with the matching job_id and group by college
        $collegeCounts = Model::where('job_id', $jobId)
            ->selectRaw('count(*) as count, JSON_UNQUOTE(JSON_EXTRACT(data, "$.details.college")) as college')
            ->groupBy('college')
            ->get();

        return view($this->viewFolder . 'colleges', compact('collegeCounts'))->with('data', $this);
    }

    // public function search(Request $request, Job $job)
    // {
    //     $accessCode = $request->input('access_code');
    //     $jobs = [];
    //     $jobUsers = [];
    //     dd($accessCode);

    //     if ($accessCode) {
    //         // Find jobs with the provided access code
    //         $jobs = Job::where('access_codes', 'like', "%$accessCode%")->get();

    //         // Find job users related to the current job
    //         $jobUsers = Model::where('job_id', $job->id)->where('accesscode', $accessCode)->get();
    //     }

    //     return view($this->viewFolder.'applicants', [
    //         'jobs' => $jobs,
    //         'users' => $jobUsers,
    //         'accessCode' => $accessCode,
    //     ]);
    // }

    public function showApplicants($slug)
    {
        $job = Job::where('slug', $slug)->firstOrFail();
        $settings = json_decode($job['settings'], true);

        // Access the 'deadline' field within the decoded settings array
        $deadline = $settings['deadline'];
        // Pass $job and $deadline to the view
        return view($this->viewFolder . 'applicants', compact('job', 'deadline', 'slug'));
    }

    public function search(Request $request, $slug)
    {
        $accessCode = strtolower($request->input('code')); // Convert $accessCode to lowercase

        // Find job first using job slug
        $job = Job::where('slug', $slug)->first();

        // Retrieve results for the specified job_id
        $results = Model::where('job_id', $job->id)->get();

        // Filter records with the given accesscode from $results
        $filteredResults = $results->filter(function ($item) use ($accessCode) {
            return strtolower($item->accesscode) === $accessCode;
        });

        // Decode the JSON data in the 'data' attribute for each item in $filteredResults
        $dataArray = $filteredResults->map(function ($item) {
            return json_decode($item->data, true);
        });

        // job post details
        $job = Job::where('slug', $slug)->firstOrFail();
        $settings = json_decode($job['settings'], true);

        // Access the 'deadline' field within the decoded settings array
        $deadline = $settings['deadline'];
        // dd($filteredResults->getAttributes());
        return view($this->viewFolder . 'applicants', compact('filteredResults', 'slug', 'dataArray', 'job', 'deadline', 'results', 'accessCode'));
    }
}
