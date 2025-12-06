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
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 11px;
        }
        .info-box {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
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
        .badge-critico {
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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Gerado em: {{ $dataGeracao }}</p>
    </div>

    <div class="info-box">
        <p><strong>Total de itens em estoque crítico:</strong> {{ $itens->count() }}</p>
        <p><strong>Definição:</strong> Itens onde a quantidade atual é menor ou igual ao estoque mínimo</p>
    </div>

    @if($itens->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Quantidade Atual</th>
                    <th class="text-center">Estoque Mínimo</th>
                    <th>Unidade</th>
                    <th class="text-center">MMC</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itens as $item)
                    <tr>
                        <td><strong>{{ $item->nome }}</strong></td>
                        <td class="text-center"><strong style="color: #dc2626;">{{ $item->quantidade_atual }}</strong></td>
                        <td class="text-center">{{ $item->estoque_minimo }}</td>
                        <td>{{ $item->unidade_medida }}</td>
                        <td class="text-center">{{ number_format($item->media_mensal_consumo, 2) }}</td>
                        <td class="text-center">
                            <span class="badge-critico">CRÍTICO</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #059669;">
            <p style="font-size: 16px; font-weight: bold;">✅ Nenhum item em estoque crítico no momento!</p>
        </div>
    @endif

    <div class="footer">
        <p>Sistema de Controle de Estoque - CEVESP</p>
    </div>
</body>
</html>

