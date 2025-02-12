<x-base>

    @push('styles')
        <style>
            body {
                background-image: url('{{ asset('images/back-guzcar.jpg') }}');
                background-size: cover;
                background-position: center center;
                background-repeat: no-repeat;
                height: 100vh;
            }
        </style>
    @endpush

    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card">
                        <div class="card-header text-center py-4">
                            <h5 class="font-weight-light mb-0">Automotores <b>GUZCAR</b></h5>
                            <p class="mb-0">Ingrese su correo electrónico</p>
                        </div>
                        <div class="card-header text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="fa-solid fa-arrow-left me-1"></i>
                                Volver al inicio de sesión
                            </a>
                        </div>

                        <div class="card-body">

                            @if (session('status'))
                                <div class="alert alert-light border alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <div class="form-floating mb-3">
                                    <input id="email" class="form-control @error('email') is-invalid @enderror"
                                        type="email" name="email" value="{{ old('email') }}" required
                                        placeholder="name@example.com" autocomplete="email" autofocus>
                                    <label for="email">Correo electrónico</label>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <button type="submit" class="py-3 btn btn-primary w-100">
                                    Enviar email
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base>