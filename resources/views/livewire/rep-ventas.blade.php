<div>
    <h1 class="h2 mb-4">Reporte de Compras</h1>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                {{-- Filtro de fecha --}}
                <div class="col-md-3">
                    <label for="fechaInicio" class="form-label">Fecha Inicial</label>
                    <input type="date" id="fechaInicio" wire:model.live="fechaInicio" class="form-control">
                </div>

                <div class="col-md-3">
                    <label for="fechaFin" class="form-label">Fecha Final</label>
                    <input type="date" id="fechaFin" wire:model.live="fechaFin" class="form-control">
                </div>

                {{-- Dropdown para admin --}}
                @if ($userRole === 'admin')
                    <div class="col-md-3">
                        <label for="empresa" class="form-label">Empresa</label>
                        <select id="empresa" wire:model.live="selectedEmpresaId" class="form-select">
                            <option value="">Todas las empresas</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}">{{ $empresa->razonSocial }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                {{-- Campo de búsqueda 
                <div class="col-md-3">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" id="search" wire:model.live="search" placeholder="Pasajero, Boleto, etc." class="form-control">
                </div>--}}
            </div>

            {{-- Botones de exportación --}}
            <div class="mt-4 d-flex justify-content-start gap-2">
                <button wire:click="exportarExcel" class="btn btn-success">
                    Exportar a Excel
                </button>
                <button wire:click="exportarPDF" class="btn btn-danger">
                    Exportar a PDF
                </button>
            </div>
        </div>
    </div>
    @php
        $clienteId = $datos->first()->idCliente ?? null;
    @endphp


    {{-- Tabla de resultados --}}
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered tabla-compras">
            <thead class="table-light">
                <tr>
                    <th scope="col">Tipo</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Boleto</th>
                    <th scope="col">Documento</th>
                    <th scope="col">Pasajero</th>
                    <th scope="col">Solicitante</th>
                    <th scope="col">Ruta</th>
                    <th scope="col">Tipo Ruta</th>
                    <th scope="col">C.Costo</th>
                    @if ($clientConfig->cod1_is_visible)
                        <th scope="col">{{ $clientConfig->cod1_label ?? 'Cod1' }}</th>
                    @endif
                    
                    @if ($clientConfig->cod2_is_visible)
                        <th scope="col">{{ $clientConfig->cod2_label ?? 'Cod2' }}</th>
                    @endif
                    
                    @if ($clientConfig->cod3_is_visible)
                        <th scope="col">{{ $clientConfig->cod3_label ?? 'Cod3' }}</th>
                    @endif
                    
                    @if ($clientConfig->cod4_is_visible)
                        <th scope="col">{{ $clientConfig->cod4_label ?? 'Cod4' }}</th>
                    @endif
                    <th scope="col">Moneda</th>
                    <th scope="col">Tarifa Neta</th>
                    <th scope="col">Inafecto</th>
                    <th scope="col">Otros Impuestos</th>
                    <th scope="col">IGV</th>
                    <th scope="col">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($datos as $item)
                    <tr>
                        <td>{{ $item->Tipo }}</td>
                        <td>{{ $item->FechaEmision }}</td>
                        <td>{{ $item->NumeroBoleto }}</td>
                        <td>{{ $item->Documento }}</td>
                        <td>{{ $item->pasajero }}</td>
                        <td>{{ $item->Solicitante }}</td>
                        <td>{{ $item->Ruta }}</td>
                        <td>{{ $item->TipoRuta }}</td>
                        <td>{{ $item->CentroCosto }}</td>
                        @if ($clientConfig->cod1_is_visible)
                            <td>{{ $item->Cod1 }}</td>
                        @endif
                        
                        @if ($clientConfig->cod2_is_visible)
                            <td>{{ $item->Cod2 }}</td>
                        @endif
                        
                        @if ($clientConfig->cod3_is_visible)
                            <td>{{ $item->Cod3 }}</td>
                        @endif
                        
                        @if ($clientConfig->cod4_is_visible)
                            <td>{{ $item->Cod4 }}</td>
                        @endif
                        <td>{{ $item->Moneda }}</td>
                        <td>{{ number_format($item->TarifaNeta, 2) }}</td>
                        <td>{{ number_format($item->Inafecto, 2) }}</td>
                        <td>{{ number_format($item->OtrosImpuestos, 2) }}</td>
                        <td>{{ number_format($item->IGV, 2) }}</td>
                        <td>{{ number_format($item->Total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="20" class="text-center">No se encontraron datos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $datos->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>