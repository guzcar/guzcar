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
                            <p class="mb-0">{{ __('Reset Password') }}</p>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf

                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="form-floating mb-3">
                                    <input class="form-control @error('email') is-invalid @enderror" type="email"
                                        id="email" name="email" value="{{ $email ?? old('email') }}"
                                        placeholder="Correo electrónico" required readonly>
                                    <label for="email">Correo electrónico</label>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-floating mb-3">
                                    <input class="form-control @error('password') is-invalid @enderror" type="password"
                                        id="password" name="password" placeholder="Contraseña" required
                                        autocomplete="new-password" autofocus>
                                    <label for="password">Contraseña</label>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-floating mb-3">
                                    <input class="form-control" type="password" id="password-confirm"
                                        name="password_confirmation" placeholder="Confirmar contraseña" required
                                        autocomplete="new-password">
                                    <label for="password-confirm">Confirmar contraseña</label>
                                </div>

                                <button type="submit" class="btn btn-primary py-3 w-100">
                                    {{ __('Reset Password') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base>