<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $activeBusinessUnit->name ?? config('app.name', 'Laravel') }}</title>


    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon"
      href="{{ asset('storage/' . ltrim($activeBusinessUnit->favicon_path ?? 'assets/img/favicon/favicon.webp', '/')) }}">

    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js') }} in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/rs/dataTables.bootstrap5.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendor/rs/jquery-confirm.min.css') }}">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" /> -->
    {{-- RealtySuite theme: colours come from the business unit (navy + gold for KREA). --}}
    <style>
        :root {
            --rs-navy: {{ $activeBusinessUnit->theme_color ?? '#011e48' }};
            --rs-gold: {{ $activeBusinessUnit->secondary_color ?? '#c3b246' }};
            /* old names, still used by some pages */
            --theme-color: var(--rs-navy);
            --secondary-color: var(--rs-gold);
        }
    </style>

    
    <link rel="stylesheet" href="{{ asset('assets/css/realtysuite.css') }}?v={{ @filemtime(public_path('assets/css/realtysuite.css')) }}" />
</head>
<body class="rs-app">
    <div class="preloader" style="display: none">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
                @include('include.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                @include('include.topnav')
                @if (\Session::has('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {!! \Session::get('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (\Session::has('error'))
                    <div class="alert alert-danger  alert-dismissible" role="alert">
                        {!! \Session::get('error') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @yield('content')

                <!-- Footer -->
                @include('include.footer')
                <!-- / Footer -->
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <input type="hidden" id="base_url" value="{{ url('/') }}">
    <input type="hidden" id="asset_url" value="{{ asset('/') }}">
    <!-- Core JS -->
    
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/core.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>


    <script src="{{ asset('assets/vendor/rs/jquery.dataTables.min.js') }}"></script>
    <!-- <script src="{{ asset('assets/vendor/dataTable/dataTables.bootstrap4.min.js') }}"></script> -->
    <script src="{{ asset('assets/vendor/rs/dataTables.bootstrap5.min.js') }}"></script>

    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>-->
    <script src="{{ asset('assets/vendor/rs/jquery-confirm.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/rs/bootstrap-select.min.css') }}">
    <script src="{{ asset('assets/vendor/rs/bootstrap-select.min.js') }}"></script>

    <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script> -->
    @yield('script')
        <script>
            $('select:not(.raw-select)').selectpicker({
                'liveSearch' : true,
            });

            </script>
  </body>
</html>
