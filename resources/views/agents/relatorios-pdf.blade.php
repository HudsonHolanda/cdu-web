<!doctype html>
<html lang="pt-BR">
<head><meta charset="utf-8"><title>Relatório geral de denúncias</title><style>body{font-family:DejaVu Sans,sans-serif;color:#17251d;font-size:11px}h1{color:#1d3b34;margin-bottom:4px}.muted{color:#68756d}.summary{display:flex;gap:12px;margin:18px 0}.card{border:1px solid #dce5de;padding:10px;flex:1}.card strong{display:block;font-size:20px;margin-top:4px}table{width:100%;border-collapse:collapse;margin-top:18px}th{background:#1d3b34;color:white;text-align:left}th,td{border:1px solid #dce5de;padding:7px}tr:nth-child(even){background:#f5f7f5}</style></head>
<body>
    <h1>Relatório geral de denúncias</h1><p class="muted">Gerado em {{ now()->format('d/m/Y H:i') }}</p>
    <div class="summary"><div class="card">Total<strong>{{ $summary['total'] }}</strong></div><div class="card">Urgentes<strong>{{ $summary['urgent'] }}</strong></div><div class="card">Resolvidas<strong>{{ $summary['resolved'] }}</strong></div></div>
    <table><thead><tr><th>ID</th><th>Ocorrência</th><th>Categoria</th><th>Bairro</th><th>Status</th><th>Prioridade</th><th>Data</th></tr></thead><tbody>@forelse ($rows as $row)<tr><td>{{ $row['id'] }}</td><td>{{ $row['title'] }}</td><td>{{ $row['category'] }}</td><td>{{ $row['district'] }}</td><td>{{ $row['status'] }}</td><td>{{ $row['priority'] }}</td><td>{{ $row['date'] }}</td></tr>@empty<tr><td colspan="7">Nenhuma ocorrência encontrada.</td></tr>@endforelse</tbody></table>
</body>
</html>
