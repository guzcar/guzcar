@extends('test.layout')

@section('content')
    <div class="d-flex justify-between items-center mb-4">
        <h2>Lista de Presupuestos</h2>
        <a href="{{ route('test.presupuestos.create') }}" class="btn btn-primary">
            Crear Nuevo
        </a>
    </div>

    <div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Vehículo (Placa)</th>
                    <th>Marca</th>
                    <th>Color</th>
                    {{-- La clase .actions-cell se encargará de la alineación --}}
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($presupuestos as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>{{ $p->cliente?->nombre ?? 'N/A' }}</td>
                        <td>{{ $p->vehiculo?->placa ?? 'N/A' }}</td>
                        <td>{{ $p->vehiculo?->marca?->nombre ?? 'N/A' }}</td>
                        <td>{{ $p->vehiculo?->color ?? 'N/A' }}</td>
                        {{-- Usamos la nueva clase para alinear y espaciar los botones --}}
                        <td class="actions-cell">
                            <a href="{{ route('test.presupuestos.pdf', $p) }}" target="_blank" class="btn-link">PDF</a>
                            <a href="{{ route('test.presupuestos.edit', $p) }}" class="btn-link">Editar</a>
                            
                            {{-- 
                                El estilo en línea 'display: inline;' ya no es necesario 
                                gracias al 'gap' de la clase .actions-cell 
                            --}}
                            </td>
                    </tr>
                @empty
                    <tr>
                        {{-- Usamos la clase de utilidad .text-center --}}
                        <td colspan="6" class="text-center">No se encontraron presupuestos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Envolvemos la paginación para darle margen superior --}}
    <div class="pagination-container">
        {{ $presupuestos->links() }}
    </div>
@endsection