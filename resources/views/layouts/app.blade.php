<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Bettica Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('vendors/images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('vendors/images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('vendors/images/favicon-16x16.png') }}">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Core CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/core.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/icon-font.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('deskapp/src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('deskapp/src/plugins/datatables/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/style.css') }}">
    <link rel="stylesheet" href="{{ asset('iziToast/css/iziToast.min.css') }}">
    <style>

         #filter-summary {
            max-width: 100%;
            /* don’t overflow container */
            overflow-x: auto;
            /* enable horizontal scroll if needed */
            white-space: nowrap;
            /* keep everything in one line */
            -webkit-overflow-scrolling: touch;
            /* smooth scrolling on mobile */
        }

        #filter-summary::-webkit-scrollbar {
            height: 6px;
        }

        #filter-summary::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }

        .toast-success {
            background-color: #28a745 !important;
            color: #fff !important;
        }

        .toast-error {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .toast-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .toast-info {
            background-color: #17a2b8 !important;
            color: #fff !important;
        }

        #toast-success .iziToast-message,
        #toast-error .iziToast-message,
        #toast-warning .iziToast-message,
        #toast-info .iziToast-message {
            font-size: 1rem;
            /* increase font size */
            font-weight: 500;
            /* slightly bolder */
            line-height: 1.4;
        }

        /* Optional: enlarge title too */
        #toast-success .iziToast-title,
        #toast-error .iziToast-title,
        #toast-warning .iziToast-title,
        #toast-info .iziToast-title {
            font-size: 1.05rem;
            font-weight: 600;
        }

        /* Adjust icon alignment */
        .iziToast-icon {
            font-size: 1.2rem !important;
            /* make FA icon a bit bigger */
            margin-right: 8px;
        }

        .main-container {
            margin-left: 0 !important;
            padding-left: 0 !important;
        }

        .header {
            margin-left: 0 !important;
        }

        .pd-ltr-20 {
            padding-left: 20px;
            padding-right: 0px;
            /* keep a little padding */
        }

         .filter-summary {
            background: #f0f4ff;
            border: 1px solid #d0d7f5;
            color: #2c3e50;
            padding: 6px 12px;
            border-radius: 25px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            transition: 0.3s;
            cursor: default;
        }

        .filter-summary:hover {
            background: #e6edff;
            border-color: #a8b3f0;
        }

        .filter-summary .dw {
            font-size: 16px;
        }

        .header {
            left: 0 !important;
            width: 100% !important;
        }


        .toast-success {
            background-color: #28a745 !important;
            color: #fff !important;
        }

        .toast-error {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .toast-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .toast-info {
            background-color: #17a2b8 !important;
            color: #fff !important;
        }

        #toast-success .iziToast-message,
        #toast-error .iziToast-message,
        #toast-warning .iziToast-message,
        #toast-info .iziToast-message {
            font-size: 1rem;
            /* increase font size */
            font-weight: 500;
            /* slightly bolder */
            line-height: 1.4;
        }

        /* Optional: enlarge title too */
        #toast-success .iziToast-title,
        #toast-error .iziToast-title,
        #toast-warning .iziToast-title,
        #toast-info .iziToast-title {
            font-size: 1.05rem;
            font-weight: 600;
        }

        /* Adjust icon alignment */
        .iziToast-icon {
            font-size: 1.2rem !important;
            /* make FA icon a bit bigger */
            margin-right: 8px;
        }

        #trades_table.dataTable tbody tr {
            height: 32px;
            /* or auto */
        }

        /* Reduce cell padding */
        #trades_table.dataTable tbody td {
            padding: 4px 8px !important;
            line-height: 1.2;
            vertical-align: middle;
            /* font-size: 13px; */
        }

        /* Reduce header spacing */
        #trades_table.dataTable thead th {
            padding: 6px 8px !important;
            /* font-size: 13px; */
        }

        td.dt-control {
            cursor: pointer;
            text-align: center;
            width: 40px;
            /* keep it tight */
        }

        tr.shown td.dt-control::before {
            content: "−";
            background-color: #dc3545;
            /* red */
        }

        td.dt-control::before {
            content: "+";
            display: inline-block;
            width: 22px;
            height: 22px;
            line-height: 22px;
            border-radius: 50%;
            background-color: #007bff;
            /* blue */
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            transition: all 0.2s ease-in-out;
        }
    </style>


    @stack('styles')
