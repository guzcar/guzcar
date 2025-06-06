<x-layout>

    <h1 class="mb-3">Evidencias para {{ $trabajo->vehiculo->placa }}</h1>

    <div class="d-flex justify-content-between mb-3">
        <a class="btn btn-light border py-2" href="{{ route('home') }}">Volver</a>
        <button type="button" class="btn btn-primary py-2" data-bs-toggle="modal" data-bs-target="#nuevaEvidencia">
            Subir Evidencias
        </button>
    </div>

    <ul class="list-group mb-3">
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Tipo</span>
                <span>{{ $trabajo->vehiculo->tipoVehiculo->nombre }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Marca</span>
                <span>{{ $trabajo->vehiculo->marca?->nombre }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Modelo</span>
                <span>{{ $trabajo->vehiculo->modelo?->nombre }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Color</span>
                <span>{{ $trabajo->vehiculo->color }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Ingreso</span>
                <span>{{ $trabajo->fecha_ingreso->isoFormat('dddd, D [de] MMMM') }}</span>
            </div>
        </li>
        @if ($trabajo->fecha_salida)
            <li class="list-group-item">
                <div class="d-flex">
                    <span class="fw-bold" style="min-width: 100px;">Salida</span>
                    <span class="text-danger">
                        {{ $trabajo->fecha_salida->isoFormat('dddd, D [de] MMMM') }}
                    </span>
                </div>
            </li>
        @endif
    </ul>

    @error('evidencias')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <p class="my-0">{{ $message }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @enderror

    <div class="card">
        <div class="card-body p-0 border-bottom-0">
            <div class="table-responsive">
                <table class="table mb-0 table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="py-3">Evidencia</th>
                            <th class="py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($evidencias as $evidencia)
                            <tr>
                                <td>
                                    @if ($evidencia->tipo === 'imagen')
                                        <img src="{{ Storage::url($evidencia->evidencia_url) }}" alt="Evidencia"
                                            class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <a href="{{ Storage::url($evidencia->evidencia_url) }}" target="_blank"
                                            class="btn btn-light border d-flex justify-content-center align-items-center"
                                            style="width: 60px; height: 60px;">
                                            <i class="fa-solid fa-video fs-3 text-muted"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-editar" 
                                        data-id="{{ $evidencia->id }}" 
                                        data-observacion="{{ $evidencia->observacion }}"
                                        data-url="{{ route('gestion.evidencias.update', [$trabajo, $evidencia]) }}"
                                        style="width: 60px; height: 60px;">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-eliminar" 
                                        data-id="{{ $evidencia->id }}" 
                                        data-url="{{ route('gestion.evidencias.destroy', [$trabajo, $evidencia]) }}"
                                        style="width: 60px; height: 60px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-secondary py-5" colspan="2">
                                    <i class="fa-regular fa-circle-xmark fs-1 mb-3"></i>
                                    <p class="mb-0">No hay evidencias registradas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($evidencias->hasPages())
            <div class="card-footer border-top-0">
                <div class="d-flex justify-content-center mt-3">
                    {{ $evidencias->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>

    {{-- Modal para crear nueva evidencia --}}
    <div class="modal fade" id="nuevaEvidencia" tabindex="-1" aria-labelledby="nuevaEvidenciaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('gestion.evidencias.store', $trabajo) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevaEvidenciaLabel">Nueva Evidencia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="evidencias" class="form-label">
                                Subir Evidencias
                                <span class="text-muted">(Máximo 5)</span>
                            </label>
                            <input type="file" name="evidencias[]" class="form-control" id="evidencias" multiple
                                required>
                        </div>
                        <div>
                            <label for="observacion" class="form-label">Observación</label>
                            <textarea name="observacion" class="form-control" id="observacion" rows="5"
                                placeholder="Escribe tu observación aquí"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Subir Evidencias</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para editar evidencia --}}
    <div class="modal fade" id="editarEvidenciaModal" tabindex="-1" aria-labelledby="editarEvidenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editarEvidenciaForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarEvidenciaModalLabel">Editar Evidencia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label for="observacionEditar" class="form-label">Observación</label>
                            <textarea name="observacion" class="form-control" id="observacionEditar" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para eliminar evidencia --}}
    <div class="modal fade" id="eliminarEvidenciaModal" tabindex="-1" aria-labelledby="eliminarEvidenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="eliminarEvidenciaForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="eliminarEvidenciaModalLabel">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas eliminar esta evidencia de manera permanente?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Manejar el foco al cerrar los modales
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    modal.addEventListener('hidden.bs.modal', function () {
                        const triggerButton = document.querySelector(`[data-bs-target="#${modal.id}"]`);
                        if (triggerButton) {
                            triggerButton.focus(); // Devuelve el foco al botón que abrió el modal
                        }
                    });
                });

                // Manejar la edición de evidencias
                document.querySelectorAll('.btn-editar').forEach(button => {
                    button.addEventListener('click', function () {
                        const id = this.getAttribute('data-id');
                        const observacion = this.getAttribute('data-observacion');
                        const url = this.getAttribute('data-url');

                        document.getElementById('observacionEditar').value = observacion;
                        document.getElementById('editarEvidenciaForm').action = url;

                        const modal = new bootstrap.Modal(document.getElementById('editarEvidenciaModal'));
                        modal.show();
                    });
                });

                // Manejar la eliminación de evidencias
                document.querySelectorAll('.btn-eliminar').forEach(button => {
                    button.addEventListener('click', function () {
                        const url = this.getAttribute('data-url');

                        document.getElementById('eliminarEvidenciaForm').action = url;

                        const modal = new bootstrap.Modal(document.getElementById('eliminarEvidenciaModal'));
                        modal.show();
                    });
                });
            });
        </script>
    @endpush

</x-layout>