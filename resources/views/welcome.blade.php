<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased">
    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 selection:bg-red-500 selection:text-white">
        @if (Route::has('login'))
            <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                @auth
                    <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Admin Login</a>
                    <a href="/agent/login" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Agent Login</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex justify-center">
                <h1 class="text-6xl font-bold text-gray-900 mb-8">
                    Insurance App
                </h1>
            </div>

            <div class="mt-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                    <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <h2 class="mt-6 text-xl font-semibold text-gray-900">Welcome to Insurance Management</h2>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Manage your insurance policies, claims, and documentation all in one place. Our comprehensive platform provides you with the tools you need to stay organized and informed about your insurance coverage.
                            </p>
                        </div>
                    </div>

                    <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <h2 class="mt-6 text-xl font-semibold text-gray-900">Easy Policy Management</h2>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Track your policies, view coverage details, and manage renewals with our intuitive interface. Get notifications for important dates and never miss a payment again.
                            </p>
                        </div>
                    </div>

                    <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <h2 class="mt-6 text-xl font-semibold text-gray-900">Claims Processing</h2>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Submit and track insurance claims quickly and efficiently. Upload documents, communicate with adjusters, and get real-time updates on your claim status.
                            </p>
                        </div>
                    </div>

                    <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <h2 class="mt-6 text-xl font-semibold text-gray-900">Secure & Reliable</h2>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Your data is protected with industry-standard security measures. Access your information anywhere, anytime with confidence that your personal information is safe.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-16">
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Go to Dashboard
                    </a>
                @else
                    <div class="flex space-x-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Admin Login
                        </a>
                        @if (Route::has('agent.login'))
                            <a href="/agent/login" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Agent Login
                            </a>
                        @endif
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
