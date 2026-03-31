<div class="container-fluid appointment py-5">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.2">
                <div class="section-title text-start">
                    <h4 class="sub-title pe-3 mb-0">Solutions To Your Pain</h4>
                    <h1 class="display-4 mb-4">Best Quality Services With Minimal Pain Rate</h1>
                    <p class="mb-4">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quaerat deleniti amet at atque sequi quibusdam cumque itaque repudiandae temporibus, eius nam mollitia voluptas maxime veniam necessitatibus saepe in ab? Repellat!</p>
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="d-flex flex-column h-100">
                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fa fa-check text-primary me-2"></i> Body Relaxation</h5>
                                    <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Et deserunt qui cupiditate veritatis enim ducimus.</p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fa fa-check text-primary me-2"></i> Body Relaxation</h5>
                                    <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Et deserunt qui cupiditate veritatis enim ducimus.</p>
                                </div>
                                <div class="text-start mb-4">
                                    <a href="#" class="btn btn-primary rounded-pill text-white py-3 px-5">More Details</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="video h-100">
                                <img src="img/video-img.jpg" class="img-fluid rounded w-100 h-100" style="object-fit: cover;" alt="">
                                <button type="button" class="btn btn-play" data-bs-toggle="modal" data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-bs-target="#videoModal">
                                    <span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.4s">
                <div class="appointment-form rounded p-5">
                    <p class="fs-4 text-uppercase text-primary">Get In Touch</p>
                    <h1 class="display-5 mb-4">Get Appointment</h1>
                    @if(session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session()->get('message') }}
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <script>
                        setTimeout(function() {
                            document.querySelector('.alert').style.display = 'none';
                        }, 5000); // Hide after 5 seconds
                    </script>
                    @endif
                    <form action="{{ url('appointment') }}" method="POST">
                        @csrf
                        <div class="row gy-3 gx-4">

                            <div class="col-md-6">
                                <input type="text" class="form-control py-3 border-primary bg-transparent" placeholder="First Name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control py-3 border-primary bg-transparent" placeholder="Email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <input type="tel" class="form-control py-3 border-primary bg-transparent" placeholder="Phone" name="number" required>
                            </div>

                            <div class="col-md-6">
                                <input type="date" name="date" class="form-control py-3 border-primary bg-transparent" required>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select py-3 border-primary bg-transparent" aria-label="Default select example" name="doctor" required>
                                    <option selected disabled>Select Doctor</option>
                                    @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->name }} --Speciality-- {{ $doctor->speciality }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <textarea class="form-control border-primary bg-transparent" name="message" id="area-text" cols="30" rows="5" placeholder="Write Comments"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary  w-100 py-3 px-5">SUBMIT NOW</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for video -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Youtube Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <iframe class="embed-responsive-item" src="" id="video" allowfullscreen allowscriptaccess="always" allow="autoplay"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>