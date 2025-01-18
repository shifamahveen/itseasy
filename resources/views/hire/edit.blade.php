@extends('layouts.app')
@section('title', 'Edit Job')
@section('content')

<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Edit Job</h2>
    <form action="{{ route('job.update', $job->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-gray-700">Title</label>
            <input type="text" name="title" value="{{ $job->title }}" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>
        </div>
        <div>
            <label class="block text-gray-700">Company</label>
            <input type="text" name="company" value="{{ $job->company }}" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
        </div>
        <div>
            <label class="block text-gray-700">Locations</label>
            <input type="text" name="locations" value="{{ $job->locations }}" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
        </div>
        <div>
            <label class="block text-gray-700">Description</label>
            <textarea name="description" rows="4" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-200">{{ $job->description }}</textarea>
        </div>
        <div>
            <label class="block text-gray-700">Logo</label>
            <input type="file" name="logo" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
        </div>
        <div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">Update</button>
        </div>
    </form>
</div>

@endsection
