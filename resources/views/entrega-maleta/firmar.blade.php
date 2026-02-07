<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Firmar Entrega #{{ $entrega->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Bloquear gestos táctiles en el canvas para que no mueva la página */
        canvas { display: block; touch-action: none; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 lg:bg-gray-100 min-h-screen flex flex-col items-center">

    <div class="w-full lg:max-w-6xl bg-white lg:shadow-xl lg:rounded-xl lg:my-8 flex flex-col min-h-screen lg:min-h-0 lg:overflow-hidden">
        
        <div class="bg-white border-b px-4 py-4 flex flex-row justify-between items-center sticky top-0 z-20 shadow-sm lg:static lg:shadow-none">
            <div class="flex items-center gap-3">
                <div class="bg-blue-600 text-white p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">{{ $titulo }}</h1>
                    <p class="text-xs text-gray-500">Entrega #{{ $entrega->id }}</p>
                </div>
            </div>
            <button onclick="window.close()" class="text-gray-500 hover:bg-gray-100 p-2 rounded-full transition" title="Cerrar ventana">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-grow p-4 lg:p-8 grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 overflow-y-auto">
            
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-sm shadow-sm">
                    <div class="space-y-2">
                        <div class="flex justify-between border-b border-blue-200 pb-1">
                            <span class="text-blue-800 font-semibold">Maleta:</span>
                            <span class="text-gray-700">{{ $entrega->maleta->codigo ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-blue-200 pb-1">
                            <span class="text-blue-800 font-semibold">Propietario:</span>
                            <span class="text-gray-700 text-right truncate max-w-[150px]">{{ $entrega->propietario->name ?? '---' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-800 font-semibold">Responsable:</span>
                            <span class="text-gray-700 text-right truncate max-w-[150px]">{{ $entrega->responsable->name ?? '---' }}</span>
                        </div>
                    </div>
                </div>

                <details class="group bg-white border rounded-xl shadow-sm overflow-hidden" open>
                    <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-4 bg-gray-50 hover:bg-gray-100 transition">
                        <span class="text-gray-700 font-bold">Ver Lista ({{ count($herramientas) }})</span>
                        <span class="transition group-open:rotate-180">
                            <svg fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="20"><path d="M6 9l6 6 6-6"></path></svg>
                        </span>
                    </summary>
                    <div class="text-gray-600 group-open:animate-fadeIn max-h-60 overflow-y-auto p-2">
                        <table class="min-w-full text-xs">
                            <tbody class="divide-y divide-gray-100">
                                @forelse($herramientas as $tool)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-3 py-2 flex items-center gap-2">
                                            <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            {{ $tool->nombre }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="px-4 py-2 italic text-center">Sin items</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </details>
            </div>

            <div class="lg:col-span-2 flex flex-col gap-6 pb-24 lg:pb-0">
                
                <div class="bg-white p-4 rounded-xl border-2 border-gray-200 shadow-sm">
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">Firma del Propietario</h3>
                            <p class="text-xs text-gray-500">{{ $entrega->propietario->name ?? 'Requerido' }}</p>
                        </div>
                        <button type="button" id="btn-limpiar-propietario" class="text-xs text-red-600 bg-red-50 px-3 py-1.5 rounded hover:bg-red-100 transition font-semibold">
                            Limpiar
                        </button>
                    </div>
                    <div class="relative w-full border-2 border-dashed border-gray-300 rounded-lg bg-white overflow-hidden" id="container-propietario">
                        <canvas id="canvas-propietario" class="block w-full h-full"></canvas>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-xl border-2 border-gray-200 shadow-sm">
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">Firma del Responsable</h3>
                            <p class="text-xs text-gray-500">{{ $entrega->responsable->name ?? 'Requerido' }}</p>
                        </div>
                        <button type="button" id="btn-limpiar-responsable" class="text-xs text-red-600 bg-red-50 px-3 py-1.5 rounded hover:bg-red-100 transition font-semibold">
                            Limpiar
                        </button>
                    </div>
                    <div class="relative w-full border-2 border-dashed border-gray-300 rounded-lg bg-white overflow-hidden" id="container-responsable">
                        <canvas id="canvas-responsable" class="block w-full h-full"></canvas>
                    </div>
                </div>

            </div>
        </div>

        <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t z-30 lg:static lg:bg-transparent lg:border-0 lg:p-6 lg:pt-0">
            <button id="btn-guardar-todo" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-lg shadow-lg transition flex justify-center items-center gap-2 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Guardar Firmas
            </button>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            class SignatureManager {
                constructor(canvasId, containerId, clearBtnId, initialBase64) {
                    this.canvas = document.getElementById(canvasId);
                    this.container = document.getElementById(containerId);
                    this.clearBtn = document.getElementById(clearBtnId);
                    
                    // Configuración de proporción (3:1 es estándar para firmas)
                    this.aspectRatio = 3.0; 
                    
                    this.pad = new SignaturePad(this.canvas, {
                        backgroundColor: 'rgba(255, 255, 255, 0)', // Transparente
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 1.5,
                        maxWidth: 3.5, // Trazo grueso para móviles
                    });

                    // Estado interno
                    this.bgImage = null; // Guardará la imagen de BD cargada en memoria
                    this.hasBgImage = false; // Flag para saber si hay imagen de BD activa

                    this.initListeners();

                    // Carga inicial
                    if (initialBase64) {
                        this.preloadImage(initialBase64);
                    } else {
                        this.resizeCanvas();
                    }
                }

                // Cargar imagen en memoria una sola vez (síncrono para el canvas despues)
                preloadImage(base64) {
                    const img = new Image();
                    img.src = base64;
                    img.onload = () => {
                        this.bgImage = img;
                        this.hasBgImage = true;
                        this.resizeCanvas(); // Pintar una vez cargada
                    };
                }

                resizeCanvas() {
                    // 1. Guardar trazos actuales del usuario (vectores)
                    // Importante: toData() guarda los puntos, no los píxeles
                    const currentStrokes = this.pad.toData();

                    // 2. Calcular dimensiones físicas
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    const width = this.container.offsetWidth;
                    const height = width / this.aspectRatio; // Forzar altura proporcional

                    // Aplicar altura al contenedor CSS
                    this.container.style.height = height + 'px';

                    // 3. Redimensionar Canvas (Esto borra el contenido automáticamente)
                    this.canvas.width = width * ratio;
                    this.canvas.height = height * ratio;
                    
                    const ctx = this.canvas.getContext("2d");
                    ctx.scale(ratio, ratio);

                    // 4. Limpiar explícitamente (buena práctica)
                    this.pad.clear();

                    // 5. RESTAURAR IMAGEN DE FONDO (Firma de BD)
                    // Usamos drawImage directo, es más rápido y fiable que pad.fromDataURL en resizes
                    if (this.hasBgImage && this.bgImage) {
                        // Dibujamos la imagen estirándola al tamaño actual del canvas
                        // width/height aquí son las dimensiones lógicas (CSS), el context ya está escalado
                        ctx.drawImage(this.bgImage, 0, 0, width, height);
                    }

                    // 6. RESTAURAR TRAZOS NUEVOS (Si el usuario dibujó algo encima)
                    if (currentStrokes.length > 0) {
                        this.pad.fromData(currentStrokes);
                    }
                }

                initListeners() {
                    // Botón limpiar con confirmación
                    this.clearBtn.addEventListener('click', () => {
                        if(this.pad.isEmpty() && !this.hasBgImage) return;

                        Swal.fire({
                            title: '¿Borrar firma?',
                            text: "Esta acción no se puede deshacer",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#9ca3af',
                            confirmButtonText: 'Sí, borrar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.pad.clear(); // Borra vectores
                                this.hasBgImage = false; // Olvida la imagen de BD
                                this.bgImage = null;
                                
                                // Forzar repintado para asegurar que se borre visualmente
                                this.resizeCanvas();
                            }
                        });
                    });
                }

                getDataForSave() {
                    // Si el pad está vacío y NO tenemos imagen de fondo, retornamos null
                    if (this.pad.isEmpty() && !this.hasBgImage) return null;
                    
                    // toDataURL exporta todo lo que se ve en el canvas (Fondo + Trazos)
                    return this.canvas.toDataURL('image/png');
                }
            }

            // --- INICIALIZACIÓN ---
            
            // Datos desde Laravel
            const dataProp = @json($entrega->firma_propietario);
            const dataResp = @json($entrega->firma_responsable);

            const managerProp = new SignatureManager('canvas-propietario', 'container-propietario', 'btn-limpiar-propietario', dataProp);
            const managerResp = new SignatureManager('canvas-responsable', 'container-responsable', 'btn-limpiar-responsable', dataResp);

            // --- MANEJO DE REDIMENSIONAMIENTO ---
            // Usamos un pequeño debounce para no sobrecargar, pero la lógica de pintado ahora es síncrona
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    managerProp.resizeCanvas();
                    managerResp.resizeCanvas();
                }, 100); 
            });

            // --- GUARDADO ---
            const btnGuardar = document.getElementById('btn-guardar-todo');
            btnGuardar.addEventListener('click', function () {
                
                // Si ambos están vacíos (ni trazos ni imagen previa)
                const firmaProp = managerProp.getDataForSave();
                const firmaResp = managerResp.getDataForSave();

                if (!firmaProp && !firmaResp) {
                    Swal.fire('Atención', 'No hay firmas para guardar.', 'info');
                    return;
                }

                const originalText = btnGuardar.innerHTML;
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = 'Guardando...';
                btnGuardar.classList.add('opacity-70', 'cursor-wait');

                fetch("{{ route('entrega.firma.save', $entrega) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        firma_propietario: firmaProp,
                        firma_responsable: firmaResp,
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            title: '¡Guardado!',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => window.close());
                    } else {
                        throw new Error('Error en servidor');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = originalText;
                    btnGuardar.classList.remove('opacity-70', 'cursor-wait');
                });
            });
        });
    </script>
</body>
</html>