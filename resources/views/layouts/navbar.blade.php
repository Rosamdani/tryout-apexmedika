<nav class="navbar navbar-expand-lg navbar-light shadow" style="background-color: white;">
    <div class="container-fluid mx-1 mx-md-5 d-flex justify-content-between">
        <ul class="navbar-nav gap-3 align-items-center d-flex flex-row">
            <li>
                <button class="navbar-toggler border-0 d-flex d-lg-flex d-md-flex d-sm-flex"
                    style="display: flex!important;" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </li>
            <li><a href="#" class="nav-link d-none d-md-block">Beli UKMPPD</a></li>
            <li class="d-none d-md-block" style="border: 1px solid #B9B9B9FF; height: 50px;"></li>
            <li><a href="#" class="nav-link d-none d-md-block">Beli Expert Class</a></li>
            <li class="d-none d-md-block" style="border: 1px solid #B9B9B9FF; height: 50px;"></li>
            <li><a href="#" class="nav-link d-none d-md-block">Beli Pre-Internship</a></li>
        </ul>
        <div class="dropdown">
            <button class="btn dropdown-toggle d-flex align-items-center gap-3" type="button" id="dropdownMenuButton1"
                data-bs-toggle="dropdown" aria-expanded="false">
                @auth
                <div style="color: #199501FF;">{{ strtoupper(Auth::user()->name) }}</div>
                <img src="https://ui-avatars.com/api/?name={{ substr(Auth::user()->name, 0, 1) == ' ' ? substr(Auth::user()->name, 0, 2) : substr(Auth::user()->name, 0, 1) }}&color=FFFFFF&background=09090b"
                    alt="{{ Auth::user()->name}}" class="rounded-circle shadow-sm" style="width: 40px; height: 40px;">
                @endauth

            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="{{ route('logout')}}">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>