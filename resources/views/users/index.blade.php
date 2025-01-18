@extends('layouts.app')
@section('title','Users App')
@section('content')

    <div class="container mx-auto mt-10">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Users</h1>
            <a href="{{ route('users.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Create User</a>
        </div>
        
        <div class="mt-6 bg-white shadow-md rounded-lg p-4">
            <table class="min-w-full table-auto border-collapse border border-gray-200">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">SNO</th>
                        <th class="border px-4 py-2">Name</th>
                        <th class="border px-4 py-2">Email</th>
                        <th class="border px-4 py-2">Phone</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                @if (count($users))
                <tbody>
                    @foreach ($users as $k=>$user)
                    <tr>
                        <td class="border px-4 py-2">{{ ++$k }}</td>
                        <td class="border px-4 py-2">{{ $user->name }}</td>
                        <td class="border px-4 py-2">{{ $user->email }}</td>
                        <td class="border px-4 py-2">{{ $user->phone }}</td>
                        <td class="border px-4 py-2 flex space-x-2">
                            <a href="{{ route('users.edit', $user) }}" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @else
                <p class="my-2 text-sm">No users found</p>
                @endif
            </table>
        </div>
          
        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $users->links('pagination::tailwind') }}
        </div>
    </div>

@endsection