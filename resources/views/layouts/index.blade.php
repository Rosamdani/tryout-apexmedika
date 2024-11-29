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
</head>

<body class="bg-light">

    <!-- Navbar Bootstrap -->
    @if (!request()->routeIs('tryout.tes'))
    @include('layouts.navbar')
    @endif

    <!-- Konten Halaman -->
    <div class="container mt-3">
        @yield('content')
    </div>

    @include('layouts.vendor-script')
    @yield('script')
</body>

</html>