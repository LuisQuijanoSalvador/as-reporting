<div>
    <h1>Componente de usuarios cargado</h1>
    
    @if(auth()->user()->role !== 'admin')
        <div class="alert alert-danger">
            No tienes permisos para acceder a esta sección.
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Usuarios</h3>
                <div class="card-tools">
                    <button wire:click="dispatch('openModal')" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Usuario
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="mb-3">
                    <input type="text" wire:model="search" class="form-control" placeholder="Buscar usuarios...">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Empresa</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->id }}</td>
                                    <td>{{ $usuario->name }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        @if($usuario->role === 'admin')
                                            <span class="badge bg-danger">Administrador</span>
                                        @else
                                            <span class="badge bg-info">Usuario</span>
                                        @endif
                                    </td>
                                    <td>{{ $usuario->empresa->razonSocial }}</td>
                                    <td>
                                        <button wire:click="dispatch('editUsuario', {{ $usuario->id }})" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <button wire:click="deleteUsuario({{ $usuario->id }})" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $usuarios->links() }}
            </div>
        </div>
    @endif

    <!-- Modal para crear/editar usuario -->
    <livewire:usuario-form />
</div>