<?php

namespace App\Http\Controllers\Hire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ResumeController extends Controller
{
    // define app and module and view folder
    public $app = 'hire';
    public $module = 'resume';
    public $viewFolder = 'apps.hire.resume.';
    public $alert = null;
    public $errors = null;
    public $resume = null;

    /**
     * load the defaults into the current object
     */
    public function __construct()
    {
        // load the alert if any
        $this->alert = session('alert');
        // load the errors if any
        $this->errors = session('errors');
    }
    /**
     * Display the specified resource to user
     */
    public function show(Request $request)
    {

        if (user()) {
            //load the resume if exists
            if (Storage::exists('resume/' . user('slug') . '.pdf'))
                $this->resume = Storage::url('resume/' . user('slug') . '.pdf');
            // return the view
            return view($this->viewFolder . 'show')
                ->with('data', $this);
        } else {
            abort('403');
        }
    }

    /**
     * Display the specified resource to public
     */
    public function display($slug)
    {
        if (Storage::exists('resume/' . $slug . '.pdf')) {
            $this->resume = Storage::url('resume/' . $slug . '.pdf');
        } else {
            abort('403', 'Resume not found');
        }

        // return the view
        return view($this->viewFolder . 'display')
            ->with('data', $this);
    }

    /**
     * Upload form for resume
     */
    public function upload(Request $request)
    {
        if (user()) {
            // return the view
            return view($this->viewFolder . 'upload')
                ->with('data', $this);
        } else {
            abort('403');
        }
    }

    /**
     * Display the specified resource.
     */
    public function store(Request $request)
    {
        /* validation */

        $validator = Validator::make($request->all(), [
            'resume' => 'file|mimes:pdf',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return redirect()->back()
                ->with('errors', $errors->messages())
                ->withInput();
        }

        /* If image is given upload and store path */
        if (isset($request->all()['resume'])) {
            $fname =  user('slug') . '.pdf';
            Storage::putFileAs('resume', $request->file('resume'), $fname, 'public');
        }

        $alert = "Successfully uploaded the resume!";
        if (request()->get('redirect'))
            return redirect()->to(request()->get('redirect'));
        else
            return redirect()->route('resume.show')->with('alert', $alert);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $token = str_replace(" ", "", strtolower(request()->get('token')));
        $token = str_replace("'", "", $token);

        // reconfirm the delete by matching the name
        if ('permanentlydelete' != $token) {
            $alert = 'Token [' . request()->get('token') . '] is incorrect! Kindly enter token[permanently delete].';
            return redirect()->route('resume.show')
                ->with('alert', $alert)
                ->withInput();
        }

        // delete the resources from storage if any
        if (Storage::exists('resume/' . user('slug') . '.pdf')) {
            Storage::delete('resume/' . user('slug') . '.pdf');
        }

        // alert message
        $alert = 'Your resume is successfully deleted!';
        //redirect to index
        return redirect()->route('resume.show')
            ->with('alert', $alert);
    }
}
