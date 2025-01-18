@extends('layouts.app')
@section('title', 'Post a Job')
@section('content')

<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Post a Job</h2>

    <!-- Display Success Message -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Display Error Messages -->
    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('job.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-gray-700">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
        </div>
        <div>
            <label class="block text-gray-700">Company</label>
            <input type="text" name="company" value="{{ old('company') }}" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
        </div>
        <div>
            <label class="block text-gray-700">Locations</label>
            <input type="text" name="locations" value="{{ old('locations') }}" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
        </div>
        <div>
            <label class="block text-gray-700">Description</label>
            <textarea name="description" rows="4" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-200">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-gray-700">Logo</label>
            <input type="file" name="logo" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
            <p class="text-sm text-gray-500 mt-1">Upload only .jpg or .png files (Max: 2MB).</p>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">Submit</button>
        </div>
    </form>
</div>

@endsection
