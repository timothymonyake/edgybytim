<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Trading Journal')</title>
    
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/core.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/icon-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('deskapp/src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('deskapp/src/plugins/datatables/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    @stack('styles')
    <style>
        /* Sticky footer removed as per user request */
        .footer-text-bold {
            font-weight: 700;
            font-size: 14px;
            color: #333;
        }
        .footer-text-italic {
            font-style: italic;
            color: #555;
            margin: 0 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    @include('layouts.partials.header')
    
    <div class="main-container">
        <div class="pd-ltr-20" style="padding-bottom: 50px;">
            @yield('content')
        </div>
    </div>

    <div class="footer-wrap pd-20 mb-20 card-box">
        <span class="footer-text-bold">HLEAABSSYGAC</span> - 
        <span class="footer-text-italic">I accept the risk.Anything can happen.My job is execution, not prediction.</span>
        <span class="footer-text-bold"> #IGWT - You're Great!</span>
    </div>
    
    @yield('left-sidebar')
    
    <!-- JS -->
    <script src="{{ asset('deskapp/vendors/scripts/core.js') }}"></script>
    <script src="{{ asset('deskapp/vendors/scripts/script.min.js') }}"></script>
    <script src="{{ asset('deskapp/vendors/scripts/process.js') }}"></script>
    <script src="{{ asset('deskapp/vendors/scripts/layout-settings.js') }}"></script>
    <script src="{{ asset('deskapp/src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('deskapp/src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('deskapp/src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('deskapp/src/plugins/datatables/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    
    @stack('scripts')
</body>
</html>
