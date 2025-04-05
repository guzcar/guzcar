<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Automotores Guzcar S.A.C.' }}</title>
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

    @stack('styles')
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
                <td>{{ $code ?? 'CODIGO' }}</td>
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

    <div class="content">
        {{ $slot }}
    </div>
</body>

</html>