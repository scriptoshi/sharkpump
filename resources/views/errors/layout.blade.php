<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Error' }} | {{ config('app.name') }}</title>
    @vite(['resources/js/app.js'])
</head>
<body class="h-full">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
        <div class="max-w-max mx-auto">
            <section class="bg-white dark:bg-gray-900 ">
                <div class="container flex items-center mx-auto">
                    <div>
                        <p class="text-6xl font-medium text-red-500 dark:text-red-400"> @yield('code')</p>
                        <h1 class="mt-3 text-2xl font-semibold text-gray-800 dark:text-white md:text-3xl">@yield('title')</h1>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">@yield('message').</p>

                        <div class="flex items-center mt-6 gap-x-3">
                            <button onclick="window.history.back()"  class="flex items-center justify-center w-1/2 px-5 py-2 text-sm text-gray-700 transition-colors duration-200 bg-white border rounded gap-x-2 sm:w-auto dark:hover:bg-gray-800 dark:bg-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:border-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 rtl:rotate-180">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18" />
                                </svg>

                                <span>Go back</span>
                            </button>

                            <a href="{{ url('/') }}" class="w-1/2 px-5 py-2 text-sm tracking-wide text-white transition-colors duration-200 bg-amber-500 rounded shrink-0 sm:w-auto hover:bg-amber-600 dark:hover:bg-amber-500 dark:bg-amber-600">
                                Take me home
                            </a>
                        </div>
                    </div>
                </div>
            </section>
            
        </div>
    </div>

    <script>
        // Check for dark mode preference
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>