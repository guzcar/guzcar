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
                                    <button type="button" class="btn btn-warning btn-editar" data-id="{{ $evidencia->id }}"
                                        data-observacion="{{ $evidencia->observacion }}"
                                        data-url="{{ route('gestion.evidencias.update', [$trabajo, $evidencia]) }}"
                                        style="width: 60px; height: 60px;">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-eliminar" data-id="{{ $evidencia->id }}"
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
    <div class="modal fade" id="nuevaEvidencia" tabindex="-1" aria-labelledby="nuevaEvidenciaLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
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
                                <span class="text-muted">(Máximo 15)</span>
                            </label>
                            <input type="file" name="evidencias[]" class="form-control" id="evidencias" multiple
                                required>
                        </div>
                        <div>
                            <label for="observacion" class="form-label">Descripción</label>
                            <textarea name="observacion" class="form-control" id="observacion" rows="5"
                                placeholder="Breve descripción del trabajo realizado"></textarea>
                        </div>
                        <div id="error-container"></div>

                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button> -->
                        <button type="submit" class="btn btn-primary">Subir Evidencias</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para editar evidencia --}}
    <div class="modal fade" id="editarEvidenciaModal" tabindex="-1" aria-labelledby="editarEvidenciaModalLabel"
        aria-hidden="true">
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
                            <label for="observacionEditar" class="form-label">Descripción</label>
                            <textarea name="observacion" class="form-control" id="observacionEditar"
                                rows="3"></textarea>
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
    <div class="modal fade" id="eliminarEvidenciaModal" tabindex="-1" aria-labelledby="eliminarEvidenciaModalLabel"
        aria-hidden="true">
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
                // 1. Función para comprimir imágenes manteniendo formatos válidos
                async function compressImage(file) {
                    try {
                        // Formatos aceptados por el servidor
                        const validFormats = {
                            'image/jpeg': 'jpg',
                            'image/jpg': 'jpg',
                            'image/png': 'png',
                            'image/webp': 'webp', // Aunque no esté en la validación, lo convertiremos a jpg
                            'video/mp4': 'mp4',
                            'video/quicktime': 'mov'
                        };

                        // Si no es un formato comprimible, devolver original
                        if (!validFormats[file.type]) {
                            console.log(`Archivo ${file.name} no requiere compresión. Tipo: ${file.type}`);
                            return file;
                        }

                        // Si es video, devolver original
                        if (file.type.startsWith('video/')) {
                            console.log(`Video ${file.name} no se comprime. Tipo: ${file.type}`);
                            return file;
                        }

                        return await new Promise((resolve) => {
                            const reader = new FileReader();

                            reader.onerror = () => {
                                console.error('Error al leer el archivo:', file.name);
                                resolve(file);
                            };

                            reader.onload = function (event) {
                                const img = new Image();

                                img.onerror = () => {
                                    console.error('Error al cargar la imagen:', file.name);
                                    resolve(file);
                                };

                                img.onload = function () {
                                    try {
                                        const canvas = document.createElement('canvas');
                                        const ctx = canvas.getContext('2d');
                                        const MAX_DIMENSION = 1200;
                                        const QUALITY = 0.6;

                                        let width = img.width;
                                        let height = img.height;

                                        if (width > MAX_DIMENSION || height > MAX_DIMENSION) {
                                            const ratio = Math.min(MAX_DIMENSION / width, MAX_DIMENSION / height);
                                            width = Math.floor(width * ratio);
                                            height = Math.floor(height * ratio);
                                        }

                                        canvas.width = width;
                                        canvas.height = height;
                                        ctx.imageSmoothingQuality = 'high';
                                        ctx.drawImage(img, 0, 0, width, height);

                                        // Convertir siempre a jpeg para cumplir con la validación del servidor
                                        const mimeType = 'image/jpeg';
                                        const newFileName = file.name.replace(/\.[^/.]+$/, '') + '.jpg';

                                        canvas.toBlob(
                                            (blob) => {
                                                if (!blob) {
                                                    console.error('No se pudo generar blob para:', file.name);
                                                    resolve(file);
                                                    return;
                                                }

                                                const compressedFile = new File([blob], newFileName, {
                                                    type: mimeType,
                                                    lastModified: Date.now()
                                                });

                                                console.log(`Compresión: ${file.name} - Original: ${(file.size / 1024).toFixed(2)}KB | Comprimido: ${(blob.size / 1024).toFixed(2)}KB`);
                                                resolve(compressedFile);
                                            },
                                            mimeType,
                                            QUALITY
                                        );
                                    } catch (error) {
                                        console.error('Error en compresión:', error);
                                        resolve(file);
                                    }
                                };

                                img.src = event.target.result;
                            };

                            reader.readAsDataURL(file);
                        });
                    } catch (error) {
                        console.error('Error general en compressImage:', error);
                        return file;
                    }
                }

                // 2. Interceptar envío del formulario - VERSIÓN CORREGIDA
                const form = document.querySelector('#nuevaEvidencia form');
                if (form) {
                    form.addEventListener('submit', async function (e) {
                        e.preventDefault();

                        const fileInput = document.getElementById('evidencias');
                        const submitButton = this.querySelector('button[type="submit"]');
                        const originalButtonText = submitButton.innerHTML;
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                            document.querySelector('input[name="_token"]')?.value ||
                            '';

                        try {
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando archivos...';

                            const formData = new FormData();

                            // Copiar todos los campos excepto archivos
                            for (const [key, value] of new FormData(this)) {
                                if (key !== 'evidencias[]') {
                                    formData.append(key, value);
                                }
                            }

                            // Procesar archivos
                            const files = Array.from(fileInput.files);
                            console.log(`Procesando ${files.length} archivos...`);

                            const processedFiles = await Promise.all(
                                files.map(async (file) => {
                                    try {
                                        return await compressImage(file);
                                    } catch (error) {
                                        console.error('Error procesando archivo:', file.name, error);
                                        return file;
                                    }
                                })
                            );

                            // Verificar que todos los archivos sean válidos
                            const validExtensions = ['.jpg', '.jpeg', '.png', '.mp4', '.mov'];
                            processedFiles.forEach(file => {
                                const fileExt = file.name.substring(file.name.lastIndexOf('.')).toLowerCase();
                                if (!validExtensions.includes(fileExt)) {
                                    console.error(`Archivo con extensión no permitida: ${file.name}`);
                                    throw new Error(`El archivo ${file.name} no tiene un formato permitido (jpg, jpeg, png, mp4, mov)`);
                                }
                                formData.append('evidencias[]', file, file.name);
                            });

                            // Configurar headers
                            const headers = {
                                'Accept': 'application/json'
                            };

                            if (csrfToken) {
                                headers['X-CSRF-TOKEN'] = csrfToken;
                            }

                            // Enviar al servidor
                            const response = await fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: headers
                            });

                            if (!response.ok) {
                                const errorData = await response.json().catch(() => ({}));
                                throw new Error(errorData.message || 'Error en el servidor');
                            }

                            window.location.reload();

                        } catch (error) {
                            console.error('Error en el envío:', error);

                            let errorMessage = 'Error al procesar los archivos';
                            if (error.message.includes('NetworkError')) {
                                errorMessage = 'Error de conexión. Verifica tu red.';
                            } else if (error.message) {
                                errorMessage = error.message;
                            }

                            // Mostrar error en un div específico (por ejemplo, con id "error-container")
                            const errorContainer = document.getElementById('error-container'); // Reemplaza con tu ID real

                            // Limpiar errores previos
                            const existingAlert = errorContainer.querySelector('.alert');
                            if (existingAlert) existingAlert.remove();

                            // Crear nuevo mensaje de error
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-3 mb-0';
                            alertDiv.innerHTML = `
                                    <strong>Error:</strong> ${errorMessage}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                `;

                            // Agregar el mensaje al contenedor específico
                            errorContainer.appendChild(alertDiv);

                        } finally {
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;
                        }
                    });
                }

                // Resto del código para modales...
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    modal.addEventListener('hidden.bs.modal', function () {
                        const triggerButton = document.querySelector(`[data-bs-target="#${modal.id}"]`);
                        if (triggerButton) triggerButton.focus();
                    });
                });

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