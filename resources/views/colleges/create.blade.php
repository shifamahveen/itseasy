@extends('layouts.app')

@section('title', 'Add College')

@section('content')
<div class="bg-white shadow rounded p-6">

    <form action="{{ route('colleges.store') }}" method="POST">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-800">Add College</h1>
            <button type="submit" class="block bg-green-500 text-white px-4 py-2 rounded w-fit ms-auto hover:bg-green-600 duration-300">Save</button>
        </div>
        @csrf
        <div class="grid grid-cols-2 gap-x-8 py-6">
            <!-- Form elements -->
            <div class="mb-4">
                <div class="block text-gray-700"> Name </div>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter the name of the college" class="w-full p-2 border-2 border-gray-200 rounded" />
                </div>
            </div>
            
            <div class="mb-4">
                <div class="block text-gray-700">Address</div>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="address" value="{{ old('address') }}" class="w-full p-2 border-2 border-gray-200 rounded" />
                </div>
            </div>  

            <div class="mb-4">
                <div class="block text-gray-700"> Code</div>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="code" value="{{ old('code') }}" class="w-full p-2 border-2 border-gray-200 rounded" />
                </div>
            </div>

            <div class="mb-4">
                <div class="block text-gray-700"> Type</div>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="type" value="{{ old('type') }}" class="w-full p-2 border-2 border-gray-200 rounded" />
                </div>
            </div>

            <div class="mb-4">
                <div class="block text-gray-700"> Location</div>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="location" value="{{ old('location') }}" class="w-full p-2 border-2 border-gray-200 rounded" />
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Rating </label>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="rating" value="{{ old('rating') }}" class="w-full p-2 border-2 border-gray-200 rounded" />
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Reviews </label>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="reviews" value="{{ old('reviews') }}" class="w-full p-2 border-2 border-gray-200 rounded" />
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Category </label>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="category" value="{{ old('category') }}" class="w-full p-2 border-2 border-gray-200 rounded" />
                </div>
            </div>

            <div class="mb-4">
                  <label class="block text-gray-700">Zone </label>
                  <select name="zone" class="block w-full col-span-2 text-gray-900 border border-gray-300 rounded bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="Hyderabad North East">Hyderabad North East</option>
                    <option value="Hyderabad Central West">Hyderabad Central West</option>
                    <option value="Hyderabad South">Hyderabad South</option>
                    <option value="Warangal">Warangal</option>
                    <option value="Visakhapatnam">Visakhapatnam</option>
                    <option value="Kakinada">Kakinada</option>
                    <option value="Vijayawada">Vijayawada</option>
                    <option value="Guntur">Guntur</option>
                    <option value="Tirupati">Tirupati</option>
                    <option value="Nellore">Nellore</option>
                    <option value="Kadapa">Kadapa</option>
                    <option value="Anantapur">Anantapur</option>
                    <option value="Unlisted">Unlisted</option>
                  </select>
            </div>

            <div class="mb-4">
                  <label class="block text-gray-700">Zone Code</label>
                  <select name="zone_code" class="block w-full col-span-2 text-gray-900 border border-gray-300 rounded bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="HNE">HNE</option>
                    <option value="HCW">HCW</option>
                    <option value="HSO">HSO</option>
                    <option value="WGL">WGL</option>
                    <option value="VZG">VZG</option>
                    <option value="KKD">KKD</option>
                    <option value="VJY">VJY</option>
                    <option value="GTR">GTR</option>
                    <option value="TPY">TPY</option>
                    <option value="NLR">NLR</option>
                    <option value="KDP">KDP</option>
                    <option value="ANP">ANP</option>
                    <option value="UNL">UNL</option>
                  </select>
            </div>
  
            <div class="mb-4">
                <div class="block text-gray-700"> District</div>
                
                <select name="district" class="block w-full col-span-2 text-gray-900 border border-gray-300 rounded bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="hyderabad">Hyderabad</option>
                  </select>
            </div>

            <div class="mb-4">
                <div class="block text-gray-700"> State</div>
                
                <select name="state" class="block w-full col-span-2 text-gray-900 border border-gray-300 rounded bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="hyderabad">Hyderabad</option>
                </select>
            </div>

            <div class="mb-4">
                <div class="block text-gray-700"> Contact Person</div>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full p-2 border-2 border-gray-200 rounded" />
                </div>
            </div>

            <div class="mb-4">
                <div class="block text-gray-700"> Contact Designation</div>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="contact_designation" value="{{ old('contact_designation') }}" class="w-full p-2 border-2 border-gray-200 rounded" />
                </div>
            </div>

            <div class="mb-4">
                <div class="block text-gray-700"> Contact Phone</div>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" class="w-full p-2 border-2 border-gray-200 rounded" />
                </div>
            </div>

            <div class="mb-4">
                <div class="block text-gray-700"> Contact Email</div>
                <div class="block w-full col-span-2 text-gray-900 rounded-lg bg-gray-50 text-md focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <input type="text" name="contact_email" value="{{ old('contact_email') }}" class="w-full p-2 border-2 border-gray-200 rounded" />
            </div>      

         <!-- end form elements -->
        </div>

    </form>
</div>
@endsection