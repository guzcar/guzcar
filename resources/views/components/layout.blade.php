<x-base class="sb-nav-fixed">
    @push('styles')
        <style>
            .sb-topnav .dropdown-item:focus,
            .sb-topnav .dropdown-item:active {
                color: inherit !important;
                background-color: transparent !important;
                outline: none !important;
                box-shadow: none !important;
            }
            .sb-topnav .dropdown-item::selection {
                background: transparent;
                color: inherit;
            }
        </style>
    @endpush

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
</x-base>
