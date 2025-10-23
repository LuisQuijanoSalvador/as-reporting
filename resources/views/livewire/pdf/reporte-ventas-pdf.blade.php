<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $reporteTitulo }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; margin-top: 20px; }
        .header { width: 100%; position: relative; margin-bottom: 20px; display: block; }
        .header .logo-empresa { position: absolute; left: 0; top: 0; max-width: 50px; }
        .header .logo-cliente { position: absolute; right: 0; top: 0; max-height: 50px; }
        .header .title { text-align: center; font-size: 18px; font-weight: bold; }
        .header .date-range { text-align: center; font-size: 12px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #592a56; font-weight: bold; color: #FFFFFF;}
        .text-end { text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        {{-- @if(isset($logoEmpresaBase64))
            <img src="{{ $logoEmpresaBase64 }}" alt="Logo Empresa" class="logo-empresa">
        @endif --}}
        <img src="{{ $logoEmpresa }}" alt="Logo Empresa" class="logo-empresa">
        <div class="title">{{ $reporteTitulo }}</div>
        <div class="date-range">Rango de Fechas: {{ date('d/m/Y', strtotime($fechaInicio)) }} - {{ date('d/m/Y', strtotime($fechaFin)) }}</div>
        @if(isset($logoCliente))
            <img src="{{ $logoCliente }}" alt="Logo Cliente" class="logo-cliente">
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Boleto</th>
                <th>Documento</th>
                <th>Pasajero</th>
                <th>Solicitante</th>
                <th>Ruta</th>
                <th>Tipo Ruta</th>
                <th>C.Costo</th>
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
                <th>Moneda</th>
                <th>Neto</th>
                <th>Inafecto</th>
                <th>Otros Imp.</th>
                <th>IGV</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datos as $item)
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
                    <td class="text-end">{{ number_format($item->TarifaNeta, 2) }}</td>
                    <td class="text-end">{{ number_format($item->Inafecto, 2) }}</td>
                    <td class="text-end">{{ number_format($item->OtrosImpuestos, 2) }}</td>
                    <td class="text-end">{{ number_format($item->IGV, 2) }}</td>
                    <td class="text-end">{{ number_format($item->Total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>