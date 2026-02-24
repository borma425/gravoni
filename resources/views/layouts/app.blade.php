<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'نظام إدارة المخزون')</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Sidebar Overlay -->
        <div id="mobile-sidebar-overlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden hidden"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 right-0 z-50 w-72 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 lg:flex lg:flex-shrink-0 -translate-x-full">
            <div class="flex flex-col w-72">
                <div class="flex flex-col flex-grow bg-gradient-to-b from-slate-800 to-slate-900 pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center justify-between flex-shrink-0 px-4">
                        <div class="flex items-center min-w-0 flex-1">
                            <img src="{{ asset('favicon-32x32.png') }}" alt="Logo" class="h-8 w-8 ml-2 flex-shrink-0" style="filter: invert(1);">
                            <span class="mr-3 text-white text-lg font-bold whitespace-nowrap">نظام المخزون جرافوني</span>
                        </div>
                        <button type="button" class="lg:hidden text-slate-300 hover:text-white flex-shrink-0" onclick="closeSidebar()">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-8 flex-grow flex flex-col">
                        <nav class="flex-1 px-2 space-y-1">
                            <a href="{{ route('dashboard') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                لوحة التحكم
                            </a>
                            <a href="{{ route('products.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('products.*') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                المنتجات
                            </a>
                            <a href="{{ route('purchases.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('purchases.*') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                المشتريات
                            </a>
                            <a href="{{ route('sales.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('sales.*') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                المبيعات
                            </a>
                            <a href="{{ route('orders.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('orders.*') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                أوردرات تحتاج التأكيد
                            </a>
                            <div class="pt-4 mt-4 border-t border-slate-600">
                                <p class="px-3 text-xs font-semibold text-slate-300 uppercase tracking-wider">الحركات</p>
                            </div>
                            <a href="{{ route('stock-movements.sales-return') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('stock-movements.sales-return') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                مرتجع بيع
                            </a>
                            <a href="{{ route('stock-movements.purchase-return') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('stock-movements.purchase-return') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                مرتجع شراء
                            </a>
                            <a href="{{ route('stock-movements.damage') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('stock-movements.damage') && !request()->routeIs('stock-movements.damage.index') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                تسجيل تلف
                            </a>
                            <a href="{{ route('stock-movements.damage.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('stock-movements.damage.index') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                قائمة التلفيات
                            </a>
                            <a href="{{ route('governorates.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('governorates.*') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                رسوم المحافظات
                            </a>
                            <div class="pt-4 mt-4 border-t border-slate-600">
                                <p class="px-3 text-xs font-semibold text-slate-300 uppercase tracking-wider">التقارير</p>
                            </div>
                            <a href="{{ route('reports.profit') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('reports.profit') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                تقرير الأرباح
                            </a>
                            <a href="{{ route('losses.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('losses*') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                الخسائر
                            </a>
                            <a href="{{ route('reports.low-stock') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('reports.low-stock') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                مخزون منخفض
                            </a>
                            <div class="pt-4 mt-4 border-t border-slate-600">
                                <p class="px-3 text-xs font-semibold text-slate-300 uppercase tracking-wider">الإعدادات</p>
                            </div>
                            <a href="{{ route('settings.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('settings.*') ? 'bg-slate-700 text-white' : '' }}">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                إعدادات الحساب
                            </a>
                        </nav>
                    </div>
                    <div class="flex-shrink-0 flex border-t border-slate-600 p-4">
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="group flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors">
                                <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Mobile menu button -->
            <div class="lg:hidden relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                <button type="button" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-slate-500" id="mobile-menu-button">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="w-full px-4 sm:px-6 lg:px-10">
                        @if(session('success'))
                            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="mr-3">
                                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="mr-3">
                                        <ul class="list-disc list-inside text-sm font-medium text-red-800">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-sidebar-overlay');
        
        function openSidebar() {
            if (sidebar) sidebar.classList.remove('-translate-x-full');
            if (overlay) overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeSidebar() {
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }
        
        mobileMenuButton?.addEventListener('click', openSidebar);
        overlay?.addEventListener('click', closeSidebar);
        
        // Close sidebar when clicking on a link (mobile only)
        if (window.innerWidth < 1024) {
            const sidebarLinks = sidebar?.querySelectorAll('a');
            sidebarLinks?.forEach(link => {
                link.addEventListener('click', closeSidebar);
            });
        }
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
