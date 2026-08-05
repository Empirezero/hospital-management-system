<!-- Topbar Start -->
<div class="container-fluid bg-dark px-5 d-none d-lg-block">
    <div class="row gx-0 align-items-center" style="height: 45px;">
        <div class="col-lg-8 text-center text-lg-start mb-lg-0">
            <div class="d-flex flex-wrap">
                <a href="#" class="text-light me-4"><i class="fas fa-map-marker-alt text-primary me-2"></i>Find A Location</a>
                <a href="#" class="text-light me-4"><i class="fas fa-phone-alt text-primary me-2"></i>+01234567890</a>
                <a href="#" class="text-light me-0"><i class="fas fa-envelope text-primary me-2"></i>Example@gmail.com</a>
            </div>
        </div>
        <div class="col-lg-4 text-center text-lg-end">
            <div class="d-flex align-items-center justify-content-end">
                <a href="#" class="btn btn-light btn-square border rounded-circle nav-fill me-3"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="btn btn-light btn-square border rounded-circle nav-fill me-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="btn btn-light btn-square border rounded-circle nav-fill me-3"><i class="fab fa-instagram"></i></a>
                <a href="#" class="btn btn-light btn-square border rounded-circle nav-fill me-0"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
</div>
<!-- Topbar End -->

<!-- Navbar & Hero Start -->
<div class="container-fluid position-relative p-0">
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-4 px-lg-5 py-3 py-lg-0">
        <a href="index.html" class="navbar-brand p-0">
            <h1 class="text-primary m-0"><i class="fas fa-star-of-life me-3"></i>Terapia</h1>
            <!-- <img src="{{ asset('frontend/img/logo.png') }}" alt="Logo"> -->
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="fa fa-bars"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto py-0">
                <a href="index.html" class="nav-item nav-link active">Home</a>
                <a href="about.html" class="nav-item nav-link">About</a>
                <a href="service.html" class="nav-item nav-link">Services</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu m-0">
                        <a href="appointment.html" class="dropdown-item">Appointment</a>
                        <a href="feature.html" class="dropdown-item">Features</a>
                        <a href="blog.html" class="dropdown-item">Our Blog</a>
                        <a href="team.html" class="dropdown-item">Our Team</a>
                        <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                        <a href="404.html" class="dropdown-item">404 Page</a>
                    </div>
                </div>
                <a href="contact.html" class="nav-item nav-link">Contact Us</a>
            </div>

            <!-- Show Dashboard if User is Authenticated, Otherwise show Login and Register -->
            @if(Auth::check())
            <a href="{{ url('/home') }}" class="btn btn-primary rounded-pill text-white py-2 px-4">Dashboard</a>
            <a href="{{ route('logout') }}" class="btn btn-outline-primary rounded-pill text-primary py-2 px-4 me-2"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
            @else
            <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill text-primary py-2 px-4 me-2">Login</a>
            <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill text-primary py-2 px-4 me-2">Register</a>
            @endif <a href="#" class="btn btn-primary rounded-pill text-white py-2 px-4">Book Appointment</a>
        </div>
    </nav>

    <!-- Carousel Start -->
    <div class="header-carousel owl-carousel">
        <div class="header-carousel-item">
            <img src="{{ asset('frontend/img/carousel-1.jpg') }}" class="img-fluid w-100" alt="Image">
            <div class="carousel-caption">
                <div class="carousel-caption-content p-3">
                    <h5 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">Physiotherapy Center</h5>
                    <h1 class="display-1 text-capitalize text-white mb-4">Best Solution For Painful Life</h1>
                    <p class="mb-5 fs-5">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                    </p>
                    <a class="btn btn-primary rounded-pill text-white py-3 px-5" href="#">Book Appointment</a>
                </div>
            </div>
        </div>
        <div class="header-carousel-item">
            <img src="{{ asset('frontend/img/carousel-2.jpg') }}" class="img-fluid w-100" alt="Image">
            <div class="carousel-caption">
                <div class="carousel-caption-content p-3">
                    <h5 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">Physiotherapy Center</h5>
                    <h1 class="display-1 text-capitalize text-white mb-4">Best Solution For Painful Life</h1>
                    <p class="mb-5 fs-5 animated slideInDown">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                    </p>
                    <a class="btn btn-primary rounded-pill text-white py-3 px-5" href="#">Book Appointment</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->
</div>
<!-- Navbar & Hero End -->