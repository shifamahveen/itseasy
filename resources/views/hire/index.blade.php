@extends('layouts.app')
@section('title', 'Job Listings')
@section('content')

<div class="flex justify-between items-center mb-4 bg-white p-8 rounded-lg border-l-4 border-blue-200">
    <h2 class="text-2xl font-semibold">Job Listings</h2>
    <a href="{{ route('job.create') }}" class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600">Post a Job</a>
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 bg-white p-8 rounded-lg border-l-4 border-blue-200">
    @foreach($jobs as $job)
    <div class="bg-white p-4 rounded-lg shadow-md">
        <div class="flex space-x-4 items-start">
            @if($job->logo)
            <img src="{{ asset('uploads/logos/' . $job->logo) }}" alt="{{ $job->title }} Logo" class="w-12 h-12 mb-4 rounded-full inline-flex">
            @endif
            <h3 class="text-lg font-bold">{{ $job->title }}</h3>
        </div>
        
        <p class="text-sm text-gray-600">{{ $job->company }}</p>
        <p class="text-gray-800">{{ $job->locations }}</p>
        <div class="mt-4 flex justify-between space-x-2 items-center">

            <a href="{{ route('job.applications', $job->id) }}" class="text-green-500 hover:underline">Applications</a> 

            <div class="flex space-x-2 items-center">
                <a href="{{ route('job.edit', $job->id) }}"  class="bg-gray-200 opacity-45 text-white hover:bg-gray-400 p-2 rounded shadow duration-300">
                    <svg viewBox="0 0 24 24" class="w-7 text-white" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path opacity="0.4" d="M15.48 3H7.52C4.07 3 2 5.06 2 8.52V16.47C2 19.94 4.07 22 7.52 22H15.47C18.93 22 20.99 19.94 20.99 16.48V8.52C21 5.06 18.93 3 15.48 3Z" fill="#292D32"></path> <path d="M21.0195 2.97979C19.2295 1.17979 17.4795 1.13979 15.6395 2.97979L14.5095 4.09979C14.4095 4.19979 14.3795 4.33979 14.4195 4.46979C15.1195 6.91979 17.0795 8.87979 19.5295 9.57979C19.5595 9.58979 19.6095 9.58979 19.6395 9.58979C19.7395 9.58979 19.8395 9.54979 19.9095 9.47979L21.0195 8.35979C21.9295 7.44979 22.3795 6.57979 22.3795 5.68979C22.3795 4.78979 21.9295 3.89979 21.0195 2.97979Z" fill="#292D32"></path> <path d="M17.8591 10.4198C17.5891 10.2898 17.3291 10.1598 17.0891 10.0098C16.8891 9.88984 16.6891 9.75984 16.4991 9.61984C16.3391 9.51984 16.1591 9.36984 15.9791 9.21984C15.9591 9.20984 15.8991 9.15984 15.8191 9.07984C15.5091 8.82984 15.1791 8.48984 14.8691 8.11984C14.8491 8.09984 14.7891 8.03984 14.7391 7.94984C14.6391 7.83984 14.4891 7.64984 14.3591 7.43984C14.2491 7.29984 14.1191 7.09984 13.9991 6.88984C13.8491 6.63984 13.7191 6.38984 13.5991 6.12984C13.4691 5.84984 13.3691 5.58984 13.2791 5.33984L7.89912 10.7198C7.54912 11.0698 7.20912 11.7298 7.13912 12.2198L6.70912 15.1998C6.61912 15.8298 6.78912 16.4198 7.17912 16.8098C7.50912 17.1398 7.95912 17.3098 8.45912 17.3098C8.56912 17.3098 8.67912 17.2998 8.78912 17.2898L11.7591 16.8698C12.2491 16.7998 12.9091 16.4698 13.2591 16.1098L18.6391 10.7298C18.3891 10.6498 18.1391 10.5398 17.8591 10.4198Z" fill="#292D32"></path> </g></svg>
                </a> 
                <form action="{{ route('job.destroy', $job->id) }}" method="POST" class="bg-red-200 opacity-45 hover:bg-red-300 text-white p-2 rounded shadow duration-300" onsubmit="return confirmDelete()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="opacity-60 text-white rounded shadow duration-300">
                        <svg fill="#df1f1f" class="w-6" viewBox="0 0 24 24" class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" stroke="#db1f1f"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M5.755,20.283,4,8H20L18.245,20.283A2,2,0,0,1,16.265,22H7.735A2,2,0,0,1,5.755,20.283ZM21,4H16V3a1,1,0,0,0-1-1H9A1,1,0,0,0,8,3V4H3A1,1,0,0,0,3,6H21a1,1,0,0,0,0-2Z"></path></g></svg>
                    </button>
                </form>
            </div>
            
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination Links -->
<div class="mt-4">
    {{ $jobs->links('pagination::tailwind') }}
</div>
<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this job? This action cannot be undone.');
    }
</script>
@endsection
