<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1f2937;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            color: #d97706;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 11px;
        }
        .info-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 10px;
            margin-bottom: 20px;
        }
        .info-box p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead {
            background-color: #f3f4f6;
        }
        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge-vencendo {
            background-color: #fef3c7;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-vencido {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .dias-restantes {
            font-weight: bold;
        }
        .dias-restantes.urgente {
            color: #dc2626;
        }
        .dias-restantes.medio {
            color: #d97706;
        }
        .dias-restantes.normal {
            color: #059669;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Itens com validade até: {{ $dataLimite }}</p>
        <p>Gerado em: {{ $dataGeracao }}</p>
    </div>

    <div class="info-box">
        <p><strong>Total de lotes próximos ao vencimento:</strong> {{ $lotes->count() }}</p>
        <p><strong>Total de itens diferentes afetados:</strong> {{ count($lotesPorItem) }}</p>
        <p><strong>Definição:</strong> Lotes que vencem nos próximos 90 dias (incluindo itens já vencidos)</p>
    </div>

    @if($lotes->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Quantidade no Lote</th>
                    <th class="text-center">Data de Validade</th>
                    <th class="text-center">Dias Restantes</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lotes as $lote)
                    @php
                        $hoje = \Carbon\Carbon::today();
                        $validade = \Carbon\Carbon::parse($lote->data_validade);
                        $diasRestantes = $hoje->diffInDays($validade, false);
                        $estaVencido = $validade->lt($hoje);
                    @endphp
                    <tr>
                        <td><strong>{{ $lote->item->nome }}</strong><br><small style="color: #6b7280;">{{ $lote->item->unidade_medida }}</small></td>
                        <td class="text-center"><strong>{{ $lote->quantidade }}</strong></td>
                        <td class="text-center">{{ $lote->data_validade->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($estaVencido)
                                <span class="dias-restantes urgente">Vencido há {{ abs($diasRestantes) }} dias</span>
                            @elseif($diasRestantes <= 7)
                                <span class="dias-restantes urgente">{{ $diasRestantes }} dias</span>
                            @elseif($diasRestantes <= 30)
                                <span class="dias-restantes medio">{{ $diasRestantes }} dias</span>
                            @else
                                <span class="dias-restantes normal">{{ $diasRestantes }} dias</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($estaVencido)
                                <span class="badge-vencido">VENCIDO</span>
                            @elseif($diasRestantes <= 7)
                                <span class="badge-vencido">URGENTE</span>
                            @else
                                <span class="badge-vencendo">VENCENDO</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #059669;">
            <p style="font-size: 16px; font-weight: bold;">✅ Nenhum lote próximo ao vencimento nos próximos 90 dias!</p>
        </div>
    @endif

    <div class="footer">
        <p>Sistema de Controle de Estoque - CEVESP</p>
    </div>
</body>
</html>

