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
            border-bottom: 2px solid #f97316;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            color: #ea580c;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 11px;
        }
        .info-box {
            background-color: #fff7ed;
            border-left: 4px solid #f97316;
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
        .badge-saida {
            background-color: #fed7aa;
            color: #9a3412;
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
        .summary-box {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .summary-box h3 {
            margin-bottom: 10px;
            color: #92400e;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #fde68a;
        }
        .summary-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Período: {{ $inicio }} a {{ $fim }}</p>
        <p>Gerado em: {{ $dataGeracao }}</p>
    </div>

    <div class="info-box">
        <p><strong>Total de movimentações de saída:</strong> {{ $movimentacoes->count() }}</p>
        <p><strong>Total de itens diferentes consumidos:</strong> {{ count($consumoPorItem) }}</p>
    </div>

    @if(count($consumoPorItem) > 0)
        <div class="summary-box">
            <h3>Resumo por Item</h3>
            @foreach($consumoPorItem as $consumo)
                <div class="summary-item">
                    <span><strong>{{ $consumo['item']->nome }}</strong> ({{ $consumo['item']->unidade_medida }})</span>
                    <span><strong>{{ $consumo['total'] }}</strong> unidades consumidas</span>
                </div>
            @endforeach
        </div>

        <table>
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Item</th>
                    <th class="text-center">Quantidade</th>
                    <th>Unidade</th>
                    <th>Responsável</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movimentacoes as $movimentacao)
                    <tr>
                        <td>
                            {{ $movimentacao->created_at->format('d/m/Y') }}<br>
                            <small style="color: #6b7280;">{{ $movimentacao->created_at->format('H:i:s') }}</small>
                        </td>
                        <td><strong>{{ $movimentacao->item->nome }}</strong></td>
                        <td class="text-center"><strong style="color: #ea580c;">{{ $movimentacao->quantidade }}</strong></td>
                        <td>{{ $movimentacao->item->unidade_medida }}</td>
                        <td>{{ $movimentacao->responsavel }}</td>
                        <td>{{ $movimentacao->observacoes ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <p style="font-size: 16px;">Nenhuma movimentação de saída encontrada no período especificado.</p>
        </div>
    @endif

    <div class="footer">
        <p>Sistema de Controle de Estoque - CEVESP</p>
    </div>
</body>
</html>

