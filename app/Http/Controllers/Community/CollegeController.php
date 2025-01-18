<?php

namespace App\Http\Controllers\Community;

use App\Models\Community\College;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class CollegeController extends Controller
{
    public function index()
    {
        $colleges = College::paginate(10);
        return view('colleges.index', compact('colleges'));
    }

    public function create()
    {
        return view('colleges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->all(); 
        $data['slug'] = Str::random(6);
        $data['client_slug'] = 'super';
        College::create($data);

        return redirect()->route('colleges.index')->with('success', 'College created successfully.');
    }

    public function edit(College $college)
    {
        return view('colleges.edit', compact('college'));
    }

    public function update(Request $request, College $college)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $college->update($data);

        return redirect()->route('colleges.index')->with('success', 'College updated successfully.');
    }

    public function destroy(College $college)
    {
        $college->delete();
        return redirect()->route('colleges.index')->with('success', 'College deleted successfully.');
    }
}
