<?php

namespace App\Exports;

use App\Models\Bidang;
use App\Models\PeriodeLaporan;
use App\Models\Transaksi;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapKonsolidasiExport implements WithEvents, WithTitle
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function title(): string
    {
        return 'Rekap Konsolidasi ' . $this->bulan . '-' . $this->tahun;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(18);

                // 1. KOP SURAT (Letterhead)
                $sheet->setCellValue('A1', 'YAYASAN AL MANAR PESANGGRAHAN');
                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1B3236'));
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A2', 'LAPORAN BULANAN KONSOLIDASI KEUANGAN YAYASAN');
                $sheet->mergeCells('A2:G2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('2C4F3E'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $namaBulan = Carbon::createFromDate($this->tahun, $this->bulan, 1)->translatedFormat('F');
                $sheet->setCellValue('A3', "Periode Laporan: $namaBulan {$this->tahun}");
                $sheet->mergeCells('A3:G3');
                $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(11);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A4', 'Jl. Swadarma Raya No. 1, RT 01 / RW 08, Kel. Pesanggrahan, Kec. Pesanggrahan, Jakarta Selatan');
                $sheet->mergeCells('A4:G4');
                $sheet->getStyle('A4')->getFont()->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('5F7D6D'));
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Underline letterhead
                $sheet->getStyle('A4:G4')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

                // 2. SUMMARY TABLE (Tabel Ringkasan Konsolidasi)
                $sheet->setCellValue('A6', 'I. RINGKASAN LAPORAN KEUANGAN BIDANG');
                $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12);

                $headers = ['No', 'Nama Bidang', 'Saldo Awal', 'Pemasukan (+)', 'Pengeluaran (-)', 'Surplus/Defisit', 'Saldo Akhir'];
                $col = 'A';
                foreach ($headers as $h) {
                    $sheet->setCellValue($col . '7', $h);
                    $col++;
                }

                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1B3236'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ];
                $sheet->getStyle('A7:G7')->applyFromArray($headerStyle);
                $sheet->getRowDimension(7)->setRowHeight(25);

                // Fetch Bidang and their periods for the given month/year
                $bidangs = Bidang::orderBy('id', 'asc')->get();
                $row = 8;
                $no = 1;

                foreach ($bidangs as $b) {
                    $period = PeriodeLaporan::where('bidang_id', $b->id)
                        ->where('bulan', $this->bulan)
                        ->where('tahun', $this->tahun)
                        ->first();

                    $saldoAwal = $period ? (float)$period->saldo_awal : 0;
                    $pemasukan = $period ? (float)$period->transaksi()->where('kategori', 'pemasukan')->sum('jumlah') : 0;
                    $pengeluaran = $period ? (float)$period->transaksi()->where('kategori', 'pengeluaran')->sum('jumlah') : 0;
                    $selisih = $pemasukan - $pengeluaran;
                    $saldoAkhir = $saldoAwal + $selisih;

                    $sheet->setCellValue('A' . $row, $no++);
                    $sheet->setCellValue('B' . $row, $b->nama_bidang);
                    $sheet->setCellValue('C' . $row, $saldoAwal);
                    $sheet->setCellValue('D' . $row, $pemasukan);
                    $sheet->setCellValue('E' . $row, $pengeluaran);
                    $sheet->setCellValue('F' . $row, $selisih);
                    $sheet->setCellValue('G' . $row, $saldoAkhir);

                    // Number formats
                    $sheet->getStyle("C$row:G$row")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $row++;
                }

                // Total Row
                $sheet->setCellValue('A' . $row, '');
                $sheet->setCellValue('B' . $row, 'TOTAL GABUNGAN');
                $sheet->setCellValue('C' . $row, "=SUM(C8:C" . ($row - 1) . ")");
                $sheet->setCellValue('D' . $row, "=SUM(D8:D" . ($row - 1) . ")");
                $sheet->setCellValue('E' . $row, "=SUM(E8:E" . ($row - 1) . ")");
                $sheet->setCellValue('F' . $row, "=SUM(F8:F" . ($row - 1) . ")");
                $sheet->setCellValue('G' . $row, "=SUM(G8:G" . ($row - 1) . ")");

                $sheet->getStyle("A$row:G$row")->getFont()->setBold(true);
                $sheet->getStyle("C$row:G$row")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("A7:G$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Highlight total row
                $sheet->getStyle("A$row:G$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EBEAE4');

                // 3. DETAILED LIST (SIDE BY SIDE PEMASUKAN & PENGELUARAN)
                $row += 3;
                $sheet->setCellValue('A' . $row, 'II. DETAIL ALIRAN KAS GABUNGAN');
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);

                $row++;
                // Headers for Pemasukan and Pengeluaran side-by-side
                $sheet->setCellValue('A' . $row, 'PEMASUKAN KONSOLIDASI');
                $sheet->mergeCells("A$row:C$row");
                $sheet->setCellValue('E' . $row, 'PENGELUARAN KONSOLIDASI');
                $sheet->mergeCells("E$row:G$row");

                $sheet->getStyle("A$row:C$row")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C4F3E']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
                $sheet->getStyle("E$row:G$row")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '991B1B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                $row++;
                $sheet->setCellValue('A' . $row, 'Bidang');
                $sheet->setCellValue('B' . $row, 'Kategori / Uraian');
                $sheet->setCellValue('C' . $row, 'Jumlah');
                
                $sheet->setCellValue('E' . $row, 'Bidang');
                $sheet->setCellValue('B' . $row, 'Kategori / Uraian'); // Wait, column F is next
                $sheet->setCellValue('F' . $row, 'Kategori / Uraian');
                $sheet->setCellValue('G' . $row, 'Jumlah');

                $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);
                $sheet->getStyle("E$row:G$row")->getFont()->setBold(true);
                $sheet->getStyle("C$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("G$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Fetch all transactions for this month/year grouped by category
                $txList = Transaksi::with(['bidang', 'subKategori'])
                    ->whereHas('periodeLaporan', function ($q) {
                        $q->where('bulan', $this->bulan)->where('tahun', $this->tahun);
                    })
                    ->get();

                $inflow = $txList->where('kategori', 'pemasukan')->values();
                $outflow = $txList->where('kategori', 'pengeluaran')->values();

                $startDataRow = $row + 1;
                $maxRows = max($inflow->count(), $outflow->count());
                $currRow = $startDataRow;

                for ($i = 0; $i < $maxRows; $i++) {
                    // Pemasukan side
                    if ($i < $inflow->count()) {
                        $tx = $inflow[$i];
                        $sheet->setCellValue('A' . $currRow, $tx->bidang->nama_bidang);
                        $sheet->setCellValue('B' . $currRow, ($tx->subKategori ? $tx->subKategori->nama_sub_kategori : 'Umum') . ' - ' . $tx->uraian);
                        $sheet->setCellValue('C' . $currRow, (float)$tx->jumlah);
                        $sheet->getStyle('C' . $currRow)->getNumberFormat()->setFormatCode('#,##0.00');
                    }

                    // Pengeluaran side
                    if ($i < $outflow->count()) {
                        $tx = $outflow[$i];
                        $sheet->setCellValue('E' . $currRow, $tx->bidang->nama_bidang);
                        $sheet->setCellValue('F' . $currRow, ($tx->subKategori ? $tx->subKategori->nama_sub_kategori : 'Umum') . ' - ' . $tx->uraian);
                        $sheet->setCellValue('G' . $currRow, (float)$tx->jumlah);
                        $sheet->getStyle('G' . $currRow)->getNumberFormat()->setFormatCode('#,##0.00');
                    }
                    $currRow++;
                }

                // Add sub-totals
                $sheet->setCellValue('A' . $currRow, '');
                $sheet->setCellValue('B' . $currRow, 'TOTAL PEMASUKAN');
                $sheet->setCellValue('C' . $currRow, "=SUM(C$startDataRow:C" . ($currRow - 1) . ")");
                $sheet->getStyle('C' . $currRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("A$currRow:C$currRow")->getFont()->setBold(true);

                $sheet->setCellValue('E' . $currRow, '');
                $sheet->setCellValue('F' . $currRow, 'TOTAL PENGELUARAN');
                $sheet->setCellValue('G' . $currRow, "=SUM(G$startDataRow:G" . ($currRow - 1) . ")");
                $sheet->getStyle('G' . $currRow)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("E$currRow:G$currRow")->getFont()->setBold(true);

                // Add grid borders to detail section
                $sheet->getStyle("A" . ($startDataRow - 2) . ":C$currRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("E" . ($startDataRow - 2) . ":G$currRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // 4. SIGNATURE SECTION
                $currRow += 3;
                $sheet->setCellValue('B' . $currRow, 'Mengetahui,');
                $sheet->setCellValue('F' . $currRow, 'Jakarta, ' . Carbon::now()->translatedFormat('d F Y'));
                
                $currRow++;
                $sheet->setCellValue('B' . $currRow, 'Ketua Yayasan Al Manar');
                $sheet->setCellValue('F' . $currRow, 'Bendahara Umum / Rekap');

                $currRow += 4;
                $sheet->setCellValue('B' . $currRow, '___________________________');
                $sheet->setCellValue('F' . $currRow, '___________________________');

                $sheet->getStyle("B" . ($currRow - 5) . ":B$currRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F" . ($currRow - 5) . ":F$currRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
