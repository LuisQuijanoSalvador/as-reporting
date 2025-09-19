<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filtros de Reporte</h3>
            {{-- <img src="{{ public_path() . '\img\logo-as-travel.png' }}" alt="AS Travel">
            <img src="{{ asset('img/logo-as-travel.png') }}" alt="AS Travel"> --}}
        </div>
        <div class="card-body">
            <div class="row">
                @if(auth()->user()->role === 'admin')
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="empresa_id">Empresa</label>
                        <select wire:model="empresa_id" id="empresa_id" class="form-control">
                            <option value="">Todas las empresas</option>
                            @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->razonSocial }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_inicial">Fecha Inicial</label>
                        <input type="date" wire:model="fecha_inicial" id="fecha_inicial" class="form-control">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_final">Fecha Final</label>
                        <input type="date" wire:model="fecha_final" id="fecha_final" class="form-control">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button wire:click="filtrar" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                            <button wire:click="limpiarFiltros" class="btn btn-secondary">
                                <i class="fas fa-eraser"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Registros de Ventas</h3>
            <div>
                <button wire:click="exportExcel" class="btn btn-success mr-2">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </button>
                <button wire:click="exportPDF" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($ventas->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped reporte-table">
                    <thead>
                        <tr>
                            <th class="column-small">Tipo</th>
                            <th class="column-medium">Documento</th>
                            <th class="column-medium">NumeroBoleto</th>
                            <th class="column-large">pasajero</th>
                            <th class="column-large">Solicitante</th>
                            <th class="column-xlarge">Ruta</th>
                            <th class="column-medium">TipoRuta</th>
                            <th class="column-medium">Counter</th>
                            <th class="column-medium">CentroCosto</th>
                            <th class="column-small">Cod1</th>
                            <th class="column-small">Cod2</th>
                            <th class="column-small">Cod3</th>
                            <th class="column-small">Cod4</th>
                            <th class="column-large">Cliente</th>
                            <th class="column-medium">FechaEmision</th>
                            <th class="column-small">Moneda</th>
                            <th class="column-medium text-end">TarifaNeta</th>
                            <th class="column-medium text-end">Inafecto</th>
                            <th class="column-medium text-end">OtrosImpuestos</th>
                            <th class="column-medium text-end">IGV</th>
                            <th class="column-medium text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventas as $venta)
                        <tr>
                            <td class="text-center">{{ $venta->Tipo }}</td>
                            <td>{{ $venta->Documento }}</td>
                            <td>{{ $venta->NumeroBoleto }}</td>
                            <td>
                                <div class="text-ellipsis" title="{{ $venta->pasajero }}">
                                    {{ $venta->pasajero }}
                                </div>
                            </td>
                            <td>
                                <div class="text-ellipsis" title="{{ $venta->Solicitante }}">
                                    {{ $venta->Solicitante }}
                                </div>
                            </td>
                            <td>
                                <div class="text-ellipsis" title="{{ $venta->Ruta }}">
                                    {{ $venta->Ruta }}
                                </div>
                            </td>
                            <td>{{ $venta->TipoRuta }}</td>
                            <td>{{ $venta->Counter }}</td>
                            <td>{{ $venta->CentroCosto }}</td>
                            <td>{{ $venta->Cod1 }}</td>
                            <td>{{ $venta->Cod2 }}</td>
                            <td>{{ $venta->Cod3 }}</td>
                            <td>{{ $venta->Cod4 }}</td>
                            <td>
                                <div class="text-ellipsis" title="{{ $venta->Cliente }}">
                                    {{ $venta->Cliente }}
                                </div>
                            </td>
                            <td>{{ $venta->FechaEmision }}</td>
                            <td class="text-center">{{ $venta->Moneda }}</td>
                            <td class="text-end">{{ number_format($venta->TarifaNeta, 2) }}</td>
                            <td class="text-end">{{ number_format($venta->Inafecto, 2) }}</td>
                            <td class="text-end">{{ number_format($venta->OtrosImpuestos, 2) }}</td>
                            <td class="text-end">{{ number_format($venta->IGV, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($venta->Total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="17" class="text-end">Total:</th>
                            <th class="text-end">{{ number_format($ventas->sum('TarifaNeta'), 2) }}</th>
                            <th class="text-end">{{ number_format($ventas->sum('Inafecto'), 2) }}</th>
                            <th class="text-end">{{ number_format($ventas->sum('OtrosImpuestos'), 2) }}</th>
                            <th class="text-end">{{ number_format($ventas->sum('IGV'), 2) }}</th>
                            <th class="text-end">{{ number_format($ventas->sum('Total'), 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <p class="mb-0">Mostrando {{ $ventas->firstItem() }} a {{ $ventas->lastItem() }} de {{ $ventas->total() }} registros</p>
                </div>
                <div>
                    {{ $ventas->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <h4>No se encontraron registros</h4>
                <p class="text-muted">No hay ventas que coincidan con los filtros seleccionados.</p>
            </div>
            @endif
        </div>
    </div>
</div>