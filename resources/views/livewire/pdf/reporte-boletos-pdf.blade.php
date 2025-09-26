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
                <th scope="col">Fecha</th>
                <th scope="col">Boleto</th>
                <th scope="col">Documento</th>
                <th scope="col">Pasajero</th>
                <th scope="col">Solicitante</th>
                <th scope="col">C.Costo</th>
                @if ($datos->first()->idCliente !== 1036)
                    <th scope="col">Cod1</th>
                    <th scope="col">Cod2</th>
                    <th scope="col">Cod3</th>
                    <th scope="col">Cod4</th>
                @endif
                <th scope="col">Aerolinea</th>
                <th scope="col">Clase</th>
                {{-- <th scope="col">FechaSalida</th>
                <th scope="col">FechaLlegada</th>
                <th scope="col">Ruta</th> --}}
                <th scope="col">Mon.</th>
                <th scope="col">Neto</th>
                <th scope="col">Inaf.</th>
                <th scope="col">IGV</th>
                <th scope="col">Otros Imp.</th>
                <th scope="col">Total</th>
                <th scope="col">NetoFee</th>
                <th scope="col">IGVFee</th>
                <th scope="col">TotalFee</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datos as $item)
                <tr>
                    <td>{{ $item->fechaEmision }}</td>
                    <td>{{ $item->Boleto }}</td>
                    <td>{{ $item->Documento }}</td>
                    <td>{{ $item->Pasajero }}</td>
                    <td>{{ $item->Solicitante }}</td>
                    <td>{{ $item->centroCosto }}</td>
                    @if ($item->idCliente !== 1036)
                        <td>{{ $item->cod1 }}</td>
                        <td>{{ $item->cod2 }}</td>
                        <td>{{ $item->cod3 }}</td>
                        <td>{{ $item->cod4 }}</td>
                    @endif
                    <td>{{ $item->Aerolinea }}</td>
                    <td>{{ $item->Clase }}</td>
                    {{-- <td>{{ $item->fechaSalida }}</td>
                    <td>{{ $item->fechaLlegada }}</td>
                    <td>{{ $item->Ruta }}</td> --}}
                    <td>{{ $item->Moneda }}</td>
                    <td>{{ number_format($item->tarifaNeta, 2) }}</td>
                    <td>{{ number_format($item->Inafecto, 2) }}</td>
                    <td>{{ number_format($item->igv, 2) }}</td>
                    <td>{{ number_format($item->otrosImpuestos, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                    <td>{{ number_format($item->NetoFee, 2) }}</td>
                    <td>{{ number_format($item->IGVFee, 2) }}</td>
                    <td>{{ number_format($item->TotalFee, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>