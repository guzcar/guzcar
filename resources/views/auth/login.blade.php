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
                    <div class="card shadow rounded-lg">
                        <div class="card-header text-center py-4">
                            <h5 class="font-weight-light mb-0">Automotores <b>GUZCAR</b></h5>
                        </div>
                        <div class="card-body py-4 px-4">
                            @if (session('status'))
                                <div class="alert alert-light border alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <p class="my-0">{{ $error }}</p>
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            <form action="{{ route('login.process') }}" method="POST">
                                @csrf
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="email" id="inputEmail" name="email"
                                        value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
                                    <label for="inputEmail">Correo electrónico</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="password" id="inputPassword" name="password"
                                        placeholder="Password" minlength="8" required>
                                    <label for="inputPassword">Contraseña</label>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" id="inputRememberPassword" type="checkbox"
                                                value="" name="remember" />
                                            <label class="form-check-label"
                                                for="inputRememberPassword">Recordarme</label>
                                        </div>
                                    </div>
                                    <div class="col-8 text-end">
                                        <a href="{{ route('password.request') }}">
                                            ¿Ha olvidado su contraseña?
                                        </a>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-primary py-3" type="submit">
                                        Iniciar Sesión
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center py-3">
                            <a class="text-decoration-none" href="{{ route('filament.admin.auth.login') }}">
                                Ingresar como Administrador <i class="fa-solid fa-user-shield"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base>