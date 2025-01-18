@extends('layouts.app')
@section('title','Users Edit')
@section('content')
    <div class="container mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-4">{{ isset($user) ? 'Edit' : 'Create' }} User</h1>
        <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST" class="bg-white shadow-md rounded-lg p-4 ">
            @csrf
            @if (isset($user))
            @method('PUT')
           @endif
           <div class="grid grid-cols-2 gap-x-8">

                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ $user->name ?? old('name') }}" class="w-full border border-gray-300 rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ $user->email ?? old('email') }}" class="w-full border border-gray-300 rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ $user->phone ?? old('phone') }}" class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <div class="block text-gray-700"> Status </div>
                    
                    <select name="status" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="mb-4">
                    <div class="block text-gray-700"> Privilege </div>
                    
                    <select name="privilege" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">None</option>
                        <option value="HR">HR</option>
                        <option value="TPO">TPO</option>
                        <option value="Enrolled">Enrolled</option>
                    </select>
                </div>

                <div class="mb-4">
                    <div class="block text-gray-700"> College </div>
                    
                    <select id="college" name="c1" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="hyderabad">Hyderabad</option>
                        <option value="vijayawada">Vijayawada</option>
                        <option value="vizag">Vizag</option>
                    </select>
                </div>
            
                <div class="mb-4">
                    <div class="block text-gray-700"> Branch</div>
                    
                    <select id="branch" name="c2" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="hyderabad">Hyderabad</option>
                        <option value="vijayawada">Vijayawada</option>
                        <option value="vizag">Vizag</option>
                    </select>
                </div>      
            
                <div class="mb-4">
                    <div class="block text-gray-700"> Year Of Passing</div>
                    
                    <select id="branch" name="c3" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="hyderabad">Hyderabad</option>
                        <option value="vijayawada">Vijayawada</option>
                        <option value="vizag">Vizag</option>
                    </select>
                </div>      
            
                <div class="mb-4">
                    <div class="block text-gray-700">Gender</div>
                
                    <select id="gender" name="c4" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="others">Others</option>
                    </select>
                </div>      
            
                <div class="mb-4">
                    <div class="block text-gray-700">Class 10th</div>
                    
                    <input type="number" name="c5" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="" />
                </div>
            
                <div class="mb-4">
                    <div class="block text-gray-700">Class 12th</div>
                    
                    <input type="number" name="c6" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="" />
                </div>
            
                <div class="mb-4">
                    <div class="block text-gray-700">Graduation</div>
                    
                    <input type="number" name="c7" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="" />
                </div>
            
                <div class="mb-4">
                    <div class="block text-gray-700">Post Graduation</div>
                    
                    <input type="number" name="c8" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="" />
                </div>
            
                <div class="mb-4">
                    <div class="block text-gray-700">Backlogs</div>
                    
                    <input type="number" name="c9" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="" />
                </div>
            
                <div class="mb-4">
                    <div class="block text-gray-700">Skills</div>
                    
                    <input type="text" name="c10" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="" />
                </div>           
            </div>

            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded w-full hover:bg-green-600 duration-300">
                {{ isset($user) ? 'Update' : 'Create' }}
            </button>
        </form>
    </div>

@endsection