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
    <link rel="stylesheet" href="{{ asset("assets/vendor/fonts/boxicons.css") }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset("assets/vendor/css/core.css") }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset("assets/vendor/css/theme-default.css") }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset("assets/css/demo.css") }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset("assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css") }}" />

    <link rel="stylesheet" href="{{ asset("assets/vendor/libs/apex-charts/apex-charts.css") }}" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset("assets/vendor/js/helpers.js") }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js") }} in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset("assets/js/config.js") }}"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" /> -->
    <style>
        .menu .app-brand.demo {
            height: 120px;
            margin-top: 12px;
        }
          :root {
            --theme-color: {{ $activeBusinessUnit->theme_color ?? '#7367F0' }};
            --secondary-color: {{ $activeBusinessUnit->secondary_color ?? '#28C76F' }};
        }

        /* Apply colors dynamically */
        .btn-primary,
        .bg-primary,
        .navbar {
            background-color: var(--theme-color) !important;
            border-color: var(--theme-color) !important;
        }

        .text-primary {
            color: var(--theme-color) !important;
        }

        .btn-secondary,
        .bg-secondary {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
        }

        .text-secondary {
            color: var(--secondary-color) !important;
        }

        /* Sidebar hover or highlight */
        .menu-inner .menu-item:hover > a {
            background-color: rgba(0, 0, 0, 0.05);
            color: var(--theme-color) !important;
        }

        /* Topbar border or accent */
        .layout-navbar {
            border-bottom: 3px solid var(--theme-color);
        }
        
            h6, .h6, 
            h5, .h5, 
            h4, .h4, 
            h3, .h3, 
            h2, .h2, 
            h1, .h1 {
                margin-top: 0;
                margin-bottom: 1rem;
                font-weight: 500;
                line-height: 1.1;
                color: {{ $activeBusinessUnit->secondary_color ?? '#6c757d' }};
            }
    </style>
    @if(isset($activeBusinessUnit))
    <style>
    :root {
        --theme-color: {{ $activeBusinessUnit->theme_color ?? '#7367F0' }};
        --secondary-color: {{ $activeBusinessUnit->secondary_color ?? '#28C76F' }};
    }

    /* Sidebar background and hover styles */
    #layout-menu {
        background-color: var(--theme-color) !important;
    }

    /* Sidebar text, icons, and links */
    #layout-menu .menu-item a,
    #layout-menu .menu-item .menu-icon {
        color: #fff !important;
    }

    /* Hover and active menu items */
    #layout-menu .menu-item.active > a,
    #layout-menu .menu-item a:hover {
        background-color: var(--secondary-color) !important;
        color: #fff !important;
    }

    /* Menu toggler icon hover */
    #layout-menu .layout-menu-toggle i:hover {
        color: var(--secondary-color);
    }

    /* Submenu background */
    #layout-menu .menu-sub {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Highlight the active open section */
    #layout-menu .menu-item.open > a {
        background-color: var(--secondary-color) !important;
    }

    /* Scrollbar and shadow (optional aesthetic tweak) */
    .menu-inner-shadow {
        box-shadow: inset 0 -2px 5px rgba(0, 0, 0, 0.1);
    }
    </style>
    @endif

    
</head>
<body>
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
    <script src="{{ asset("assets/vendor/js/core.js") }}"></script>
    <script src="{{ asset("assets/vendor/libs/jquery/jquery.js") }}"></script>
    <script src="{{ asset("assets/js/custom.js") }}"></script>
    <script src="{{ asset("assets/vendor/libs/popper/popper.js") }}"></script>
    <script src="{{ asset("assets/vendor/js/bootstrap.js") }}"></script>
    <script src="{{ asset("assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js") }}"></script>

    <script src="{{ asset("assets/vendor/js/menu.js") }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset("assets/vendor/libs/apex-charts/apexcharts.js") }}"></script>

    <!-- Main JS -->
    <script src="{{ asset("assets/js/main.js") }}"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <script src="{{ asset('assets/vendor/dataTable/jquery.dataTables.min.js') }}"></script>
    <!-- <script src="{{ asset('assets/vendor/dataTable/dataTables.bootstrap4.min.js') }}"></script> -->
   <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

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
