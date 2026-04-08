<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horários — Todas as Turmas</title>
    <style>
        @page { 
            size: A4 landscape; 
            margin: 15mm; 
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
            background: #fff;
            line-height: 1.3;
            text-align: center;
        }

        .page {
            page-break-after: always;
            width: 100%;
            margin: 0 auto;
        }

        .page:last-child { page-break-after: auto; }

        .report-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-header h1 {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .report-header h2 {
            font-size: 18px;
            font-weight: normal;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 2px solid #000;
            margin: 0 auto;
        }

        th, td {
            border: 1.5px solid #000;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        /* Group Headers */
        .header-row-groups th {
            font-size: 13px;
            padding: 8px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .bg-matutino { background-color: #bcd4e6; }
        .bg-intervalo { background-color: #d1e8e2; }

        /* Time Headers */
        .header-row-times th {
            font-size: 10px;
            padding: 12px 4px;
            font-weight: normal;
            background-color: #f0f0f0;
        }
        .header-row-times th.intervalo-time { background-color: #d1e8e2; }

        /* Left Side (Days) */
        .day-row-title {
            background-color: #000;
            color: #fff;
            width: 55px;
            font-weight: bold;
            font-size: 18px;
            height: 82px; /* Slightly smaller to fit 5 rows */
        }

        /* Cells */
        td {
            height: 82px; /* Slightly smaller to fit 5 rows */
            padding: 8px;
            font-size: 11px;
        }

        .subject-name {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 5px;
            display: block;
            color: #000;
        }
        .teacher-name {
            font-size: 10px;
            color: #555;
            display: block;
            font-style: italic;
        }

        .cell-empty { background-color: #fff; }
        .cell-intervalo { background-color: #fafafa; }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            color: #777;
            border-top: 1px solid #ccc;
            padding-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    @foreach($allData as $data)
    <div class="page">
        <div class="report-header">
            <h1>ClassFlow - Sistema de Gestão Escolar</h1>
            <h2>Horário de Aulas - Turma: {{ $data['schoolClass']->name }} (Ano Letivo: 2026)</h2>
        </div>

        <table>
            <thead>
                <tr class="header-row-groups">
                    <th rowspan="2" style="width: 50px; background: #fff; border: none;"></th>
                    <th colspan="2" class="bg-matutino">Bloco 1</th>
                    <th class="bg-intervalo">Intervalo</th>
                    <th colspan="4" class="bg-matutino">Bloco 2 (Pós-Intervalo)</th>
                </tr>
                <tr class="header-row-times">
                    <th style="width: 14%;">07:00 - 07:50<br><b>(1º Aula)</b></th>
                    <th style="width: 14%;">07:50 - 08:40<br><b>(2º Aula)</b></th>
                    <th class="intervalo-time" style="width: 9%;">08:40 - 09:00<br><b>Recreio</b></th>
                    <th style="width: 14%;">09:00 - 09:50<br><b>(3º Aula)</b></th>
                    <th style="width: 14%;">09:50 - 10:40<br><b>(4º Aula)</b></th>
                    <th style="width: 13%;">10:40 - 11:30<br><b>(5º Aula)</b></th>
                    <th style="width: 13%;">11:30 - 12:20<br><b>(6º Aula)</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach($days as $dayId => $dayName)
                <tr>
                    <td class="day-row-title">
                        {{ substr($dayName, 0, 3) }}
                    </td>
                    
                    @php 
                        $p1 = $data['grid'][1][$dayId] ?? null;
                        $p2 = $data['grid'][2][$dayId] ?? null;
                        $p3 = $data['grid'][3][$dayId] ?? null;
                        $p4 = $data['grid'][4][$dayId] ?? null;
                        $p5 = $data['grid'][5][$dayId] ?? null;
                        $p6 = $data['grid'][6][$dayId] ?? null;
                    @endphp

                    <td class="{{ !$p1 ? 'cell-empty' : '' }}">
                        @if($p1) <span class="subject-name">{{ $p1->classSubject->subject->name }}</span><span class="teacher-name">Prof. {{ $p1->classSubject->teacher->name }}</span> @endif
                    </td>
                    <td class="{{ !$p2 ? 'cell-empty' : '' }}">
                        @if($p2) <span class="subject-name">{{ $p2->classSubject->subject->name }}</span><span class="teacher-name">Prof. {{ $p2->classSubject->teacher->name }}</span> @endif
                    </td>
                    <td class="cell-intervalo"></td>
                    <td class="{{ !$p3 ? 'cell-empty' : '' }}">
                        @if($p3) <span class="subject-name">{{ $p3->classSubject->subject->name }}</span><span class="teacher-name">Prof. {{ $p3->classSubject->teacher->name }}</span> @endif
                    </td>
                    <td class="{{ !$p4 ? 'cell-empty' : '' }}">
                        @if($p4) <span class="subject-name">{{ $p4->classSubject->subject->name }}</span><span class="teacher-name">Prof. {{ $p4->classSubject->teacher->name }}</span> @endif
                    </td>
                    <td class="{{ !$p5 ? 'cell-empty' : '' }}">
                        @if($p5) <span class="subject-name">{{ $p5->classSubject->subject->name }}</span><span class="teacher-name">Prof. {{ $p5->classSubject->teacher->name }}</span> @endif
                    </td>
                    <td class="{{ !$p6 ? 'cell-empty' : '' }}">
                        @if($p6) <span class="subject-name">{{ $p6->classSubject->subject->name }}</span><span class="teacher-name">Prof. {{ $p6->classSubject->teacher->name }}</span> @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p><strong>Observações:</strong> Documento oficial gerado pelo ClassFlow ERP em {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    @endforeach
</body>
</html>
