<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Automotores Guzcar S.A.C.' }}</title>
    <style>
        @page {
            size: A4;
            margin: 130px 80px 0px 80px;
            /* Ajustado margen superior para el header */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            text-transform: uppercase;
            margin: 0;
            padding: 0;
            position: relative;
        }

        header {
            position: fixed;
            top: -100px;
            /* Ajustado para coincidir con el margen superior de @page */
            left: 0;
            right: 0;
            height: 70px;
            /* Aumentado para mejor espacio */
            margin-bottom: 20px;
            /* Espacio adicional después del header */
        }

        .header-logo {
            width: 150px;
        }

        .header-title {
            color: rgb(31, 62, 129);
            font-weight: bold;
        }

        footer {
            position: fixed;
            bottom: 0px;
            /* Ajustado para coincidir con el margen inferior */
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 11px;
        }

        /* Resto de tus estilos permanecen igual */
        .border-top {
            border-top: 1px solid black;
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

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            /* border-bottom: 1px solid black;
            border-top: 1px solid black; */
            color: white;
            background-color: rgb(31, 62, 129);
            border: solid white 1px;
        }

        .table tr:nth-child(even) {
            background-color: rgba(100, 100, 100, 0.25);
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .content {
            margin-top: 30px;
            /* Espacio adicional después del header */
            margin-bottom: 60px;
            /* Espacio adicional antes del footer */
        }

        .m-0 {
            margin: 0;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mt-0 {
            margin-top: 0;
        }

        .w-100 {
            width: 100%;
        }

        .w-150 {
            width: 150px;
        }

        .w-200 {
            width: 200px;
        }

        .bold {
            font-weight: bold;
        }

        .table-void {
            border-collapse: collapse;
            width: 100%;
        }

        .table-void td {
            padding: 1px 0;
            vertical-align: bottom;
        }

        .table-header {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-header td {
            padding: 1px 0;
        }

        .table-simple {
            width: 100%;
            border-collapse: collapse;
        }

        .table-simple th {
            color: white;
            background-color: #424242;
            border: 1px solid white;
        }

        .table-simple td {
            border: 1px solid white;
        }

        .table-simple tr:nth-child(even) {
            background-color: rgba(100, 100, 100, 0.25);
        }
    </style>

    @stack('styles')
</head>

<body>

    <header>
        <table class="table-header">
            <tbody>
                <tr style="height: 100%;">
                    <td class="text-left" style="padding: 0px 5px 0px 0px; vertical-align: top;">
                        <img src="{{ public_path('images/logo-guzcar.jpg') }}" class="header-logo">
                    </td>
                    <td class="text-left" style="vertical-align: middle;">
                        <!-- <p class="header-title mt-0">AUTOMOTORES GUZCAR S.A.C.</p> -->
                        <p class="mt-0">Prolog. Leoncio Prado N° 1575 - CHIMBOTE</p>
                        <p class="m-0">TELF: 919294602 - 998248543</p>
                        <p class="m-0" style="text-transform: lowercase;">automotoresguzcar1@hotmail.com</p>
                    </td>
                    <td style="
                        border-radius: 10px;
                        width: 150px;
                        text-align: center;
                        padding: 5px 0;
                        height: 100%;
                        display: table-cell;
                        vertical-align: middle;
                        position: relative;
                        background-color:rgb(31, 62, 129);
                    ">
                        <div style="
                            position: absolute;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                            width: calc(100% - 10px);
                            color: white;
                        ">
                            <p class="bold mt-0">RUC: 20600613716</p>
                            <p class="m-0">{{ $tipoReporte ?? 'REPORTE' }}</p>
                            <p class="m-0">N° {{ $code ?? 'CODIGO' }}</p>
                            <!-- <p class="m-0">{{ date('d/m/Y h:i:s') }}</p> -->
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="border-bottom: solid black 1px; padding-top: 10px;"></div>
    </header>

    <footer>
        <div class="border-top" style="margin-bottom: 10px;"></div>
        <div>GRACIAS POR SU PREFERENCIA</div>
        <div>AUTOMOTORES GUZCAR S.A.C.</div>
    </footer>

    <img src="{{ public_path('images/logo-kia.jpg') }}" class="watermark">

    <div class="content">
        {{ $slot }}
    </div>
</body>

</html>