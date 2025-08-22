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
                                <td>{!! $detalle->descripcion !!}</td>
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
                        <label class="form-label">Descripción</label>

                        <!-- Trix usa este input oculto como “source of truth” -->
                        <input id="descripcion" type="hidden" name="descripcion" value="{{ old('descripcion') }}">
                        <trix-editor input="descripcion" class="trix-content"></trix-editor>
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
                        <label class="form-label">Descripción</label>

                        <input id="descripcionEditar" type="hidden" name="descripcion">
                        <trix-editor input="descripcionEditar" class="trix-content"></trix-editor>
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

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
        <style>
            /* === TOOLBAR: mostrar solo lo permitido === */

            /* Grupo de texto: ocultar todo... */
            trix-toolbar [data-trix-button-group="text-tools"] .trix-button {
                display: none !important;
            }

            /* ...y volver a mostrar B / I / strike */
            trix-toolbar [data-trix-button-group="text-tools"] .trix-button[data-trix-attribute="bold"],
            trix-toolbar [data-trix-button-group="text-tools"] .trix-button[data-trix-attribute="italic"],
            trix-toolbar [data-trix-button-group="text-tools"] .trix-button[data-trix-attribute="strike"] {
                display: inline-flex !important;
            }

            /* Grupo de bloques: ocultar todo... */
            trix-toolbar [data-trix-button-group="block-tools"] .trix-button {
                display: none !important;
            }

            /* ...y volver a mostrar bullets y numbers */
            trix-toolbar [data-trix-button-group="block-tools"] .trix-button[data-trix-attribute="bullet"],
            trix-toolbar [data-trix-button-group="block-tools"] .trix-button[data-trix-attribute="number"] {
                display: inline-flex !important;
            }

            /* Ocultar grupos que no usas */
            trix-toolbar [data-trix-button-group="file-tools"],
            trix-toolbar [data-trix-button-group="history-tools"],
            trix-toolbar .trix-button-group-spacer {
                display: none !important;
            }

            /* === EDITOR: más alto y con resize vertical === */
            trix-editor {
                min-height: 260px;
                overflow: auto;
                /* necesario para que funcione resize */
                resize: vertical;
                /* el usuario puede estirar/encoger en alto */
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
        <script>
            // Evita adjuntar archivos en Trix (opcional)
            document.addEventListener('trix-file-accept', e => e.preventDefault());
        </script>
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
                        const input = document.getElementById('descripcionEditar');
                        const editor = document.querySelector('trix-editor[input="descripcionEditar"]');

                        form.action = url;

                        // Carga el HTML en el input y en el editor
                        input.value = descripcion || '';
                        // Trix necesita que le “inyectes” el HTML al editor:
                        editor.editor.loadHTML(input.value);

                        const modal = new bootstrap.Modal(document.getElementById('editarDetalleModal'));
                        modal.show();

                        // Enfoca el editor al abrir (pequeño delay para que monte bien)
                        setTimeout(() => editor.focus(), 150);
                    });
                });

                // ELIMINAR (igual que antes)
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
    @endpush

</x-layout>