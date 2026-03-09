<x-layout>
    <div style="max-width: 1100px;" class="mx-auto pb-5">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h1 class="mb-0 text-dark fw-bold">
                <i class="fa-solid fa-file-lines text-secondary me-2"></i> Descripciones Técnicas
            </h1>
            <a class="btn btn-light border py-2 fw-semibold shadow-sm"
                href="{{ route('consulta.buscar.vehiculo', ['placa' => $trabajo->vehiculo->placa]) }}">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver a Consulta
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-4 bg-secondary text-white">
            <div class="card-body p-4 d-flex flex-wrap justify-content-between gap-3">
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
            @forelse ($detalles as $detalle)
                <div class="col-lg-6 mb-3">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body bg-white p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                    style="width: 32px; height: 32px;">
                                    <i class="fa-solid fa-user-gear small"></i>
                                </div>
                                <span class="fw-bold text-dark">{{ $detalle->user->name }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3 text-muted small pb-3 border-bottom">
                                <i class="fa-regular fa-calendar-check me-2"></i>
                                <span
                                    class="text-capitalize">{{ $detalle->created_at->isoFormat('dddd, D [de] MMMM YYYY - h:mm A') }}</span>
                            </div>

                            <div class="descripcion-html text-dark flex-grow-1">
                                {!! $detalle->descripcion !!}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border shadow-sm text-center py-5">
                        <i class="fa-solid fa-file-circle-xmark fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-dark fw-bold">Sin descripciones registradas</h5>
                        <p class="mb-0 text-muted">Aún no se han registrado anotaciones o descripciones técnicas para este
                            trabajo en el taller.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4 mb-5">
            {{ $detalles->links('pagination::bootstrap-5') }}
        </div>

    </div>

    @push('styles')
        <style>
            /* Ajustes específicos para el contenido HTML incrustado del editor de Filament */
            .descripcion-html img {
                max-width: 100%;
                height: auto;
                border-radius: 0.375rem;
                margin-top: 0.5rem;
                margin-bottom: 0.5rem;
            }

            .descripcion-html table {
                width: 100%;
                margin-bottom: 1rem;
                border-collapse: collapse;
            }

            .descripcion-html table th,
            .descripcion-html table td {
                border: 1px solid #dee2e6;
                padding: 0.5rem;
            }

            .descripcion-html blockquote {
                border-left: 4px solid #e9ecef;
                padding-left: 1rem;
                color: #6c757d;
                font-style: italic;
                background-color: #f8f9fa;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }

            .descripcion-html pre {
                background: #f8f9fa;
                padding: 1rem;
                border-radius: 0.375rem;
                overflow: auto;
                border: 1px solid #dee2e6;
            }

            .descripcion-html p:last-child {
                margin-bottom: 0;
            }

            .descripcion-html ul,
            .descripcion-html ol {
                padding-left: 1.5rem;
                margin-bottom: 1rem;
            }
        </style>
    @endpush
</x-layout>