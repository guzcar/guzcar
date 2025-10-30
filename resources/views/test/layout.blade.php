<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-R">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Presupuestos</title>
    <style>
        /* --- Reset y Base --- */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
            background-color: #f8f9fa; /* Un fondo gris muy sutil */
            color: #212529;
            margin: 0; 
            padding: 0; 
        }

        /* --- Layout (Sin Header) --- */
        .container { 
            max-width: 1000px; 
            margin: 4rem auto; /* Más margen superior para compensar la falta de header */
            padding: 2rem; 
            background-color: #fff; /* El contenido principal se mantiene en una "hoja" blanca */
            /* Sin bordes, sin sombras, sin radio */
        }

        /* --- Tipografía y Títulos --- */
        h2 {
            font-size: 1.75rem;
            font-weight: 400; /* Más ligero */
            color: #343a40;
            margin-top: 0;
            margin-bottom: 1.5rem;
        }
        h3 {
            font-size: 1.25rem;
            font-weight: 500;
            margin-top: 2rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid #eee; /* Mantenemos el separador sutil */
            padding-bottom: 0.5rem;
        }
        hr {
            border: 0;
            border-top: 1px solid #e9ecef;
            margin: 2rem 0;
        }

        /* --- Formularios Minimalistas --- */
        .form-group { margin-bottom: 1rem; }
        .form-group label { 
            display: block; 
            margin-bottom: 0.5rem; 
            font-weight: 500; 
            font-size: 0.9rem;
            color: #495057;
        }
        .form-control { 
            width: 100%; 
            padding: 0.6rem 0.75rem; 
            font-size: 1rem; 
            line-height: 1.5; 
            color: #495057;
            background-color: #f8f9fa; /* Fondo sutil, igual que el body */
            border: 1px solid #f8f9fa; /* Borde "invisible" */
            border-radius: 0; /* Sin bordes redondeados */
            transition: border-color 0.15s ease-in-out, background-color 0.15s ease-in-out;
        }
        .form-control:focus {
            border-color: #007bff; /* Borde de acento al enfocar */
            background-color: #fff;
            outline: 0;
            box-shadow: none; /* Sin sombra */
        }
        textarea.form-control { min-height: 80px; }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; gap: 1rem; }
        }

        /* --- Filas de Ítems (Servicios/Artículos) --- */
        .item-row {
            display: grid;
            grid-template-columns: 1fr 110px 110px auto;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            align-items: start;
        }
        .add-item-container { margin-top: 1rem; }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        /* --- Botones Planos --- */
        .btn { 
            display: inline-block; 
            padding: 0.6rem 1rem; 
            font-size: 0.9rem; 
            font-weight: 500;
            text-align: center;
            text-decoration: none; 
            border-radius: 0; /* Sin bordes redondeados */
            cursor: pointer; 
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }
        .btn-primary { background-color: #007bff; color: white; border-color: #007bff; }
        .btn-primary:hover { background-color: #0069d9; border-color: #0062cc; }
        .btn-secondary { background-color: #6c757d; color: white; border-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; border-color: #545b62; }
        .btn-success { background-color: #28a745; color: white; border-color: #28a745; }
        .btn-success:hover { background-color: #218838; border-color: #1e7e34; }
        
        /* Botón de eliminar (estilo "outline" minimalista) */
        .btn-remove-item {
            background-color: transparent;
            color: #dc3545;
            border: 1px solid #dc3545;
            font-weight: bold;
            padding: 0.6rem 0.8rem;
        }
        .btn-remove-item:hover { background-color: #dc3545; color: white; }
        
        /* Botón de enlace (ya es minimalista) */
        .btn-link {
            background: none; border: none;
            color: #007bff; padding: 0;
            text-decoration: none; cursor: pointer;
            font-size: 0.9rem; font-family: inherit;
        }
        .btn-link:hover { text-decoration: underline; }
        .btn-link.text-danger { color: #dc3545; }

        /* --- Alertas Sutiles --- */
        .alert { 
            padding: 1rem; 
            margin-bottom: 1.5rem; 
            border: none;
            border-left: 4px solid transparent; /* Solo un borde izquierdo de color */
            border-radius: 0;
            background-color: #f8f9fa; /* Fondo muy sutil */
        }
        .alert-success { color: #155724; border-left-color: #28a745; }
        .alert-danger { color: #721c24; border-left-color: #dc3545; }
        .alert-danger ul { margin: 0; padding-left: 1.5rem; }

        /* --- Tabla (ya era bastante minimalista, se mantiene) --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        th, td {
            padding: 0.9rem 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6; 
            vertical-align: middle;
        }
        thead th {
            font-weight: 500;
            background-color: #f8f9fa; 
            color: #495057;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tbody tr:hover {
            background-color: #f8f9fa; 
        }
        .actions-cell {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 1rem; 
        }

        /* --- Paginación Minimalista (solo texto) --- */
        .pagination-container {
            margin-top: 2rem;
        }
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
        }
        .page-item .page-link {
            color: #6c757d; /* Color gris por defecto */
            padding: 0.6rem 0.9rem;
            text-decoration: none;
            border: none; /* Sin bordes */
            margin-left: 0;
            transition: color 0.1s ease;
        }
        .page-item:first-child .page-link,
        .page-item:last-child .page-link {
            border-radius: 0; /* Sin bordes redondeados */
        }
        .page-item .page-link:hover {
            background-color: transparent;
            color: #007bff; /* Color de acento al pasar el mouse */
        }
        .page-item.active .page-link {
            z-index: 1;
            color: #007bff; /* Color de acento para el activo */
            font-weight: 700; /* Lo hacemos negrita */
            background-color: transparent;
            border-color: transparent;
        }
        .page-item.disabled .page-link {
            color: #ced4da; /* Color muy claro para deshabilitado */
            pointer-events: none;
            background-color: transparent;
        }

        /* --- Clases de Utilidad (se mantienen) --- */
        .d-flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .items-center { align-items: center; }
        .mb-4 { margin-bottom: 1.5rem; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

    </style>
</head>
<body>
    
    <main class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
    
    @stack('scripts')
</body>
</html>