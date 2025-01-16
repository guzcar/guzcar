<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Guzcar</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        @stack('styles')
    </head>
    <body class="sb-nav-fixed">
        <x-navbar />
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <x-sidebar />
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container py-4 px-sm-4">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        @stack('scripts')
    </body>
</html>