</head>

<body>
    {{-- Loader --}}
    {{--  @include('partials.loader')
 --}}
    {{-- Header --}}



    @include('layouts.partials.header')

    {{-- Sidebar --}}
    @include('layouts.partials.sidebar')

    <div class="mobile-menu-overlay"></div>

    {{-- Main Content --}}
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('deskapp/vendors/scripts/core.js') }}"></script>
    <script src="{{ asset('deskapp/vendors/scripts/script.min.js') }}"></script>
    <script src="{{ asset('deskapp/vendors/scripts/process.js') }}"></script>
    <script src="{{ asset('deskapp/vendors/scripts/layout-settings.js') }}"></script>
    {{-- <script src="{{ asset('deskapp/src/plugins/apexcharts/apexcharts.min.js') }}"></script> --}}
    <script src="{{ asset('deskapp/src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('deskapp/src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('deskapp/src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('deskapp/src/plugins/datatables/js/responsive.bootstrap4.min.js') }}"></script>
	<script src="{{ asset('deskapp/src/plugins/datatables/js/dataTables.buttons.min.js') }}"></script>
	<script src="{{ asset('deskapp/src/plugins/datatables/js/buttons.bootstrap4.min.js') }}"></script>
	<script src="{{ asset('deskapp/src/plugins/datatables/js/buttons.print.min.js') }}"></script>
	<script src="{{ asset('deskapp/src/plugins/datatables/js/buttons.html5.min.js') }}"></script>
	<script src="{{ asset('deskapp/src/plugins/datatables/js/buttons.flash.min.js') }}"></script>
	<script src="{{ asset('deskapp/src/plugins/datatables/js/pdfmake.min.js') }}"></script>
	<script src="{{ asset('deskapp/src/plugins/datatables/js/vfs_fonts.js') }}"></script>
    {{-- <script src="{{ asset('deskapp/vendors/scripts/dashboard.js') }}"></script> --}}
    <script src="{{ asset('deskapp/vendors/scripts/datatable-setting.js') }}"></script>
    <script src="{{ asset('deskapp/src/scripts/moment.js') }}"></script>
    <script src="{{ asset('iziToast/js/iziToast.js') }}"></script>
    @stack('scripts')
    <script>
      /*   $(document).on('show.bs.modal', '.modal', function() {
            const $modal = $(this);
            $modal.attr('role', 'dialog');
            $modal.find('.modal-dialog').attr('role', 'document');
            $modal.find('.modal-header .close').each(function() {
                const $btn = $(this);
                if (!$btn.attr('aria-label')) {
                    $btn.attr('aria-label', 'Close');
                }
                $btn.find('span').attr('aria-hidden', 'true');
            });
            $modal.find('.modal-dialog').css('margin-top', '5vh');
        });
 */
        function iziToastNotify(type, message, title = '') {
            const icons = {
                success: 'dw-checked',
                error: 'dw-cancel',
                warning: 'dw-warning',
                info: 'dw-information'
            };
            iziToast.show({
                class: 'toast-' + type,
                title: title,
                message: message,
                theme: 'dark', // 'dark', 'light', 'dark-grey'
                color: type, // 'error', 'success', 'warning', 'info'
                icon: 'dw ' + icons[type] || 'dw-bell',
                position: 'topRight',
                backgroundColor: '',
                timeout: 4000, // auto close in 3 seconds
                close: true,
                progressBar: false,
                transitionIn: 'fadeInDown',
                transitionOut: 'fadeOutUp',
                pauseOnHover: true,
                animateInside: true,
                drag: true,
            });
        }
    </script>
</body>

</html>
