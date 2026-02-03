<x-layout>
    <h1 class="mb-3">Mis Maletas Asignadas</h1>

    <div class="row">
        @forelse($maletas as $maleta)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="display-4 text-secondary me-3">
                                <i class="fa-solid fa-briefcase"></i>
                            </div>
                            <div>
                                <h5 class="card-title fw-bold text-primary mb-0">{{ $maleta->codigo }}</h5>
                                <small class="text-muted">Asignada a: {{ $maleta->propietario->name ?? 'Mí' }}</small>
                            </div>
                        </div>
                        
                        <p class="card-text text-secondary">
                            {{ $maleta->observacion ? Str::limit($maleta->observacion, 50) : 'Sin observaciones registradas.' }}
                        </p>

                        <div class="d-grid">
                            <a href="{{ route('maletas.show', $maleta) }}" class="btn btn-primary">
                                <i class="fa-solid fa-eye me-1"></i> Ver Herramientas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle me-2"></i> No tienes maletas asignadas actualmente.
                </div>
            </div>
        @endforelse
    </div>
</x-layout>