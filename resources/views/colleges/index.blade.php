@extends('layouts.app')

@section('title', 'Colleges')

@section('content')
<div class="shadow rounded p-6 bg-white">

    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800">Colleges</h1>
        <a href="{{ route('colleges.create') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 duration-300">Add New College</a>
    </div>
   
    <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 text-left font-medium text-gray-700">SNO</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-700">Name</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-700">Type</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-700">Location</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($colleges as $k=>$college)
                <tr>
                    <td class="px-6 py-4 border-b text-gray-700">{{ ++$k }}</td>
                    <td class="px-6 py-4 border-b text-gray-700">{{ $college->name }}</td>
                    <td class="px-6 py-4 border-b text-gray-700">{{ $college->type }}</td>
                    <td class="px-6 py-4 border-b text-gray-700">{{ $college->location }}</td>
                    <td class="px-6 py-4 border-b text-gray-700 flex space-x-1">
                        <a href="{{ route('colleges.edit', $college->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                        <form action="{{ route('colleges.destroy', $college->id) }}" method="POST" onsubmit="return confirmDeletion();">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $colleges->links('pagination::tailwind') }}
    </div>
</div>

<script>
    function confirmDeletion() {
        return confirm('Are you sure you want to delete this college? This action cannot be undone.');
    }
</script>
@endsection