<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-4">Register</h2>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Name:</label>
                <input type="text" id="name" name="name" required class="border border-gray-300 p-2 rounded w-full">
            </div>

            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username:</label>
                <input type="text" id="username" name="username" required class="border border-gray-300 p-2 rounded w-full">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password:</label>
                <input type="password" id="password" name="password" required class="border border-gray-300 p-2 rounded w-full">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700">Confirm Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="border border-gray-300 p-2 rounded w-full">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Register</button>
        </form>

        <p class="mt-4 text-center">Already have an account? <a href="{{ route('login') }}" class="text-blue-500">Login here</a></p>
    </div>
</body>

</html>
