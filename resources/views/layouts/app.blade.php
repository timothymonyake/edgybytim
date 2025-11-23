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
</head>
<body>
    @include('layouts.partials.header')
    
    <div class="main-container">
        <div class="pd-ltr-20">
            @yield('content')
        </div>
    </div>
    
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
    
    @stack('scripts')
</body>
</html>
