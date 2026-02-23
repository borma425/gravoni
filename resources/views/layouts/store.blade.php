<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'جرافوني - متجر الأزياء')</title>
    
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * { font-family: 'Cairo', sans-serif; }
        
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        .product-image {
            transition: transform 0.5s ease;
        }
        
        .color-option {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .color-option:hover, .color-option.active {
            transform: scale(1.15);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
        
        .size-option {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .size-option:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        .size-option.active {
            background-color: #1e293b;
            color: white;
            border-color: #1e293b;
        }
        .size-option.out-of-stock {
            opacity: 0.4;
            cursor: not-allowed;
            text-decoration: line-through;
        }
        
        .zoom-lens {
            position: absolute;
            border: 2px solid #3b82f6;
            width: 150px;
            height: 150px;
            pointer-events: none;
            background-repeat: no-repeat;
            display: none;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .zoom-result {
            position: absolute;
            border: 1px solid #e5e7eb;
            width: 400px;
            height: 400px;
            background-repeat: no-repeat;
            display: none;
            z-index: 100;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .gallery-thumb {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .gallery-thumb:hover, .gallery-thumb.active {
            border-color: #3b82f6;
            transform: scale(1.05);
        }
        
        .badge-sale {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #475569 50%, #64748b 100%);
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-skeleton {
            animation: shimmer 2s infinite linear;
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 200% 100%;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('store.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('favicon-32x32.png') }}" alt="جرافوني" class="h-8 w-8">
                    <span class="text-xl font-bold text-slate-800">جرافوني</span>
                </a>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('store.index') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">الرئيسية</a>
                    <a href="{{ route('store.index') }}#products" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">المنتجات</a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button type="button" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100" id="mobile-nav-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div class="hidden md:hidden bg-white border-t" id="mobile-nav">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('store.index') }}" class="block py-2 text-slate-600 hover:text-slate-900 font-medium">الرئيسية</a>
                <a href="{{ route('store.index') }}#products" class="block py-2 text-slate-600 hover:text-slate-900 font-medium">المنتجات</a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-slate-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('favicon-32x32.png') }}" alt="جرافوني" class="h-8 w-8" style="filter: invert(1);">
                        <span class="text-xl font-bold">جرافوني</span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        متجرك المفضل للأزياء العصرية بجودة عالية وأسعار منافسة
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">روابط سريعة</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('store.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm">الرئيسية</a></li>
                        <li><a href="{{ route('store.index') }}#products" class="text-slate-400 hover:text-white transition-colors text-sm">المنتجات</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">تواصل معنا</h3>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>01234567890</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>القاهرة، مصر</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-800 mt-8 pt-8 text-center text-slate-400 text-sm">
                <p>&copy; {{ date('Y') }} جرافوني. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>
    
    <script>
        document.getElementById('mobile-nav-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-nav')?.classList.toggle('hidden');
        });
    </script>
    
    @yield('scripts')
</body>
</html>
