<?php
namespace App\Http\Controllers\Hire;

use App\Models\Hire\Job;
use App\Models\Hire\JobUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class JobApplicationController extends Controller
{
    public $viewFolder = 'hire.';

    public function apply($jobId)
    {
        $job = Job::findOrFail($jobId);
        
        return view($this->viewFolder . 'apply', compact('job'));
    }

    public function applications($jobId)
    {
        $applications = JobUser::where('job_id', $jobId)->with('user')->get(); 
        $job = Job::findOrFail($jobId); 
        return view($this->viewFolder . 'applications', compact('applications', 'job'));
    }    

    public function storeApplication(Request $request, $jobId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'college' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'year_of_passing' => 'required|integer',
            'gender' => 'required|string|max:10',
            'current_city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'class_10_percentage' => 'nullable|numeric|min:0|max:100',
            'class_12_percentage' => 'nullable|numeric|min:0|max:100',
            'graduation_percentage' => 'nullable|numeric|min:0|max:100',
            'backlogs' => 'nullable|string|max:255',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $slug = Str::random(6);
        $path = $request->file('resume')->store('resumes');

        $data = [
            'details' => [
                'name' => $validated['name'],
                'slug' => $slug,
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'college' => $validated['college'],
                'branch' => $validated['branch'],
                'year_of_passing' => $validated['year_of_passing'],
                'gender' => $validated['gender'],
                'current_city' => $validated['current_city'],
                'state' => $validated['state'],
                'class_10_percentage' => $validated['class_10_percentage'] ?? 'none',
                'class_12_percentage' => $validated['class_12_percentage'] ?? 'none',
                'graduation_percentage' => $validated['graduation_percentage'] ?? 'none',
                'backlogs' => $validated['backlogs'] ?? 'none',
                'plans' => $request->plans ?? 'none',
            ],
            'resume' => [
                'link' => asset("storage/$path"),
            ],
        ];
        \DB::table('job_user')->insert([
            'job_id' => $jobId,
            'user_id' => auth()->id(),
            'data' => json_encode($data),
            'applied_at' => now(),
        ]);

        return redirect()->route('job.publicindex')->with('success', 'Application submitted successfully.');
    }

}
