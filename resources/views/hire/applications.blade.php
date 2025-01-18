@extends('layouts.app')
@section('title', 'Job Applications')
@section('content')

<div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Applications for {{ $job->title }}</h2>

    @if($applications->isNotEmpty())
        <table class="w-full text-left table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">S.NO</th>
                    <th class="border border-gray-300 px-4 py-2">Applicant</th>
                    <th class="border border-gray-300 px-4 py-2">College</th>
                    <th class="border border-gray-300 px-4 py-2">Branch</th>
                    <th class="border border-gray-300 px-4 py-2">Year Of Passing</th>
                    <th class="border border-gray-300 px-4 py-2">Applied At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $k => $application)
                    <tr class="hover:bg-gray-100">
                        <td class="border border-gray-300 px-4 py-2">{{ ++$k }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <p class="text-lg">{{ $application->user->name }}</p>
                            <p class="text-sm text-green-500">{{ $application->user->email }}</p>
                            <p class="text-sm text-gray-500">{{ $application->user->phone }}</p>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">{{ $application->user->c1 }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $application->user->c2 }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $application->user->c3 }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $application->applied_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-700 bg-gray-50 p-4 rounded my-4">No applications found.</p>
    @endif
</div>
@endsection