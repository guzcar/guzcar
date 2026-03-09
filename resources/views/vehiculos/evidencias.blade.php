<x-layout>
    <div style="max-width: 1100px;" class="mx-auto pb-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0 text-dark fw-bold">
                <i class="fa-solid fa-camera-retro text-primary me-2"></i> Evidencias del Trabajo
            </h1>
            <a class="btn btn-light border py-2 fw-semibold shadow-sm"
                href="{{ route('consulta.buscar.vehiculo', ['placa' => $trabajo->vehiculo->placa]) }}">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver a Consulta
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body p-4 d-flex flex-wrap justify-content-between">
                <div>
                    <span class="d-block small text-white-50 mb-1">Vehículo</span>
                    <span class="fw-bold fs-5">{{ $trabajo->vehiculo->placa }} -
                        {{ $trabajo->vehiculo->marca?->nombre }} {{ $trabajo->vehiculo->modelo?->nombre }}</span>
                </div>
                <div>
                    <span class="d-block small text-white-50 mb-1">Fecha de Ingreso</span>
                    <span class="fw-bold fs-5">{{ $trabajo->fecha_ingreso->isoFormat('DD/MM/YYYY hh:mm A') }}</span>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            @forelse ($evidencias as $evidencia)
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card shadow-sm h-100 border-0 overflow-hidden">
                        <div class="card-img-top bg-dark position-relative"
                            style="height: 250px; display: flex; align-items: center; justify-content: center;">
                            @if ($evidencia->tipo == 'imagen')
                                <a href="{{ Storage::url($evidencia->evidencia_url) }}" data-fancybox="gallery"
                                    data-caption="<b>{{ $evidencia->user->name }}</b><br/>{{ $evidencia->observacion ?? 'Sin descripción' }}"
                                    class="w-100 h-100 d-block">
                                    <img src="{{ Storage::url($evidencia->evidencia_url) }}" alt="Evidencia"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                </a>
                            @elseif ($evidencia->tipo == 'video')
                                <video controls style="width: 100%; height: 100%; object-fit: cover;">
                                    <source src="{{ asset('storage/' . $evidencia->evidencia_url) }}" type="video/mp4">
                                    Tu navegador no soporta la etiqueta de video.
                                </video>
                            @endif
                        </div>

                        <div class="card-body bg-white border-top p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                    style="width: 32px; height: 32px;">
                                    <i class="fa-solid fa-user small"></i>
                                </div>
                                <span class="fw-bold text-dark">{{ $evidencia->user->name }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3 text-muted small">
                                <i class="fa-regular fa-calendar-check me-2"></i>
                                <span>{{ $evidencia->created_at->isoFormat('dddd, D [de] MMMM YYYY - h:mm A') }}</span>
                            </div>

                            <div class="bg-light p-3 rounded border border-light-subtle flex-grow-1">
                                <span class="d-block text-secondary small fw-bold mb-1"><i
                                        class="fa-solid fa-align-left me-1"></i> Descripción:</span>
                                <span
                                    class="text-dark small">{{ $evidencia->observacion ?? 'Sin descripción técnica registrada.' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border shadow-sm text-center py-5">
                        <i class="fa-solid fa-images fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-dark fw-bold">Sin evidencias</h5>
                        <p class="mb-0 text-muted">Aún no se han subido fotos ni videos para este trabajo.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4 mb-5">
            {{ $evidencias->links('pagination::bootstrap-5') }}
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
        <script>
            Fancybox.bind("[data-fancybox]", {
                infinite: true,
                Thumbs: {
                    autoStart: true,
                },
            });
        </script>
    @endpush
</x-layout>