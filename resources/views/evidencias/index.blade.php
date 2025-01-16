<x-layout>
    <h1 class="mb-3">Evidencias para {{ $trabajo->vehiculo->placa }}</h1>
    <div class="d-flex justify-content-between mb-3">
        <a class="btn btn-light border" href="{{ route('home') }}">Volver</a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaEvidencia">
            Subir evidencia
        </button>
    </div>
    <ul class="list-group mb-3">
    <li class="list-group-item">
        <div class="d-flex">
            <span class="fw-bold" style="width: 100px;">Marca</span>
            <span>{{ $trabajo->vehiculo->marca }}</span>
        </div>
    </li>
    <li class="list-group-item">
        <div class="d-flex">
            <span class="fw-bold" style="width: 100px;">Modelo</span>
            <span>{{ $trabajo->vehiculo->modelo }}</span>
        </div>
    </li>
    <li class="list-group-item">
        <div class="d-flex">
            <span class="fw-bold" style="width: 100px;">Color</span>
            <span>{{ $trabajo->vehiculo->color }}</span>
        </div>
    </li>
    <li class="list-group-item">
        <div class="d-flex">
            <span class="fw-bold" style="width: 100px;">Tipo</span>
            <span>{{ $trabajo->vehiculo->tipoVehiculo->nombre }}</span>
        </div>
    </li>
</ul>

    <div class="card">
        <div class="card-body p-0">
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
                                            class="btn btn-secondary d-flex justify-content-center align-items-center"
                                            style="width: 60px; height: 60px;">
                                            <i class="fa-solid fa-play"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editarEvidencia{{ $evidencia->id }}"
                                        style="width: 60px; height: 60px;">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#eliminarEvidencia{{ $evidencia->id }}"
                                        style="width: 60px; height: 60px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- Modal para eliminar --}}
                            <div class="modal fade" id="eliminarEvidencia{{ $evidencia->id }}" tabindex="-1"
                                aria-labelledby="eliminarEvidenciaLabel{{ $evidencia->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('evidencias.destroy', [$trabajo, $evidencia]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="eliminarEvidenciaLabel{{ $evidencia->id }}">
                                                    Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estás seguro de que deseas eliminar esta evidencia de manera permanente?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal para editar --}}
                            <div class="modal fade" id="editarEvidencia{{ $evidencia->id }}" tabindex="-1"
                                data-bs-backdrop="static" aria-labelledby="editarEvidenciaLabel{{ $evidencia->id }}"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('evidencias.update', [$trabajo, $evidencia]) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editarEvidenciaLabel{{ $evidencia->id }}">Editar
                                                    Evidencia</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="evidencia{{ $evidencia->id }}" class="form-label">Actualizar
                                                        Evidencia</label>
                                                    <div class="preview-container text-center border rounded"
                                                        style="position: relative; cursor: pointer;">
                                                        <input type="file" name="evidencia"
                                                            class="form-control evidencia-input"
                                                            id="evidencia{{ $evidencia->id }}" accept="image/*,video/*"
                                                            style="opacity: 0; position: absolute; height: 100%; width: 100%; cursor: pointer;">
                                                        <div id="preview-{{ $evidencia->id }}"
                                                            class="d-flex flex-column justify-content-center align-items-center"
                                                            style="min-height: 150px;">
                                                            @if ($evidencia->tipo === 'video')
                                                                <i class="fa-solid fa-video fa-3x text-muted"></i>
                                                                <p class="text-muted mt-2 mb-0">Haz clic para editar la evidencia
                                                                </p>
                                                            @else
                                                                <img src="{{ Storage::url($evidencia->evidencia_url) }}"
                                                                    alt="Previsualización" class="img-fluid"
                                                                    style="max-width: 200px;">
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="observacion{{ $evidencia->id }}"
                                                        class="form-label">Actualizar Observación</label>
                                                    <textarea name="observacion" class="form-control"
                                                        id="observacion{{ $evidencia->id }}"
                                                        rows="3">{{ $evidencia->observacion }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
    </div>

    {{-- Modal para crear nueva evidencia --}}
    <div class="modal fade" id="nuevaEvidencia" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="nuevaEvidenciaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('evidencias.store', $trabajo) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevaEvidenciaLabel">Nueva Evidencia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="evidencia" class="form-label">Subir Evidencia</label>
                            <div class="preview-container text-center border rounded"
                                style="position: relative; cursor: pointer;">
                                <input type="file" name="evidencia" class="form-control evidencia-input"
                                    id="nueva-evidencia" accept="image/*,video/*"
                                    style="opacity: 0; position: absolute; height: 100%; width: 100%; cursor: pointer;"
                                    required>
                                <div id="preview-nueva"
                                    class="d-flex flex-column justify-content-center align-items-center"
                                    style="min-height: 150px;">
                                    <i class="fa-solid fa-image fa-3x text-muted"></i>
                                    <p class="text-muted mt-2 mb-0">Haz clic para subir una evidencia</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="observacion" class="form-label">Observación</label>
                            <textarea name="observacion" class="form-control" id="observacion" rows="5"
                                placeholder="Escribe tu observación aquí"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Subir Evidencia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const handlePreview = (input, previewContainerId) => {
                    const file = input.files[0];
                    const previewContainer = document.getElementById(previewContainerId);

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            if (file.type.startsWith('image/')) {
                                previewContainer.innerHTML = `<img src="${e.target.result}" class="img-fluid" style="max-width: 200px;">`;
                            } else if (file.type.startsWith('video/')) {
                                previewContainer.innerHTML = `<i class="fa-solid fa-video fa-3x text-muted"></i>`;
                            } else {
                                previewContainer.innerHTML = `<i class="fa-solid fa-file fa-3x text-muted"></i>`;
                            }
                        };
                        reader.readAsDataURL(file);
                    } else {
                        previewContainer.innerHTML = `
                        <i class="fa-solid fa-image fa-3x text-muted"></i>
                        <p class="text-muted mt-2 mb-0">Haz clic para subir una evidencia</p>
                    `;
                    }
                };

                // Modal de "Nuevo"
                const nuevaEvidenciaInput = document.getElementById('nueva-evidencia');
                nuevaEvidenciaInput.addEventListener('change', function () {
                    handlePreview(this, 'preview-nueva');
                });

                // Modal de "Edición"
                const evidenciaInputs = document.querySelectorAll('.evidencia-input');
                evidenciaInputs.forEach(input => {
                    const id = input.id.split('evidencia')[1];
                    input.addEventListener('change', function () {
                        handlePreview(this, `preview-${id}`);
                    });
                });
            });
        </script>
    @endpush

</x-layout>
