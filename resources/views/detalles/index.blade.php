<x-layout>

    <h1 class="mb-3">
        Detalles técnicos {{ $trabajo->vehiculo->placa ?? '-' }}
    </h1>

    <div class="d-flex justify-content-between mb-3">
        <a class="btn btn-light border py-2" href="{{ route('home') }}">Volver</a>
        <button type="button" class="btn btn-primary py-2" data-bs-toggle="modal" data-bs-target="#nuevoDetalle">
            Agregar detalle
        </button>
    </div>

    <ul class="list-group mb-3">
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Tipo</span>
                <span>{{ $trabajo->vehiculo->tipoVehiculo->nombre ?? '-' }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Marca</span>
                <span>{{ $trabajo->vehiculo->marca?->nombre ?? '-' }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Modelo</span>
                <span>{{ $trabajo->vehiculo->modelo?->nombre ?? '-' }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Color</span>
                <span>{{ $trabajo->vehiculo->color ?? '-' }}</span>
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

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <p class="my-0">{{ session('success') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @error('descripcion')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <p class="my-0">{{ $message }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @enderror

    <div class="card">
        <div class="card-body p-0 border-bottom-0">
            <div class="table-responsive">
                <table class="table mb-0 table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="py-3">Detalle</th>
                            <th class="py-3" style="width: 160px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($detalles as $detalle)
                            <tr>
                                <td>{!! nl2br(e($detalle->descripcion)) !!}</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-editar me-2"
                                        data-id="{{ $detalle->id }}"
                                        data-url="{{ route('gestion.detalles.update', [$trabajo, $detalle]) }}"
                                        data-descripcion='@json($detalle->descripcion)'>
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <button type="button" class="btn btn-danger btn-eliminar"
                                        data-url="{{ route('gestion.detalles.destroy', [$trabajo, $detalle]) }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-secondary py-5">
                                    <i class="fa-regular fa-circle-xmark fs-1 mb-3"></i>
                                    <p class="mb-0">Aún no has registrado detalles.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($detalles->hasPages())
            <div class="card-footer border-top-0">
                <div class="d-flex justify-content-center mt-3">
                    {{ $detalles->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: nuevo detalle --}}
    <div class="modal fade" id="nuevoDetalle" tabindex="-1" aria-labelledby="nuevoDetalleLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('gestion.detalles.store', $trabajo) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevoDetalleLabel">Nuevo detalle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción del trabajo</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" rows="10" 
                                placeholder="Escribe aquí una descripción clara del trabajo realizado">{{ old('descripcion') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: editar detalle --}}
    <div class="modal fade" id="editarDetalleModal" tabindex="-1" aria-labelledby="editarDetalleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editarDetalleForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarDetalleModalLabel">Editar detalle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea id="descripcionEditar" name="descripcion" class="form-control" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: eliminar detalle --}}
    <div class="modal fade" id="eliminarDetalleModal" tabindex="-1" aria-labelledby="eliminarDetalleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="eliminarDetalleForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="eliminarDetalleModalLabel">Confirmar eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Eliminar este detalle de forma permanente?
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // EDITAR
            document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', () => {
                    const url = btn.getAttribute('data-url');
                    const raw = btn.getAttribute('data-descripcion');

                    let descripcion = '';
                    try { descripcion = raw ? JSON.parse(raw) : ''; }
                    catch (e) { descripcion = raw ?? ''; }

                    const form = document.getElementById('editarDetalleForm');
                    const textarea = document.getElementById('descripcionEditar');

                    form.action = url;
                    textarea.value = descripcion || '';

                    const modal = new bootstrap.Modal(document.getElementById('editarDetalleModal'));
                    modal.show();

                    // Enfoca el textarea al abrir
                    setTimeout(() => textarea.focus(), 150);
                });
            });

            // ELIMINAR
            document.querySelectorAll('.btn-eliminar').forEach(btn => {
                btn.addEventListener('click', () => {
                    const url = btn.getAttribute('data-url');
                    const form = document.getElementById('eliminarDetalleForm');
                    form.action = url;
                    new bootstrap.Modal(document.getElementById('eliminarDetalleModal')).show();
                });
            });

            // Accesibilidad: devolver focus al trigger
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function () {
                    const trigger = document.querySelector(`[data-bs-target="#${modal.id}"]`);
                    if (trigger) trigger.focus();
                });
            });
        });
    </script>

</x-layout>