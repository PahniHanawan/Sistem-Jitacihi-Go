<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - {{ $period->period_name }}</title>
    <!-- Kita hanya menggunakan CSS dasar untuk cetak agar lebih ringan dan konsisten -->
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header .logo-wrapper {
            display: inline-block;
            margin-bottom: 10px;
        }
        .header .logo-box {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366F1, #7C3AED);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border: 2px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .header .logo-box span {
            color: white;
            font-size: 24px;
            font-weight: 800;
        }
        .header h1 {
            margin: 5px 0 5px 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            font-weight: normal;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            margin-top: 30px;
            border-left: 4px solid #333;
            padding-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-muted { color: #888; }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 30%;
            text-align: center;
        }
        .signature-box.right {
            text-align: right;
        }
        .signature-space {
            height: 80px;
        }
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        .print-btn {
            background: #4F46E5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .print-btn:hover {
            background: #4338CA;
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn no-print">🖨️ Cetak Dokumen</button>

    <div class="header">
        <!-- Logo - FIXED: No external image -->
        <div class="logo-wrapper">
            <div class="logo-box">
                <span>JG</span>
            </div>
        </div>
        <h1>Laporan Hasil Keputusan Promosi</h1>
        <h2>Toko Jitanichi Go</h2>
        <p>Periode Evaluasi: {{ $period->period_name }} ({{ \Carbon\Carbon::parse($period->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($period->end_date)->format('d M Y') }})</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
    </div>

    @if($rankings->isEmpty())
        <p class="text-center text-muted">Belum ada data pemeringkatan untuk periode ini.</p>
    @else
        <div class="section-title">1. Bobot Kriteria (AHP)</div>
        <table>
            <thead>
                <tr>
                    <th width="10%" class="text-center">No</th>
                    <th>Kode Kriteria</th>
                    <th>Nama Kriteria</th>
                    <th class="text-center">Bobot</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ahpResults as $index => $res)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ optional($res->criterion)->criterion_code }}</td>
                    <td>{{ optional($res->criterion)->criterion_name }}</td>
                    <td class="text-center">{{ number_format($res->weight * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-title">2. Peringkat SAW & Keputusan Diskon</div>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">Rank</th>
                    <th width="15%">Kode Produk</th>
                    <th width="25%">Nama Produk</th>
                    <th width="15%" class="text-center">Nilai Preferensi (Vi)</th>
                    <th width="20%">Keputusan Diskon</th>
                    <th width="20%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankings as $rank)
                <tr>
                    <td class="text-center"><b>{{ $rank->rank }}</b></td>
                    <td>{{ optional($rank->productAssessment->product)->product_code }}</td>
                    <td>{{ optional($rank->productAssessment->product)->product_name }}</td>
                    <td class="text-center">{{ number_format($rank->preference_value, 4) }}</td>
                    <td>
                        @if(optional($rank->promotionDecision)->discount_type)
                            <span class="badge badge-success">{{ $rank->promotionDecision->discount_type }}</span>
                        @else
                            <span class="badge badge-warning">Menunggu</span>
                        @endif
                    </td>
                    <td>
                        {{ optional($rank->promotionDecision)->reason ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-title">3. Ringkasan Statistik</div>
        <table>
            <tr>
                <th style="width:30%">Total Produk Diranking</th>
                <td>{{ $rankings->count() }}</td>
            </tr>
            <tr>
                <th>Produk dengan Keputusan Diskon</th>
                <td>{{ $rankings->filter(function($r) { return optional($r->promotionDecision)->discount_type; })->count() }}</td>
            </tr>
            <tr>
                <th>Nilai Preferensi Tertinggi</th>
                <td>{{ $rankings->first() ? number_format($rankings->first()->preference_value, 4) : '-' }}</td>
            </tr>
            <tr>
                <th>Nilai Preferensi Terendah</th>
                <td>{{ $rankings->last() ? number_format($rankings->last()->preference_value, 4) : '-' }}</td>
            </tr>
        </table>

        <div class="footer">
            <div class="signature-box" style="visibility: hidden;">
                <p>Mengetahui,</p>
                <div class="signature-space"></div>
                <p><b>____________________</b></p>
            </div>
            <div class="signature-box"></div>
            <div class="signature-box right">
                <p>Mengetahui, Pimpinan/Owner</p>
                <div class="signature-space"></div>
                <p><b>____________________</b></p>
            </div>
        </div>
    @endif

    <script>
        // Opsional: otomatis buka dialog print
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>