<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Product Catalog' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="antialiased bg-gray-50">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-gray-900">E-Commerce</a>
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="/catalog" class="text-gray-900 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                            Catalog
                        </a>
                        <a href="/admin" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-8">
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Laravel Modular E-Commerce
            </p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
