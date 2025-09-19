<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Compras - AS Travel Perú</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 15mm 15mm 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4e73df;
        }
        
        .logo-left {
            width: 120px;
            text-align: left;
        }
        
        .logo-left img {
            max-width: 120px;
            max-height: 60px;
        }
        
        .title {
            text-align: center;
            flex-grow: 1;
        }
        
        .title h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            color: #592a56;
        }
        
        .title h2 {
            font-size: 14px;
            font-weight: normal;
            margin: 5px 0 0;
            color: #666;
        }
        
        .logo-right {
            width: 120px;
            text-align: right;
        }
        
        .logo-right img {
            max-width: 120px;
            max-height: 60px;
        }
        
        .filters {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 9px;
        }
        
        .filters p {
            margin: 2px 0;
        }
        
        .filters strong {
            color: #4e73df;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        
        th {
            background-color: #592a56;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
        }
        
        td {
            vertical-align: top;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #666;
        }
        
        .page-number {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Encabezado con logos y título -->
    <div class="header">
        <div class="logo-left">
            {{-- <img src="{{ asset('/img/logo-as-travel.png') }}" alt="AS Travel"> --}}
            {{-- <img src="{{ public_path('storage/logo-as-travel.png')  }}" alt="AS Travel"> --}}
            <img src="{{ $logoEmpresa }}" alt="Logo Empresa">
            <pre>{{ $logoEmpresa }}</pre>
            {{-- <img src="https://via.placeholder.com/120x60/4e73df/FFFFFF?text=AS+Travel" alt="AS Travel"> --}}
        </div>
        <div class="title">
            <h1>REPORTE DE COMPRAS</h1>
            <h2>{{ date('d/m/Y') }}</h2>
        </div>
        <div class="logo-right">
            {{-- <img src="{{ asset('img/logo-cliente.png') }}" alt="Cliente"> --}}
            {{-- <img src="https://via.placeholder.com/120x60/28a745/FFFFFF?text=Cliente" alt="Cliente"> --}}
        </div>
    </div>
    
    <!-- Filtros aplicados -->
    <div class="filters">
        <p><strong>Filtros aplicados:</strong></p>
        @if(request('empresa_id'))
            <p>Empresa: {{ App\Models\Empresa::find(request('empresa_id'))->razonSocial }}</p>
        @endif
        @if(request('fecha_inicial') && request('fecha_final'))
            <p>Período: {{ request('fecha_inicial') }} al {{ request('fecha_final') }}</p>
        @endif
    </div>
    
    <!-- Tabla de datos -->
    <table>
        <thead>
            <tr>
                <th width="5%">F.Emision</th>
                <th width="5%">Tipo</th>
                <th width="5%">Num.Boleto</th>
                <th width="8%">Documento</th>
                <th width="8%">Pasajero</th>
                <th width="10%">Solicitante</th>
                <th width="15%">Ruta</th>
                <th width="5%">TipoRuta</th>
                {{-- <th width="5%">Counter</th> --}}
                <th width="8%">CC</th>
                <th width="4%">Cod1</th>
                <th width="4%">Cod2</th>
                <th width="4%">Cod3</th>
                <th width="4%">Cod4</th>
                {{-- <th width="10%">Cliente</th> --}}
                <th width="4%">Mon.</th>
                <th width="6%" class="text-right">Neto</th>
                <th width="8%" class="text-right">Inafecto</th>
                <th width="8%" class="text-right">OtrosImp.</th>
                <th width="5%" class="text-right">IGV</th>
                <th width="8%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
            <tr>
                <td>{{ \Carbon\Carbon::parse($venta->FechaEmision)->format('d/m/Y') }}</td>
                <td>{{ $venta->Tipo }}</td>
                <td>{{ $venta->NumeroBoleto }}</td>
                <td>{{ $venta->Documento }}</td>
                <td>{{ $venta->pasajero }}</td>
                <td>{{ $venta->Solicitante }}</td>
                <td>{{ $venta->Ruta }}</td>
                <td>{{ $venta->TipoRuta }}</td>
                {{-- <td>{{ $venta->Counter }}</td> --}}
                <td>{{ $venta->CentroCosto }}</td>
                <td>{{ $venta->Cod1 }}</td>
                <td>{{ $venta->Cod2 }}</td>
                <td>{{ $venta->Cod3 }}</td>
                <td>{{ $venta->Cod4 }}</td>
                {{-- <td>{{ $venta->Cliente }}</td> --}}
                <td class="text-center">{{ $venta->Moneda }}</td>
                <td class="text-right">{{ number_format($venta->TarifaNeta, 2) }}</td>
                <td class="text-right">{{ number_format($venta->Inafecto, 2) }}</td>
                <td class="text-right">{{ number_format($venta->OtrosImpuestos, 2) }}</td>
                <td class="text-right">{{ number_format($venta->IGV, 2) }}</td>
                <td class="text-right fw-bold">{{ number_format($venta->Total, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="14" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>{{ number_format($ventas->sum('TarifaNeta'), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($ventas->sum('Inafecto'), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($ventas->sum('OtrosImpuestos'), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($ventas->sum('IGV'), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($ventas->sum('Total'), 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
    
    <!-- Pie de página -->
    <div class="footer">
        <div>Total registros: {{ count($ventas) }}</div>
        <div class="page-number">Página {PAGE_NUM} de {PAGE_COUNT}</div>
        <div>Fecha de generación: {{ date('d/m/Y H:i:s') }}</div>
    </div>
</body>
</html>