<x-layout>
    <h2 class="text-center py-5 fw-bold">Registro de Asistencia</h2>

    <div class="text-center my-5">
        <button id="btn-asistencia" onclick="registrarAsistencia()" class="btn btn-light border shadow-sm p-4 rounded-pill">
            <i class="fa-solid fa-fingerprint me-2"></i> Marcar
        </button>
    </div>

    <div class="text-center pt-5" id="alert-container"></div>

    @push('styles')
        <style>
            /* Animación para mostrar la alerta */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(25px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animated-alert {
                animation: fadeInUp 0.5s ease;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function mostrarAlerta(mensaje, tipo = 'primary') {
                const alertContainer = document.getElementById('alert-container');
                alertContainer.innerHTML = `
                    <div style="max-width: 250px;" class="alert mx-auto alert-${tipo} alert-dismissible fade show shadow-sm rounded-pill animated-alert" role="alert">
                        ${mensaje}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }

            function registrarAsistencia() {
                const btn = document.getElementById('btn-asistencia');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Registrando...';

                if (!navigator.geolocation) {
                    mostrarAlerta('Tu navegador no soporta geolocalización.', 'danger');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-fingerprint me-2"></i> Marcar';
                    return;
                }

                navigator.geolocation.getCurrentPosition(function(pos) {
                    fetch('{{ route('asistencia.registrar') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            lat: pos.coords.latitude,
                            lng: pos.coords.longitude
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        mostrarAlerta(data.message, 'success');
                    })
                    .catch(async err => {
                        let errorMsg = 'Error al registrar asistencia.';
                        try {
                            const data = await err.json();
                            errorMsg = data.message || errorMsg;
                        } catch {}
                        mostrarAlerta(errorMsg, 'danger');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-fingerprint me-2"></i> Marcar';
                    });
                }, function(error) {
                    mostrarAlerta('Error al obtener ubicación: ' + error.message, 'danger');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-fingerprint me-2"></i> Marcar';
                });
            }
        </script>
    @endpush
</x-layout>
