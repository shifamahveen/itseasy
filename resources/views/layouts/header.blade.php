<nav class="bg-blue-800 text-white">
    <div class="max-w-screen-xl px-4 py-1 mx-auto sm:px-16 flex justify-between items-center">
        <!-- logo -->
        <div class="bg-white px-4 py-1 my-2 w-fit rounded">
            <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="w-32" />
        </div>

        <!-- nav links -->
        <div class="flex space-x-1 items-center">
            <a href="" class="hover:bg-blue-50 opacity-75 px-4 hover:text-blue-900 duration-300 p-2 rounded">Home</a>    
            <a href="{{ route('profile.edit') }}" class="hover:bg-blue-50 opacity-75 px-4 hover:text-blue-900 duration-300 p-2 rounded">Profile</a>    
            <a href="" class="hover:bg-blue-50 opacity-75 px-4 hover:text-blue-900 duration-300 p-2 rounded">Admin</a>    
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="hover:bg-blue-50 opacity-75 px-4 hover:text-blue-900 duration-300 p-2 rounded">Logout</button>
            </form>
        </div>
    </div>
</nav>