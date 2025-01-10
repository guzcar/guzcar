/*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    const sidebar = document.getElementById('layoutSidenav_nav');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            // Alternar el estado del menú lateral
            document.body.classList.toggle('sb-sidenav-toggled');
            // Guardar el estado en el localStorage
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

    // Detectar clics fuera del sidebar para cerrarlo
    document.addEventListener('click', function(event) {
        // Verificar si el clic fue fuera del menú lateral y el menú está abierto
        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target) && document.body.classList.contains('sb-sidenav-toggled')) {
            // Cerrar el menú si el clic está fuera del sidebar
            document.body.classList.remove('sb-sidenav-toggled');
            // Eliminar el estado del localStorage
            localStorage.setItem('sb|sidebar-toggle', 'false');
        }
    });
});
