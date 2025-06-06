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
                <div class="col-md-8 col-lg-6 col-xl-5">
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
                                <label for="inputEmail" class="mb-2">Correo electrónico</label>
                                <div class="mb-3">
                                    <input class="form-control" style="height: 3.5rem;" type="email" id="inputEmail"
                                        placeholder="ejemplo@guzcar.com" name="email" value="{{ old('email') }}"
                                        required autofocus>
                                </div>
                                <label for="inputEmail" class="mb-2">Contraseña</label>
                                <div class="input-group mb-3">
                                    <input class="form-control" style="height: 3.5rem;" id="inputPassword" value="{{ old('password') }}"
                                        type="password" name="password" placeholder="Contraseña" minlength="8" required>
                                    <span class="input-group-text" id="togglePassword"
                                        style="width:3.5rem; cursor: pointer">
                                        <i class="fa fa-eye-slash mx-auto fs-5"></i>
                                    </span>
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('inputPassword');
                const eyeIcon = togglePassword.querySelector('i');

                togglePassword.addEventListener('click', function () {
                    if (passwordInput.type === "password") {
                        passwordInput.type = "text";
                        eyeIcon.classList.remove("fa-eye-slash");
                        eyeIcon.classList.add("fa-eye");
                    } else {
                        passwordInput.type = "password";
                        eyeIcon.classList.remove("fa-eye");
                        eyeIcon.classList.add("fa-eye-slash");
                    }
                });
            });
        </script>
    @endpush
</x-base>