<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="grid grid-cols-2">
        <!-- img -->
        <div class="col-span-2 md:col-span-1 hidden md:flex px-8 h-screen flex-col justify-center">
            <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="w-96 block mx-auto" />

            <img src="{{ asset('storage/banner.png') }}" alt="banner" class="w-96 mt-10 block mx-auto" />
        </div>

        <!-- form -->
        <div class="col-span-2 md:col-span-1 bg-blue-800 text-white p-8 md:px-12 lg:px-24 h-screen flex flex-col justify-center">
            <x-auth-session-status class="mb-4" :status="session('status')" />
            <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="w-60 block mx-auto md:hidden bg-white p-4 py-2 rounded" />
 
            <h1 class="text-center text-xl sm:text-5xl font-semibold my-4">Welcome Back!</h1>
            <p class="text-center text-lg">Dont have an account? <a href="{{ route('register') }}" class="underline hover:text-gray-200 duration-300">Sign Up</a></p>
            <form method="POST" action="{{ route('login') }}" class="my-10">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-white text-xl font-normal" />
                    <x-text-input id="email" class="block mt-1 w-full p-4" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="text-white text-xl font-normal" />

                    <x-text-input id="password" class="block mt-1 w-full p-4 text-black"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mt-4">

                    <div class="block">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                            <span class="ms-2 text-sm text-white">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-white hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <button type="submit" class="bg-blue-500 text-white text-lg border-none hover:bg-blue-400 duration-300 w-full p-4 rounded my-8">Login</button>
            </form>
        </div>
    </main>
</body>
</html>