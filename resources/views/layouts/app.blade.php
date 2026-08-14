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
    
    <!-- Toastr -->
    <link rel="stylesheet" type="text/css" href="{{ asset('iziToast/cdn.jsdelivr.net_npm_izitoast_dist_css_iziToast.min.css') }}">
    
    <!-- DateRangePicker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    @stack('styles')
    <style>
        /* ── WHITE CLEAN GLOBAL APP HEADER ── */
        .header {
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05) !important;
            height: 70px !important;
            z-index: 10005 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0 20px !important;
        }
        .header .header-left, .header .header-search, .header .brand-logo {
            background: transparent !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }
        .header .brand-logo img {
            max-height: 42px !important;
            width: auto !important;
            object-fit: contain !important;
        }
        
        /* Header Buttons (Menu & Filter) */
        .header .btn-outline-primary,
        .header button.dropdown-toggle,
        .header .toggle-sidebar-btn {
            background: #ffffff !important;
            color: #1b00ff !important;
            border: 1.5px solid #1b00ff !important;
            border-radius: 8px !important;
            padding: 5px 12px !important;
            box-shadow: 0 2px 6px rgba(27,0,255,0.08) !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
        }
        .header .btn-outline-primary:hover,
        .header button.dropdown-toggle:hover,
        .header .toggle-sidebar-btn:hover {
            background: #1b00ff !important;
            color: #ffffff !important;
            border-color: #1b00ff !important;
        }
        .header .btn-outline-primary i,
        .header button.dropdown-toggle i,
        .header .toggle-sidebar-btn i {
            color: inherit !important;
            font-size: 15px !important;
        }

        /* User Info Dropdown Toggle */
        .header .user-info-dropdown .user-name {
            color: #1e293b !important;
            font-weight: 700 !important;
            font-size: 13.5px !important;
        }

        /* ── PERFECT DROPDOWN MENU & ICON ALIGNMENT ── */
        .header .dropdown-menu {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            z-index: 10006 !important;
            box-shadow: 0 12px 32px rgba(0,0,0,0.12) !important;
            padding: 8px !important;
            min-width: 220px !important;
        }
        .header .dropdown-item {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            color: #334155 !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            padding: 9px 14px !important;
            border-radius: 8px !important;
            transition: all 0.15s ease !important;
            position: relative !important;
            line-height: 1.4 !important;
            text-decoration: none !important;
        }
        .header .dropdown-item:hover {
            background: #f1f5f9 !important;
            color: #1b00ff !important;
        }
        .header .dropdown-item i,
        .header .dropdown-item .dw {
            position: static !important; /* Fix DeskApp absolute positioning overlap */
            font-size: 17px !important;
            color: #1b00ff !important;
            width: 22px !important;
            height: 22px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            margin: 0 !important;
            line-height: 1 !important;
        }
        .header .dropdown-item:hover i,
        .header .dropdown-item:hover .dw {
            color: #1b00ff !important;
        }

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
        /* Reminder Top Popup */
        #reminder-top-popup {
            position: fixed;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            width: 90%;
            max-width: 500px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-radius: 8px;
            padding: 15px 20px;
            transition: top 0.5s ease-in-out;
            border-left: 5px solid #1b00ff;
        }
        #reminder-top-popup.show {
            top: 20px;
        }
        #reminder-top-popup h6 {
            margin-bottom: 5px;
            color: #1b00ff;
        }
        #reminder-top-popup .close-popup {
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
            font-size: 20px;
        }
    </style>
</head>
<body>
    @include('layouts.partials.header')
    
    <div class="main-container">
        <div class="pd-ltr-20">
            @yield('content')
            
            <div class="footer-wrap pd-20 mb-20">
                <div class="card-box pd-20" style="background: linear-gradient(135deg, #1b00ff 0%, #7d00ff 100%); color: white; border-radius: 12px; text-align: center;">
                    <span class="footer-text-bold" style="color: #fff; opacity: 0.9;">HLEAABSSYGAC</span>
                    <span style="margin: 0 10px; opacity: 0.5;">|</span>
                    <span class="footer-text-italic" style="color: #fff; font-weight: 300;">"I accept the risk. Anything can happen. My job is execution, not prediction."</span>
                    <span style="margin: 0 10px; opacity: 0.5;">|</span>
                    <span class="footer-text-bold" style="color: #fff; opacity: 0.9;">#IGWT - You're Great!</span>
                </div>
            </div>
        </div>
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
    
    <!-- Toastr -->
    <script src="{{ asset('iziToast/cdn.jsdelivr.net_npm_izitoast_dist_js_iziToast.min.js') }}"></script>
    <script>
        function iziToastNotify(type, message) {
            iziToast[type]({
                title: type.toUpperCase(),
                message: message,
                position: 'topRight'
            });
        }
    </script>
    
    <div id="reminder-top-popup">
        <span class="close-popup" onclick="document.getElementById('reminder-top-popup').classList.remove('show')">&times;</span>
        <h6 id="popup-title">Reminder</h6>
        <p id="popup-content" class="mb-0"></p>
    </div>

    <script src="{{ asset('deskapp/src/scripts/moment.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('deskapp/src/scripts/reminders.js') }}"></script>
    
    @stack('modals')
    @stack('scripts')
</body>
</html>
