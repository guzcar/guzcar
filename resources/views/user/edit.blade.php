<x-layout>

    @if (session('success'))
        <div class="alert alert-light alert-dismissible border shadow-sm fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header">
                    Avatar
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if(auth()->user()->getFilamentAvatarUrl())
                            <img id="profile-preview" src="{{ auth()->user()->getFilamentAvatarUrl() }}" alt="Perfil"
                                class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                        @else
                            @php
                                $nameParts = explode(' ', auth()->user()->name);
                                $initials = '';
                                foreach ($nameParts as $part) {
                                    $initials .= strtoupper(substr($part, 0, 1)) . ' ';
                                }
                                $initials = rtrim($initials);
                            @endphp
                            <img id="profile-preview"
                                src="https://ui-avatars.com/api/?name={{ urlencode($initials) }}&background=09090b&color=ffffff"
                                alt="Perfil" class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                        @endif
                    </div>
                    <form action="{{ route('user.add-avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 text-center">
                            <input type="file" id="avatar_url" name="avatar_url"
                                class="form-control @error('avatar_url') is-invalid @enderror" accept="image/*"
                                onchange="previewImage(event)">
                            @error('avatar_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">Guardar Imagen</button>
                    </form>
                    <form action="{{ route('user.remove-avatar') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-light border w-100">Eliminar Imagen</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <form action="{{ route('user.update') }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-header">
                        Editar Perfil
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_old" class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control @error('password_old') is-invalid @enderror"
                                id="password_old" name="password_old">
                            @error('password_old')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_new" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control @error('password_new') is-invalid @enderror"
                                id="password_new" name="password_new">
                            @error('password_new')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="password_confirm" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control @error('password_confirm') is-invalid @enderror"
                                id="password_confirm" name="password_confirm">
                            @error('password_confirm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewImage(event) {
                const reader = new FileReader();
                const preview = document.getElementById('profile-preview');
                reader.onload = function () {
                    if (reader.readyState === 2) {
                        preview.src = reader.result;
                    }
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        </script>
    @endpush
</x-layout>