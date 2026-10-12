<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kargo Takip & Yönetim Sistemi')</title>

    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bs-body-font-family: 'Inter', sans-serif;
            --bs-primary: #4f46e5;
            --bs-primary-rgb: 79, 70, 229;
        }
        body {
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        h1, h2, h3, h4, h5, .navbar-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
        }
        .navbar {
            background: #ffffff !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.07);
        }
        .nav-link {
            font-weight: 500;
            color: #475569;
            padding: 0.5rem 0.9rem !important;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: #4f46e5 !important;
            background-color: #eef2ff;
        }
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }
        .btn {
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
        }
        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
        .table > :not(caption) > * > * {
            padding: 0.85rem 1rem;
            vertical-align: middle;
        }
        .badge {
            font-weight: 600;
            padding: 0.45em 0.75em;
            border-radius: 9999px;
        }
        footer {
            margin-top: auto;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
            padding: 1.25rem 0;
            color: #64748b;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top py-3">
        <div class="container">
            <a class="navbar-brand text-primary d-flex align-items-center gap-2" href="{{ route('orders.index') }}" id="nav-brand">
                <i class="bi bi-box-seam-fill fs-4"></i>
                <span>KargoTakip <span class="text-dark">PRO</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Menüyü Aç">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}" id="nav-orders">
                            <i class="bi bi-bag-check me-1"></i> Siparişler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}" id="nav-customers">
                            <i class="bi bi-people me-1"></i> Müşteriler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shipments.*') ? 'active' : '' }}" href="{{ route('shipments.index') }}" id="nav-shipments">
                            <i class="bi bi-truck me-1"></i> Kargolar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('statuses.*') ? 'active' : '' }}" href="{{ route('statuses.index') }}" id="nav-statuses">
                            <i class="bi bi-tags me-1"></i> Durumlar
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('tracking.index') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="nav-tracking-search">
                        <i class="bi bi-search"></i> Kargo Sorgula
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="py-4">
        <div class="container">
            <!-- Flash Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert" id="flash-success">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                </div>
            @endif

            <!-- Flash Error Message -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert" id="flash-error">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                </div>
            @endif

            <!-- Validation Errors ($errors) -->
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" id="flash-validation-errors">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                        <strong class="d-block">Lütfen formdaki hataları düzeltiniz:</strong>
                    </div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p class="mb-0">© {{ date('Y') }} KargoTakip PRO — Sipariş, Müşteri, Kargo & Durum Yönetim Sistemi</p>
        </div>
    </footer>

    <!-- Bootstrap 5.3 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
