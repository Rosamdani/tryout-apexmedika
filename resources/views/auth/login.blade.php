<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('assets/css/index.css') }}">
</head>

<body class="primary-background">

    <div class="mx-2 mx-md-4 py-5" style="height: 100vh;">
        <div class="row h-100 relative">
            <div class="col-7 d-none d-md-block">
                <img src="{{ asset('assets/images/login.png') }}" alt="login" class="h-100 w-100">
            </div>
            <div class="col-12 col-md-5 h-100 d-flex flex-column justify-content-between">
                <div class="row">
                    <h1 style="font-size: 3rem;">Selamat Datang Kembali!</h1>
                    <p>Mulailah mengerjakan tryoutmu!</p>
                </div>
                <div class="d-flex flex-column">
                    <form method="POST" action="{{ route('loginStore') }}" class="d-flex flex-column">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email"
                                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                style="border-radius: 10px;" value="rosamdani91@gmail.com" placeholder="Email">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" name="password" id="password"
                                class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                style="border-radius: 10px;" value="password" placeholder="Password">
                            <div class="d-flex justify-content-end">
                                <a href="" class="text-end text text-decoration-none">Lupa Password?</a>
                            </div>
                        </div>
                        <button type="submit" class="btn alternative-btn rounded-pill fw-bold mb-3">Login</button>
                        <button type="button" class="btn alternative-background rounded-pill fw-bold">Belum Punya
                            Akun? Daftar Sekarang!</button>
                    </form>
                </div>
                <div class="row">
                    <div class="col-4 d-flex justify-content-center nowrap"><a href="" class="text-center text">Tentang
                            Kami</a></div>
                    <div class="col-4 d-flex justify-content-center nowrap"><a href="" class="text-center text">Syarat
                            dan Ketentuan</a></div>
                    <div class="col-4 d-flex justify-content-center nowrap"><a href=""
                            class="text-center text">Kebijakan Privasi</a></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtHqNGTfbWV9yUzIz5O0F3RRrwJZS17tM0GcMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    @if ($errors->any())
    <script>
        let errors = @json($errors->all());
            errors.forEach(error => {
                toastr.error(error);
            });
    </script>
    @endif
    @if (session('error'))
    <script>
        toastr.error("{{ session('error') }}");
    </script>
    @endif
</body>

</html>