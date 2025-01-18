<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">

    <main class="flex items-center justify-center min-h-screen my-10">

        <!-- Form Container -->
        <div class="bg-white shadow-lg rounded-lg w-full max-w-4xl mx-4 p-8 lg:p-12">
            <!-- Logo -->
            <div class="mb-6 text-center">
                <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="w-32 mx-auto">
            </div>

            <!-- Heading -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-blue-800">Sign Up</h1>
                <p class="text-gray-600 mt-2">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Sign In</a>
                </p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input id="name" name="name" type="text" required autofocus autocomplete="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 text-gray-900">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" required autocomplete="username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 text-gray-900">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 text-gray-900">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 text-gray-900">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input id="phone" name="phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 text-gray-900">
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                        <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 text-gray-900">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="others">Others</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>

                    <!-- Additional Fields -->
                    <div>
                        <label for="current_city" class="block text-sm font-medium text-gray-700">Current City</label>
                        <select id="current_city" name="current_city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 text-gray-900">
                            <option value="hyderabad">Hyderabad</option>
                            <option value="vijayawada">Vijayawada</option>
                            <option value="vizag">Vizag</option>
                        </select>
                        <x-input-error :messages="$errors->get('current_city')" class="mt-2" />
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                        <select id="state" name="state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-3 text-gray-900">
                            <option value="hyderabad">Hyderabad</option>
                            <option value="vijayawada">Vijayawada</option>
                            <option value="vizag">Vizag</option>
                        </select>
                        <x-input-error :messages="$errors->get('state')" class="mt-2" />
                    </div>

                    <!-- College -->
                    <div class="my-2">
                        <label for="college" class="block text-sm font-medium text-gray-700">College</label>
                        <select id="college" name="c1" class="block mt-1 w-full p-4 text-black rounded border-gray-300">
                            <option value="mjcet">MJCET</option>
                            <option value="cbit">CBIT</option>
                            <option value="svit">SVIT</option>
                        </select>
                        <x-input-error :messages="$errors->get('college')" class="mt-2" />
                    </div>
                    
                    <!-- Branch -->
                    <div class="my-2">
                        <label for="branch" class="block text-sm font-medium text-gray-700">Branch</label>
                        <select id="branch" name="c2" class="block mt-1 w-full p-4 text-black rounded border-gray-300">
                            <option value="cse">Computer Science</option>
                            <option value="civit">Civil</option>
                            <option value="mech">Mech</option>
                        </select>
                        <x-input-error :messages="$errors->get('branch')" class="mt-2" />
                    </div>

                    <!-- Year Of Passing -->
                    <div class="my-2">
                    <label for="yop" class="block text-sm font-medium text-gray-700">Year Of Passing</label>
                    <select id="yop" name="c3" class="block mt-1 w-full p-4 text-black rounded border-gray-300">
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                        </select>
                        <x-input-error :messages="$errors->get('yop')" class="mt-2" />
                    </div>

                    <!-- Class 10th -->
                    <div class="my-2">
                    <label for="class_10th" class="block text-sm font-medium text-gray-700">Class 10th Percentage</label>
                    <x-text-input id="c5" class="block mt-1 w-full p-4 text-black" type="number" name="c5" required />
                        <x-input-error :messages="$errors->get('c5')" class="mt-2" />
                    </div>
                    
                    <!-- Class 12th -->
                    <div class="my-2">
                        <label for="c6" class="block text-sm font-medium text-gray-700">Class 12th Percentage</label>
                        <x-text-input id="c6" class="block mt-1 w-full p-4 text-black" type="number" name="c6" required />
                        <x-input-error :messages="$errors->get('c6')" class="mt-2" />
                    </div>
                    
                    <!-- Graduation -->
                    <div class="my-2">
                        <label for="c7" class="block text-sm font-medium text-gray-700">Graduation Percentage</label>
                        <x-text-input id="c7" class="block mt-1 w-full p-4 text-black" type="number" name="c7" required />
                        <x-input-error :messages="$errors->get('c7')" class="mt-2" />
                    </div>

                    <!-- Post Graduation -->
                    <div class="my-2">
                        <label for="c8" class="block text-sm font-medium text-gray-700">Post Graduation</label>
                        <x-text-input id="c8" class="block mt-1 w-full p-4 text-black" type="number" name="c8" />
                        <x-input-error :messages="$errors->get('c8')" class="mt-2" />
                    </div> 

                    <!-- Backlogs -->
                    <div class="my-2">
                        <label for="c9" class="block text-sm font-medium text-gray-700">Backlogs</label>
                        <x-text-input id="c9" class="block mt-1 w-full p-4 text-black" type="number" name="c9" required />
                        <x-input-error :messages="$errors->get('c9')" class="mt-2" />
                    </div>               

                    <!-- Skill Set -->
                    <div class="my-2">
                        <label for="c10" class="block text-sm font-medium text-gray-700">Skill Set</label>
                        <x-text-input id="c10" class="block mt-1 w-full p-4 text-black" type="text" name="c10" />
                        <x-input-error :messages="$errors->get('c10')" class="mt-2" />
                    </div>

                </div>

                <!-- Submit Button -->
                <div class="mt-8 text-center">
                    <button type="submit" class="bg-blue-600 text-white py-3 px-8 rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Register
                    </button>
                </div>
            </form>
        </div>

    </main>

</body>
</html>