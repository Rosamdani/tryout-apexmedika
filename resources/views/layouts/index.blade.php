<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title')
    </title>
    <!-- Tambahkan Bootstrap CSS -->
    @include('layouts.header')
    @yield('style')
</head>

<body style="background: #e5e5e5">

    <!-- Navbar Bootstrap -->
    @if (!request()->routeIs('tryout.tes'))
    @include('layouts.navbar')
    @endif

    <!-- Konten Halaman -->
    <div class="container mt-3">
        @yield('content')
    </div>


    <div id="loading-overlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.8); z-index: 9999; text-align: center; justify-content: center; align-items: center;">
        <div class="d-flex h-100 justify-content-center align-items-center">
            <div class="spinner-border" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    @include('layouts.vendor-script')
    <script>
        function showLoading() {
            $('#loading-overlay').show();
        }

        function hideLoading() {
            $('#loading-overlay').hide();
        }
    </script>
    @yield('script')
</body>

</html>