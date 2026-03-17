<x-layout>

    <h1 class="mb-3">
        Gestión de repuestos {{ $trabajo->vehiculo->placa ?? '-' }}
    </h1>

    <div class="d-flex justify-content-between mb-3">
        <a class="btn btn-light border py-2" href="{{ route('home') }}">Volver</a>
        <button type="button" class="btn btn-primary py-2" data-bs-toggle="modal" data-bs-target="#nuevoRepuesto">
            Agregar repuesto
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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="my-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0 border-bottom-0">
            <div class="table-responsive">
                <table class="table mb-0 table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="py-3">Descripción</th>
                            <th class="py-3 text-center">Precio</th>
                            <th class="py-3 text-center">Cant.</th>
                            <th class="py-3 text-center">Subtotal</th>
                            <th class="py-3" style="width: 120px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($repuestos as $repuesto)
                            <tr>
                                <td>{{ $repuesto->descripcion }}</td>
                                <td class="text-center">S/ {{ number_format($repuesto->precio, 2) }}</td>
                                <td class="text-center">{{ $repuesto->cantidad }}</td>
                                <td class="text-center fw-bold">S/ {{ number_format($repuesto->precio * $repuesto->cantidad, 2) }}</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm btn-editar me-1"
                                        data-url="{{ route('gestion.repuestos.update', [$trabajo, $repuesto]) }}"
                                        data-descripcion="{{ $repuesto->descripcion }}"
                                        data-precio="{{ $repuesto->precio }}"
                                        data-cantidad="{{ $repuesto->cantidad }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <button type="button" class="btn btn-danger btn-sm btn-eliminar"
                                        data-url="{{ route('gestion.repuestos.destroy', [$trabajo, $repuesto]) }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-5">
                                    <i class="fa-solid fa-box-open fs-1 mb-3"></i>
                                    <p class="mb-0">Aún no has registrado repuestos.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($repuestos->hasPages())
            <div class="card-footer border-top-0">
                <div class="d-flex justify-content-center mt-3">
                    {{ $repuestos->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: nuevo repuesto --}}
    <div class="modal fade" id="nuevoRepuesto" tabindex="-1" aria-labelledby="nuevoRepuestoLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('gestion.repuestos.store', $trabajo) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevoRepuestoLabel">Nuevo repuesto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-semibold">Repuesto</label>
                            <input type="text" id="descripcion" name="descripcion" class="form-control" required placeholder="" value="{{ old('descripcion') }}">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label for="cantidad" class="form-label fw-semibold">Cantidad</label>
                                <input type="number" step="1" min="1" id="cantidad" name="cantidad" class="form-control" required value="{{ old('cantidad', 1) }}">
                            </div>
                            <div class="col-6 mb-3">
                                <label for="precio" class="form-label fw-semibold">Precio</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">S/</span>
                                    <input type="number" step="0.01" min="0" id="precio" name="precio" class="form-control" required placeholder="0.00" value="{{ old('precio') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: editar repuesto --}}
    <div class="modal fade" id="editarRepuestoModal" tabindex="-1" aria-labelledby="editarRepuestoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editarRepuestoForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarRepuestoModalLabel">Editar repuesto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descripción</label>
                            <input type="text" id="descripcionEditar" name="descripcion" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Precio (S/)</label>
                                <input type="number" step="0.01" min="0" id="precioEditar" name="precio" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Cantidad</label>
                                <input type="number" step="1" min="1" id="cantidadEditar" name="cantidad" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: eliminar repuesto --}}
    <div class="modal fade" id="eliminarRepuestoModal" tabindex="-1" aria-labelledby="eliminarRepuestoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="eliminarRepuestoForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="eliminarRepuestoModalLabel">Confirmar eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Eliminar este repuesto de forma permanente?
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
                    const descripcion = btn.getAttribute('data-descripcion');
                    const precio = btn.getAttribute('data-precio');
                    const cantidad = btn.getAttribute('data-cantidad');

                    const form = document.getElementById('editarRepuestoForm');
                    const inputDescripcion = document.getElementById('descripcionEditar');
                    const inputPrecio = document.getElementById('precioEditar');
                    const inputCantidad = document.getElementById('cantidadEditar');

                    form.action = url;
                    inputDescripcion.value = descripcion;
                    inputPrecio.value = precio;
                    inputCantidad.value = cantidad;

                    const modal = new bootstrap.Modal(document.getElementById('editarRepuestoModal'));
                    modal.show();

                    setTimeout(() => inputDescripcion.focus(), 150);
                });
            });

            // ELIMINAR
            document.querySelectorAll('.btn-eliminar').forEach(btn => {
                btn.addEventListener('click', () => {
                    const url = btn.getAttribute('data-url');
                    const form = document.getElementById('eliminarRepuestoForm');
                    form.action = url;
                    new bootstrap.Modal(document.getElementById('eliminarRepuestoModal')).show();
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