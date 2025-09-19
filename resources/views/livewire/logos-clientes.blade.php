<div>
    {{-- In work, do what you enjoy. --}}
    <div>
        <h1 class="h2 mb-4">Gestión de Logos de Clientes</h1>
    
        {{-- Botón para abrir el modal de nuevo logo --}}
        <button type="button" class="btn btn-primary mb-3" wire:click="newLogo">
            Nuevo Logo
        </button>
    
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <input type="text" class="form-control" wire:model.live="search" placeholder="Buscar por razón social...">
                </div>
            </div>
        </div>
        
        {{-- Tabla de logos --}}
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Razón Social</th>
                        <th scope="col">Logo</th>
                        <th scope="col" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logos as $logo)
                        <tr>
                            <td>{{ $logo->razonSocial }}</td>
                            <td>
                                @if ($logo->logo)
                                    <img src="{{ asset('storage/' . $logo->logo) }}" alt="Logo de {{ $logo->razonSocial }}" style="max-height: 50px;">
                                @else
                                    Sin logo
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" wire:click="editLogo({{ $logo->id }})">Editar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No se encontraron clientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    
        {{-- Paginación --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $logos->links('vendor.pagination.bootstrap-5') }}
        </div>
    
        {{-- Bootstrap Modal --}}
        <div class="modal fade" id="logoModal" tabindex="-1" aria-labelledby="logoModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoModalLabel">{{ $clienteId ? 'Editar Logo' : 'Subir Nuevo Logo' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            {{-- Select para elegir cliente (solo en modo creación) --}}
                            @if (!$clienteId)
                            <div class="mb-3">
                                <label for="idclienteBackoffice" class="form-label">Cliente</label>
                                <select class="form-select" id="idclienteBackoffice" wire:model="idclienteBackoffice">
                                    <option value="">Selecciona un cliente</option>
                                    @foreach ($empresas as $empresa)
                                        <option value="{{ $empresa->id }}">{{ $empresa->razonSocial }}</option>
                                    @endforeach
                                </select>
                                @error('idclienteBackoffice') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            @endif
    
                            {{-- Input para el archivo --}}
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control" id="logo" wire:model.live="logo">
                                @error('logo') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- Vista previa del logo --}}
                            {{-- @if ($logo)
                                <p>Vista previa del nuevo logo:</p>
                                <img src="{{ $logo->temporaryUrl() }}" alt="Vista previa del logo" style="max-height: 100px;">
                            @elseif ($logoPreview)
                                <p>Logo actual:</p>
                                <img src="{{ asset('storage/' . $logoPreview) }}" alt="Logo actual" style="max-height: 100px;">
                            @endif --}}

                            @if ($logo instanceof \Livewire\TemporaryUploadedFile)
                                <p>Vista previa del nuevo logo:</p>
                                <img src="{{ $logo->temporaryUrl() }}" alt="Vista previa del logo" style="max-height: 100px;">
                            @elseif ($logoPreview)
                                <p>Logo actual:</p>
                                <img src="{{ asset('storage/' . $logoPreview) }}" alt="Logo actual" style="max-height: 100px;">
                            @endif


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Script para controlar el modal --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            const logoModal = new bootstrap.Modal(document.getElementById('logoModal'));
            
            // Escucha el evento 'open-modal' y muestra el modal
            @this.on('open-modal', () => {
                logoModal.show();
            });
    
            // Escucha el evento 'close-modal' y oculta el modal
            @this.on('close-modal', () => {
                logoModal.hide();
            });
    
            // Escucha cuando el modal se cierra por el usuario para limpiar el estado de Livewire
            document.getElementById('logoModal').addEventListener('hidden.bs.modal', (e) => {
                // @this.reset(['clienteId', 'idclienteBackoffice', 'logo', 'logoPreview']);
                @this.call('closeModal');
            });
        });
    </script>
</div>
