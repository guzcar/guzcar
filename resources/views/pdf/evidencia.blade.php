<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evidencias {{ $trabajo->codigo }}</title>
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
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                @php $count = 0; @endphp
                @foreach ($evidencias as $evidencia)
                    @if ($evidencia->tipo === 'imagen')
                        @if ($count % 3 === 0 && $count !== 0)
                            </tr><tr>
                        @endif
                        <td style="width: 33.33%; padding-bottom: 20px; text-align: center; vertical-align: top;">
                            <div style="display: inline-block; max-width: 100%; max-height: 200px; 
                                @if ($count % 3 === 1) margin: 0 10px; @else margin: 0; @endif">
                                <img src="{{ public_path('storage/' . str_replace('public/', '', $evidencia->evidencia_url)) }}"
                                    style="max-width: 100%; max-height: 200px; width: auto; height: auto; display: block;">
                            </div>
                        </td>
                        @php $count++; @endphp
                    @endif
                @endforeach
            </tr>
        </table>
    </div>

</body>

</html>