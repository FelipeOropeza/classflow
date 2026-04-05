<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Boletim Escolar</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 20px; }
        .header { width: 100%; border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: 900; color: #000; letter-spacing: -1px; margin: 0; }
        .doc-title { font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 2px; margin-top: 4px; }
        
        .student-box { width: 100%; margin-bottom: 30px; border: 1px solid #e2e8f0; background-color: #f8fafc; border-radius: 4px; padding: 15px; }
        .student-box p { margin: 5px 0; font-size: 12px; }
        .student-box strong { color: #0f172a; display: inline-block; width: 80px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th { background-color: #f1f5f9; padding: 12px 10px; text-align: center; border-bottom: 2px solid #cbd5e1; font-size: 10px; text-transform: uppercase; color: #475569; letter-spacing: 1px; }
        th.td-left { text-align: left; }
        td { padding: 12px 10px; text-align: center; border-bottom: 1px solid #e2e8f0; }
        td.td-left { text-align: left; font-weight: bold; color: #334155; }
        
        .highlight { background-color: #f8fafc; font-weight: bold; }
        .attention { color: #dc2626; font-weight: bold; }
        
        .signatures { width: 100%; margin-top: 80px; }
        .sig-box { width: 48%; display: inline-block; text-align: center; }
        .sig-line { border-top: 1px solid #0f172a; width: 80%; margin: 0 auto; padding-top: 8px; font-size: 10px; text-transform: uppercase; font-weight: bold; color: #475569; }
        
        .footer { position: absolute; bottom: 30px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px; margin: 0 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="logo">ClassFlow</h1>
        <div class="doc-title">Documento Escolar Oficial • {{ $year }}</div>
    </div>

    <div class="student-box">
        <p><strong>Aluno(a):</strong> {{ $student->name }}</p>
        <p><strong>Matrícula:</strong> {{ $student->registration_number }}</p>
        <p><strong>Turma:</strong> {{ $className }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="td-left">Componente Curricular</th>
                @foreach($terms as $term)
                    <th>{{ $term->name }}</th>
                @endforeach
                <th class="highlight">Média Anual</th>
                <th>Faltas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $item)
                <tr>
                    <td class="td-left">{{ $item['name'] }}</td>
                    @foreach($item['terms'] as $term)
                        <td class="{{ (is_numeric($term['average']) && $term['average'] < 7) ? 'attention' : '' }}">
                            {{ $term['average'] }}
                        </td>
                    @endforeach
                    <td class="highlight {{ (is_numeric($item['final_average']) && $item['final_average'] < 7) ? 'attention' : '' }}">
                        {{ $item['final_average'] }}
                    </td>
                    <td>{{ $item['total_absences'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line">Diretoria Acadêmica</div>
        </div>
        <div class="sig-box">
            <div class="sig-line">Assinatura do Responsável</div>
        </div>
    </div>

    <div class="footer">
        Gerado eletronicamente em {{ now()->format('d/m/Y \à\s H:i') }} pelo sistema de inteligência ClassFlow.<br>
        A autenticidade deste documento e a verificação das notas de recuperação podem ser solicitadas na secretaria.
    </div>
</body>
</html>
