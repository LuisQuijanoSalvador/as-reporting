<div>
    <h2>Configuración de Campos por Cliente</h2>

    @if (session()->has('message'))
        <div style="color: green;">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <form wire:submit.prevent="saveConfiguration">
        <div>
            <label for="client">Seleccionar Cliente:</label>
            <select id="client" wire:model.live="selectedClientId">
                <option value="">-- Seleccione un cliente --</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->razonSocial }}</option>
                @endforeach
            </select>
        </div>

        @if ($selectedClientId)
            <h3>Campos Personalizados para {{ $config->client->razonSocial ?? 'Nuevo Cliente' }}</h3>
            <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <h5 class="card-header">Configuración de **Cod1**</h5>
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="visible1" 
                                           wire:model.live="cod1_is_visible">
                                    <label for="visible1">Mostrar en Reporte</label>
                                </div>
                                <div>
                                    <label for="label1">Etiqueta (Título de Columna):</label>
                                    <input type="text" id="label1" 
                                           wire:model.live="cod1_label" 
                                           placeholder="Ej: Centro de Costo">
                                    @error("cod1_label") <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <h5 class="card-header">Configuración de **Cod2**</h5>
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"  id="visible2" 
                                           wire:model.live="cod2_is_visible">
                                    <label for="visible2">Mostrar en Reporte</label>
                                </div>
                                <div>
                                    <label for="label2">Etiqueta (Título de Columna):</label>
                                    <input type="text" id="label2" 
                                           wire:model.live="cod2_label" 
                                           placeholder="Ej: Centro de Costo">
                                    @error("cod2_label") <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <h5 class="card-header">Configuración de **Cod3**</h5>
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"  id="visible3" 
                                           wire:model.live="cod3_is_visible">
                                    <label for="visible3">Mostrar en Reporte</label>
                                </div>
                                <div>
                                    <label for="label3">Etiqueta (Título de Columna):</label>
                                    <input type="text" id="label3" 
                                           wire:model.live="cod3_label" 
                                           placeholder="Ej: Centro de Costo">
                                    @error("cod3_label") <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <h5 class="card-header">Configuración de **Cod4**</h5>
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"  id="visible4" 
                                           wire:model.live="cod4_is_visible">
                                    <label for="visible4">Mostrar en Reporte</label>
                                </div>
                                <div>
                                    <label for="label4">Etiqueta (Título de Columna):</label>
                                    <input type="text" id="label4" 
                                           wire:model.live="cod4_label" 
                                           placeholder="Ej: Centro de Costo">
                                    @error("cod4_label") <span style="color: red;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <button type="submit" style="padding: 10px; background-color: blue; color: white;">
                Guardar Configuración
            </button>
        @else
            <p>Selecciona un cliente para configurar sus campos.</p>
        @endif
    </form>
</div>
