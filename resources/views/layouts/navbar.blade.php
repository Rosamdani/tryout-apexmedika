<nav class="navbar navbar-expand-lg navbar-light shadow" style="background-color: white;">
    <div class="container-fluid mx-5 d-flex justify-content-between">
        <ul class="navbar-nav gap-3 align-items-center d-flex flex-row">
            <li>
                <button class="navbar-toggler border-0 d-flex d-lg-flex d-md-flex d-sm-flex"
                    style="display: flex!important;" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </li>
            <li><a href="#" class="nav-link">Beli UKMPPD</a></li>
            <li style="border: 1px solid #B9B9B9FF; height: 50px;"></li>
            <li><a href="#" class="nav-link">Beli Expert Class</a></li>
            <li style="border: 1px solid #B9B9B9FF; height: 50px;"></li>
            <li><a href="#" class="nav-link">Beli Pre-Internship</a></li>
        </ul>
        <div class="d-flex align-items-center gap-3">
            @auth
            <div style="color: #199501FF;">{{ strtoupper(Auth::user()->name) }}</div>
            <img src="https://ui-avatars.com/api/?name={{ substr(Auth::user()->name, 0, 1) == ' ' ? substr(Auth::user()->name, 0, 2) : substr(Auth::user()->name, 0, 1) }}&color=FFFFFF&background=09090b"
                alt="{{ Auth::user()->name}}" class="rounded-circle shadow-sm" style="width: 40px; height: 40px;">
            @endauth

            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-chevron-down" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
            </svg>
        </div>
    </div>
</nav>