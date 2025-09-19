<div>
    <div class="row">
        <div class="col-lg-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['ventas'] }}</h3>
                    <p>Registros de Ventas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <a href="{{ route('reportes.ventas') }}" class="small-box-footer">Ver reportes <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($stats['total_ventas'], 2) }}</h3>
                    <p>Total Ventas (S/)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-coins"></i>
                </div>
                <a href="{{ route('reportes.ventas') }}" class="small-box-footer">Ver detalles <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Últimos Registros de Ventas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Pasajero</th>
                                    <th>Ruta</th>
                                    <th>Fecha Emisión</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimas_ventas as $venta)
                                <tr>
                                    <td>{{ $venta->Documento }}</td>
                                    <td>{{ $venta->pasajero }}</td>
                                    <td>{{ $venta->Ruta }}</td>
                                    <td>{{ $venta->FechaEmision }}</td>
                                    <td>{{ number_format($venta->Total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>