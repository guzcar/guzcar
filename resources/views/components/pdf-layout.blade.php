<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Automotores Guzcar S.A.C.' }}</title>
    <style>
        @page {
            size: A4;
            margin: 110px 80px 25px 80px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            margin: 0;
            padding: 0;
            position: relative;
        }

        header {
            position: fixed;
            top: -95px;
            left: 0;
            right: 0;
            height: 60px;
            margin-bottom: 20px;
        }

        .header-logo {
            width: 130px;
        }

        .header-title {
            color: rgb(31, 62, 129);
            font-weight: bold;
            font-size: 15px;
        }

        .header-image {
            width: 80px;
            height: auto;
        }

        footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 50px;
            font-size: 11px;
        }

        .footer-image {
            width: 150px;
            height: auto;
        }

        .border-top {
            border-top: 1px solid black;
        }

        .watermark {
            position: fixed;
            width: 450px;
            top: 45%;
            left: 42.5%;
            transform: translate(-50%, -50%);
            z-index: -1;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
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
            margin-bottom: 50px;
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

        .normal {
            font-weight: normal;
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
                    <td class="text-left" style="padding: 0px 0px 0px 0px; vertical-align: top;">
                        <img src="{{ public_path('images/logo-guzcar.jpg') }}" class="header-logo">
                    </td>
                    <td class="text-left" style="vertical-align: middle; padding-right: 1rem;">
                        <p class="header-title m-0">AUTOMOTORES GUZCAR S.A.C.</p>
                        <p class="m-0" style="margin-bottom: 4px;">RUC: 20600613716 - <span style="text-transform: none;">Prolog. Leoncio Prado N° 1575 - CHIMBOTE</span></p>
                        <p class="m-0"></p>
                        <p class="m-0"><b>{{ $tipoReporte ?? 'REPORTE' }} N° {{ $code ?? 'CODIGO' }}</b></p>
                        <p class="m-0">{{ date('d/m/Y h:i A') }}</p>
                    </td>
                    <td style="width: 80px; text-align: right; vertical-align: middle;">
                        <img src="{{ public_path('images/mega_homologado_1.png') }}" class="header-image">
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="border-bottom: solid black 1px; padding-top: 10px;"></div>
    </header>

    <footer>
        <div class="border-top" style="margin-bottom: 10px;"></div>
        <table class="table-header" style="width: 100%;">
            <tbody>
                <tr>
                    <td class="text-left" style="vertical-align: middle; width: 70%;">
                        <p class="m-0">DIRECCIÓN: <span style="text-transform: none;">Prolog. Leoncio Prado N° 1575 - CHIMBOTE</span></p>
                        <p class="m-0">CORREO: <span style="text-transform: lowercase;">automotoresguzcar1@hotmail.com</span> - TELF: 919294602 - 998248543</p>
                    </td>
                    <td style="width: 30%; text-align: right; vertical-align: middle;">
                        <img src="{{ public_path('images/mega_homologado_2.png') }}" class="footer-image">
                    </td>
                </tr>
            </tbody>
        </table>
    </footer>

    <img src="{{ public_path('images/logo-kia.jpg') }}" class="watermark">

    <div class="content">
        {{ $slot }}
    </div>
</body>

</html>