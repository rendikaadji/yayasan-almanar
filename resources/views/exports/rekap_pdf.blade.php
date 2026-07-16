<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Konsolidasi Keuangan - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1E293B;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #1B3236;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-family: 'Forum', serif;
            font-size: 18px;
            color: #1B3236;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 12px;
            color: #2C4F3E;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .header .period {
            font-size: 11px;
            font-style: italic;
            color: #1E293B;
            margin-bottom: 4px;
        }
        .header .address {
            font-size: 9px;
            color: #5F7D6D;
            margin: 0;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1B3236;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #EBEAE4;
            padding-bottom: 4px;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table-data th, .table-data td {
            border: 1px solid #CBD5E1;
            padding: 6px 8px;
            text-align: left;
            vertical-align: middle;
        }
        .table-data th {
            background-color: #1B3236;
            color: #FFFFFF;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
        }
        .table-data .text-right {
            text-align: right;
        }
        .table-data .text-center {
            text-align: center;
        }
        .table-data .total-row {
            background-color: #F8FAFC;
            font-weight: bold;
        }
        .two-column-wrapper {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }
        .two-column-wrapper td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .column-title {
            font-weight: bold;
            color: #FFFFFF;
            padding: 6px;
            text-align: center;
            font-size: 10px;
            margin-bottom: 0;
        }
        .title-pemasukan {
            background-color: #2C4F3E;
        }
        .title-pengeluaran {
            background-color: #991B1B;
        }
        .signatures {
            width: 100%;
            margin-top: 40px;
            border: none;
            border-collapse: collapse;
        }
        .signatures td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signatures .space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- 1. KOP SURAT (Letterhead) -->
    <div class="header">
        <h1>Yayasan Al Manar Pesanggrahan</h1>
        <h2>Laporan Bulanan Konsolidasi Keuangan</h2>
        <div class="period">Bulan: {{ $namaBulan }} {{ $tahun }}</div>
        <p class="address">Jl. Swadarma Raya No. 1, RT 01 / RW 08, Kel. Pesanggrahan, Kec. Pesanggrahan, Jakarta Selatan</p>
    </div>

    <!-- 2. SUMMARY SECTION -->
    <div class="section-title">I. Ringkasan Laporan Bidang</div>
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Nama Bidang</th>
                <th style="width: 15%;">Saldo Awal</th>
                <th style="width: 15%;">Pemasukan (+)</th>
                <th style="width: 15%;">Pengeluaran (-)</th>
                <th style="width: 15%;">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bidangData as $index => $b)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $b['nama'] }}</td>
                    <td class="text-right">Rp {{ number_format($b['saldo_awal'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($b['pemasukan'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($b['pengeluaran'], 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">Rp {{ number_format($b['saldo_akhir'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-center">TOTAL GABUNGAN</td>
                <td class="text-right">Rp {{ number_format($totals['saldo_awal'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totals['pemasukan'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totals['pengeluaran'], 0, ',', '.') }}</td>
                <td class="text-right" style="color: #2C4F3E;">Rp {{ number_format($totals['saldo_akhir'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="page-break-before: always;"></div>

    <!-- 3. DETAILED LIST (2 COLUMNS) -->
    <div class="section-title">II. Detail Aliran Kas Gabungan</div>
    <table class="two-column-wrapper">
        <tr>
            <!-- PEMASUKAN COLUMN (LEFT) -->
            <td style="width: 48%;">
                <div class="column-title title-pemasukan">PEMASUKAN KONSOLIDASI</div>
                <table class="table-data" style="margin-top: 5px;">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Bidang</th>
                            <th style="width: 45%;">Kategori / Uraian</th>
                            <th style="width: 25%;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inflow as $tx)
                            <tr>
                                <td style="font-size: 9px;">{{ $tx->bidang->nama_bidang }}</td>
                                <td style="font-size: 9px;">{{ $tx->subKategori ? $tx->subKategori->nama_sub_kategori : 'Umum' }} - {{ $tx->uraian }}</td>
                                <td class="text-right" style="font-size: 9px;">Rp {{ number_format($tx->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center" style="font-style: italic; color: #64748B;">Tidak ada transaksi pemasukan</td>
                            </tr>
                        @endforelse
                        <tr class="total-row">
                            <td colspan="2">TOTAL PEMASUKAN</td>
                            <td class="text-right">Rp {{ number_format($totals['pemasukan'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <!-- MARGIN COL -->
            <td style="width: 4%;"></td>
            <!-- PENGELUARAN COLUMN (RIGHT) -->
            <td style="width: 48%;">
                <div class="column-title title-pengeluaran">PENGELUARAN KONSOLIDASI</div>
                <table class="table-data" style="margin-top: 5px;">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Bidang</th>
                            <th style="width: 45%;">Kategori / Uraian</th>
                            <th style="width: 25%;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($outflow as $tx)
                            <tr>
                                <td style="font-size: 9px;">{{ $tx->bidang->nama_bidang }}</td>
                                <td style="font-size: 9px;">{{ $tx->subKategori ? $tx->subKategori->nama_sub_kategori : 'Umum' }} - {{ $tx->uraian }}</td>
                                <td class="text-right" style="font-size: 9px;">Rp {{ number_format($tx->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center" style="font-style: italic; color: #64748B;">Tidak ada transaksi pengeluaran</td>
                            </tr>
                        @endforelse
                        <tr class="total-row">
                            <td colspan="2">TOTAL PENGELUARAN</td>
                            <td class="text-right">Rp {{ number_format($totals['pengeluaran'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- 4. SIGNATURE SECTION -->
    <table class="signatures">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Ketua Yayasan Al Manar</strong>
                <div class="space"></div>
                ___________________________
            </td>
            <td>
                Jakarta, {{ $tanggalCetak }}<br>
                <strong>Bendahara Umum / Rekap</strong>
                <div class="space"></div>
                ___________________________
            </td>
        </tr>
    </table>

</body>
</html>
