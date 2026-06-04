<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>{{ $title ?? 'Laporan' }} — Tandi's Bakery</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.45;
            padding: 28px 32px 36px;
        }
        .header {
            border-bottom: 3px solid #f59e0b;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .brand-row {
            margin-bottom: 4px;
        }
        .brand-logo {
            height: 42px;
            width: auto;
            max-width: 220px;
        }
        .brand {
            font-size: 16pt;
            font-weight: bold;
            color: #b45309;
            letter-spacing: -0.02em;
        }
        .report-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 6px;
        }
        .report-subtitle {
            font-size: 9pt;
            color: #64748b;
            margin-top: 4px;
        }
        .meta-row {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .meta-row td {
            font-size: 8.5pt;
            color: #475569;
            vertical-align: top;
            padding: 0;
        }
        .meta-row td.right { text-align: right; }
        .filter-pill {
            display: inline-block;
            background: #fff7ed;
            color: #9a3412;
            border: 1px solid #fed7aa;
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 8.5pt;
            font-weight: bold;
            margin-top: 6px;
        }
        .summary-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 16px;
        }
        .summary-grid td {
            width: 33.33%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            vertical-align: top;
        }
        .summary-grid td.highlight {
            background: #fffbeb;
            border-color: #fde68a;
        }
        .summary-label {
            font-size: 8pt;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .summary-value {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .data-table th {
            background: #f1f5f9;
            color: #475569;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        .data-table th.num,
        .data-table td.num { text-align: right; }
        .data-table th.center,
        .data-table td.center { text-align: center; }
        .data-table td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 9pt;
        }
        .data-table tr:nth-child(even) td { background: #fafafa; }
        .data-table tr.total td {
            background: #fffbeb;
            font-weight: bold;
            color: #78350f;
            border-top: 2px solid #f59e0b;
        }
        .data-table tr.opening td {
            background: #fffbeb;
            font-weight: bold;
        }
        .muted { color: #94a3b8; }
        .day-block {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .day-head {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 4px 4px 0 0;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 9.5pt;
            color: #92400e;
        }
        .tx-head {
            background: #f8fafc;
            padding: 6px 10px;
            font-size: 8.5pt;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }
        .badge {
            display: inline-block;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            background: #e2e8f0;
            color: #334155;
        }
        .badge-emerald { background: #d1fae5; color: #065f46; }
        .badge-sky { background: #e0f2fe; color: #075985; }
        .badge-violet { background: #ede9fe; color: #5b21b6; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .footer {
            position: fixed;
            bottom: 16px;
            left: 32px;
            right: 32px;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
        .page-num:before { content: counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand-row">
            <img src="{{ public_path('images/tandis-logo.png') }}" alt="Tadi's Homemade Bakery" class="brand-logo" />
        </div>
        <div class="report-title">{{ $title ?? 'Laporan' }}</div>
        @if (! empty($filterLabel))
            <div class="filter-pill">{{ $filterLabel }}</div>
        @endif
        <table class="meta-row">
            <tr>
                <td>Dicetak: {{ \App\Support\PdfExporter::generatedAt() }}</td>
                <td class="right">Halaman <span class="page-num"></span></td>
            </tr>
        </table>
    </div>

    @yield('pdf-body')

    <div class="footer">
        Dokumen ini dihasilkan otomatis dari sistem Tadi's Homemade Bakery · {{ $title ?? 'Laporan' }}
    </div>
</body>
</html>
