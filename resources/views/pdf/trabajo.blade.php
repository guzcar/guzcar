<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma {{ $trabajo->codigo }}</title>
    <style>
        @page {
            size: A4;
            margin: 80px 80px 0px 80px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            position: relative;
        }

        header {
            position: fixed;
            top: -55px;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
        }

        .header-logo {
            position: absolute;
            width: 60px;
        }

        .header-title {
            color: rgb(0, 80, 150);
            font-size: 22px;
            font-weight: bold;
            margin-top: 8px;
            padding-right: 70px;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 11px;
        }

        .header-line {
            border: 2x solid rgb(180, 0, 0);
        }

        .footer-line {
            border-top: 2px solid rgb(180, 0, 0);
            width: 100%;
            margin-bottom: 10px;
        }

        .footer-text {
            color: rgb(0, 80, 150);
        }

        .watermark {
            position: fixed;
            width: 450px;
            top: 45%;
            left: 42.5%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
        }

        .content {
            text-align: justify;
            margin-bottom: 50px;
        }

        .table-info {
            margin-left: auto;
            margin-top: -32px;
            border-collapse: collapse;
            text-align: center;
        }

        .table-info th,
        .table-info td {
            padding: 1px 3px;
            border: 1px solid #000;
        }

        .table-container {
            width: 100%;
            border-collapse: collapse;
        }

        .table-container th,
        .table-container td {
            padding: 1px 3px;
            border: 1px solid #000;
        }

        .table-container th {
            background-color: rgb(180, 198, 231);
        }

        .empty-case {
            padding: 0.5rem !important;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header>
        <img src="{{ public_path('images/logo-kia.jpg') }}" class="header-logo">
        <div class="header-title">AUTOMOTORES GUZCAR S.A.C.</div>
        <table class="table-info">
            <tr>
                <th style="width: 130px;">{{ date('d / m / Y') }}</th>
            </tr>
            <tr>
                <td>{{ $trabajo->codigo }}</td>
            </tr>
        </table>
        <hr class="header-line">
    </header>

    <!-- FOOTER -->
    <footer>
        <div class="footer-line"></div>
        <div class="footer-text">PROLG. LEONCIO PRADO 1575 - PJ. MIRAMAR ALTO</div>
        <div class="footer-text">ENTEL. 998248543 - CHIMBOTE</div>
    </footer>

    <!-- MARCA DE AGUA -->
    <img src="{{ public_path('images/logo-kia.jpg') }}" class="watermark">

    <!-- CONTENIDO -->
    <div class="content">

        <h3>PROFORMA</h3>

        <!-- Tabla Principal para alinear Vehículo y Cliente en una línea -->
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- Columna Vehículo -->
                <td style="width: 50%; vertical-align: top; padding-right: 0.5rem">
                    <table class="table-container">
                        <thead>
                            <tr>
                                <th colspan="2">VEHÍCULO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width: 50px">Placa</td>
                                <td>{{ $vehiculo->placa }}</td>
                            </tr>
                            <tr>
                                <td>Marca</td>
                                <td>{{ $vehiculo->marca }}</td>
                            </tr>
                            <tr>
                                <td>Modelo</td>
                                <td>{{ $vehiculo->modelo }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                <!-- Columna Clientes -->
                <td style="width: 50%; vertical-align: top;">
                    <table class="table-container">
                        <thead>
                            <tr>
                                <th colspan="3">CLIENTES</th>
                            </tr>
                            <tr>
                                <th style="width: 100px;">RUC / DNI</th>
                                <th style="width: 120px;">Nombre</th>
                                <th style="width: 100px;">Contacto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vehiculo->clientes as $cliente)
                                <tr>
                                    <td>{{ $cliente->identificador }}</td>
                                    <td>{{ $cliente->nombre }}</td>
                                    <td>{{ $cliente->telefono }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="empty-case"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <h3>SERVICIOS EJECUTADOS</h3>

        <table class="table-container">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th style="width: 95px">Costo</th>
                    <th style="width: 95px">Cantidad</th>
                    <th style="width: 100px">Sub-Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trabajo->servicios as $trabajoServicio)
                    <tr>
                        <td>{{ $trabajoServicio->servicio->nombre }}</td>
                        <td style="text-align: right">S/ {{ $trabajoServicio->precio }}</td>
                        <td style="text-align: center">{{ $trabajoServicio->cantidad }}</td>
                        <td style="text-align: right">S/
                            {{ number_format($trabajoServicio->cantidad * $trabajoServicio->precio, 2, '.', '') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="empty-case"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="3" style="border: 0"></td>
                    <td style="text-align: right"><b>S/ {{ number_format($subtotal_servicios, 2, '.', '') }}</b></td>
                </tr>
            </tbody>
        </table>

        <h3>ARTÍCULOS UTILIZADOS</h3>

        <table class="table-container">
            <thead>
                <tr>
                    <th>Artículo</th>
                    <th style="width: 95px">Costo</th>
                    <th style="width: 95px">Cantidad</th>
                    <th style="width: 100px">Sub-Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articulosAgrupados as $articulo)
                    <tr>
                        <td>
                            {{ $articulo['articulo']->subCategoria->categoria->nombre }}
                            {{ $articulo['articulo']->subCategoria->nombre }}
                            {{ $articulo['articulo']->especificacion }}
                            {{ $articulo['articulo']->marca }}
                            {{ $articulo['articulo']->color }} - {{ $articulo['articulo']->tamano_presentacion }}
                        </td>
                        <td style="text-align: right">S/ {{ $articulo['precio'] }}</td>
                        <td style="text-align: center">{{ $articulo['cantidad'] }}</td>
                        <td style="text-align: right">S/
                            {{ number_format($articulo['cantidad'] * $articulo['precio'], 2, '.', '') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="empty-case"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="3" style="border: 0"></td>
                    <td style="text-align: right"><b>S/ {{ number_format($subtotal_articulos, 2, '.', '') }}</b></td>
                </tr>
            </tbody>
        </table>

        <h3></h3>

        @if (request('igv'))
            <table class="table-container">
                <tr>
                    <td style="border: 0"></td>
                    <td style="border: 0; width: 95px;">Sub-Total:</td>
                    <td style="text-align: right;">S/ {{ number_format($total, 2, '.', '') }}</td>
                </tr>
                <tr>
                    <td style="border: 0"></td>
                    <td style="border: 0;">IGV:</td>
                    <td style="text-align: right;">S/ {{ number_format($total * request('igv_porcentaje') / 100, 2, '.', '') }}</td>
                </tr>
                <tr>
                    <td style="border: 0"></td>
                    <td style="border: 0;">Total:</td>
                    <th style="width: 100px; text-align: right;">S/ {{ number_format($total * (1 + request('igv_porcentaje') / 100), 2, '.', '') }}</th>
                </tr>
            </table>
        @else
            <table class="table-container">
                <tr>
                    <td style="border: 0"></td>
                    <td style="border: 0; width: 95px;">Total:</td>
                    <th style="width: 100px; text-align: right;">S/ {{ number_format($total, 2, '.', '') }}</th>
                </tr>
            </table>
        @endif

        {{-- Fin de la parte que quiero mejorar --}}

        <p>Tiempo de ejecución: <b>{{ $tiempo }}</b></p>
    </div>

</body>

</html>