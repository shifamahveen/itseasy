<?php

namespace App\Http\Controllers\Hire;

use App\Models\Hire\Job;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public $viewFolder = 'hire.';

    public function index()
    {
        $jobs = Job::paginate(30);
        return view($this->viewFolder . 'index', compact('jobs'));
    }

    public function publicindex(Request $request)
    {
        $query = $request->input('search');
        $jobs = Job::when($query, function ($q) use ($query) {
            $q->where('title', 'LIKE', "%$query%")
              ->orWhere('company', 'LIKE', "%$query%")
              ->orWhere('locations', 'LIKE', "%$query%");
        })->paginate(30);
    
        // Check if it's an AJAX request and return only the jobs.
        if ($request->ajax()) {
            return response()->json([
                'html' => view($this->viewFolder . 'partials.job_listings', compact('jobs'))->render()
            ]);
        }
    
        return view($this->viewFolder . 'publicindex', compact('jobs'));
    }
    

    public function create()
    {
        return view($this->viewFolder . 'create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'locations' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|file|mimes:jpg,png|max:2048', // Validate jpg/png, max size 2MB
        ]);

        try {
            $job = new Job();
            $job->title = $validated['title'];
            $job->company = $validated['company'];
            $job->slug = Str::slug($validated['title'] . '-' . time());
            $job->client_slug = 'super';
            $job->locations = $validated['locations'] ?? null;
            $job->description = $validated['description'] ?? null;

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/logos'), $filename); // Save file in public/uploads/logos
                $job->logo = $filename;
            }

            $job->save();

            return redirect()->route('jobs.index')->with('success', 'Job posted successfully!');
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Job posting error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while posting the job: ' . $e->getMessage()]);
        }
    }

    public function edit(Job $job)
    {
        return view($this->viewFolder . 'edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:jobs,slug,' . $job->id,
            'client_slug' => 'required',
        ]);

        $job->update($request->all());
        return redirect()->route('jobs.index');
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('job.index');
    }

    public function bookmark(Job $job)
    {
        $job->bookmarkedUsers()->syncWithoutDetaching(auth()->id());
        return back()->with('success', 'Job bookmarked successfully.');
    }

}
