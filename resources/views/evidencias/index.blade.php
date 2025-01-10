<x-layout>
    <h1 class="mb-3">Evidencias para {{ $trabajo->vehiculo->placa }}</h1>
    <div class="d-flex justify-content-between mb-3">
        <a class="btn btn-secondary" href="{{ route('home') }}">Volver</a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaEvidencia">
            Subir evidencia
        </button>
    </div>
    <ul class="list-group mb-3">
        <li class="list-group-item"><b>Marca: </b>{{ $trabajo->vehiculo->marca }}</li>
        <li class="list-group-item"><b>Modelo: </b>{{ $trabajo->vehiculo->modelo }}</li>
        <li class="list-group-item"><b>Color: </b>{{ $trabajo->vehiculo->color }}</li>
        <li class="list-group-item"><b>Tipo: </b>{{ $trabajo->vehiculo->tipoVehiculo->nombre }}</li>
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
                                <!-- Columna para evidencia -->
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

                                <!-- Columna para acciones -->
                                <td>
                                    <!-- Botón para editar -->
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editarEvidencia{{ $evidencia->id }}"
                                        style="width: 60px; height: 60px;">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <!-- Botón para eliminar -->
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#eliminarEvidencia{{ $evidencia->id }}"
                                        style="width: 60px; height: 60px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal para eliminar -->
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
                                                ¿Estás seguro de que deseas eliminar esta evidencia?
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

                            <!-- Modal para editar -->
                            <div class="modal fade" id="editarEvidencia{{ $evidencia->id }}" tabindex="-1"
                                aria-labelledby="editarEvidenciaLabel{{ $evidencia->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <form action="{{ route('evidencias.update', [$trabajo, $evidencia]) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editarEvidenciaLabel{{ $evidencia->id }}">
                                                    Editar Evidencia</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="evidencia" class="form-label">Actualizar Archivo</label>
                                                    <input type="file" name="evidencia" class="form-control"
                                                        id="evidencia">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="observacion" class="form-label">Actualizar
                                                        Observación</label>
                                                    <textarea name="observacion" class="form-control" id="observacion" rows="3">{{ $evidencia->observacion }}</textarea>
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

    <!-- Modal para crear nueva evidencia -->
    <div class="modal fade" id="nuevaEvidencia" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="nuevaEvidenciaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('evidencias.store', $trabajo) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevaEvidenciaLabel">Nueva Evidencia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="evidencia" class="form-label">Evidencia</label>
                            <input type="file" name="evidencia" class="form-control" id="evidencia" required>
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
</x-layout>
