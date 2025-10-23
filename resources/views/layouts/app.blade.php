<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AS Reporting' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        /* Estilos para la tabla de reportes */
        .reporte-table {
            font-size: 0.85rem;
            white-space: nowrap;
        }
        
        .reporte-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .reporte-table td {
            vertical-align: middle;
            padding: 0.5rem !important;
        }
        
        .reporte-table .column-small {
            width: 80px;
            min-width: 80px;
            max-width: 80px;
        }
        
        .reporte-table .column-medium {
            width: 120px;
            min-width: 120px;
            max-width: 120px;
        }
        
        .reporte-table .column-large {
            width: 150px;
            min-width: 150px;
            max-width: 150px;
        }
        
        .reporte-table .column-xlarge {
            width: 200px;
            min-width: 200px;
            max-width: 200px;
        }
        
        .reporte-table .text-ellipsis {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .reporte-table .text-wrap {
            white-space: normal;
            word-wrap: break-word;
            max-width: 150px;
        }
        
        /* Para la fila de totales */
        .reporte-table tfoot th {
            background-color: #e9ecef;
            font-weight: 700;
        }
        
        /* Scroll horizontal para tablas anchas */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Mejoras para móviles */
        @media (max-width: 768px) {
            .reporte-table {
                font-size: 0.75rem;
            }
            
            .reporte-table th,
            .reporte-table td {
                padding: 0.25rem !important;
            }
            
            .reporte-table .column-small {
                width: 60px;
                min-width: 60px;
                max-width: 60px;
            }
            
            .reporte-table .column-medium {
                width: 90px;
                min-width: 90px;
                max-width: 90px;
            }
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                        </a></li>
                    </ul>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                    </a>
                </li>
                @endauth
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ url('/dashboard') }}" class="brand-link">
                {{-- <img src="{{ asset('img/logo.png') }}" alt="AS Travel Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
                <span class="brand-text font-weight-light">AS Reporting</span>
            </a>

            <div class="sidebar">
                @auth
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    {{-- <div class="image">
                        <img src="{{ asset('img/user.png') }}" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                    </div> --}}
                </div>
                @endauth

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') || request()->is('admin/dashboard') || request()->is('user/dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        
                        @if(auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->is('usuarios*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Usuarios</p>
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a href="{{ route('logos.gestion') }}" class="nav-link {{ request()->is('logos.gestion*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-image"></i>
                                    <p>Logos</p>
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a href="{{ route('gestion.campos') }}" class="nav-link {{ request()->is('gestion.campos*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-layer-group"></i>
                                    <p>Campos</p>
                                </a>
                            </li>
                        @endif
                        
                        <li class="nav-item">
                            <a href="{{ route('repventas') }}" class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Reportes de Compras</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('repboletos') }}" class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Reportes de Boletos</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    @yield('header')
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    {{-- @yield('content') --}}
                    {{ $slot }} 
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} <a href="https://astravelperu.com">AS Travel Perú</a>.</strong>
            Todos los derechos reservados.
        </footer>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Scripts personalizados -->
    <script src="{{ asset('js/app.js') }}"></script>
    @livewireScripts
    @stack('scripts')
    @push('scripts')
        <script>
            // Configuración global de SweetAlert2
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            // Mostrar notificaciones de sesión
            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}'
                });
            @endif
            
            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: '{{ session('error') }}'
                });
            @endif
        </script>
    @endpush
</body>
</html>