<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\PurchaseOrder;
use App\Models\PermintaanBarang;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $reportType = $request->input('type', 'gudang'); // options: 'gudang', 'pembelian', 'produksi'

        $reportData = $this->getReportData($reportType, $startDate, $endDate);
        $perPage = 15;
        $page = max(1, (int) $request->input('page', 1));
        $sumTotalPO = 0;

        if ($reportType === 'gudang') {
            $allItems = collect();
            foreach ($reportData['masuk'] as $m) {
                $m->jenis = 'MASUK';
                $allItems->push($m);
            }
            foreach ($reportData['keluar'] as $k) {
                $k->jenis = 'KELUAR';
                $allItems->push($k);
            }
            $allItems = $allItems->sortByDesc('tanggal')->values();
        } elseif ($reportType === 'pembelian') {
            $allItems = collect();
            foreach ($reportData['pos'] as $po) {
                foreach ($po->details as $d) {
                    $itemTotal = (float) $d->jumlah * (float) $d->harga_satuan;
                    $sumTotalPO += $itemTotal;
                    $allItems->push((object) [
                        'po' => $po,
                        'detail' => $d,
                        'item_total' => $itemTotal,
                    ]);
                }
            }
        } else {
            $allItems = collect();
            foreach ($reportData['permintaans'] as $req) {
                foreach ($req->details as $d) {
                    $allItems->push((object) [
                        'req' => $req,
                        'detail' => $d,
                    ]);
                }
            }
        }

        $paginatedItems = new LengthAwarePaginator(
            $allItems->forPage($page, $perPage)->values(),
            $allItems->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('reports.index', compact(
            'reportData',
            'paginatedItems',
            'sumTotalPO',
            'startDate',
            'endDate',
            'reportType'
        ));
    }

    public function print(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $reportType = $request->input('type', 'gudang');

        $reportData = $this->getReportData($reportType, $startDate, $endDate);

        return view('reports.print', compact('reportData', 'startDate', 'endDate', 'reportType'));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $reportType = $request->input('type', 'gudang');

        $reportData = $this->getReportData($reportType, $startDate, $endDate);
        
        $filename = "Laporan_{$reportType}_{$startDate}_to_{$endDate}.csv";
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($reportType, $reportData) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            if ($reportType === 'gudang') {
                // Header
                fputcsv($file, ['Tanggal', 'Jenis Mutasi', 'Nomor Transaksi', 'Kode Barang', 'Nama Barang', 'Jumlah', 'No. Surat Jalan / Permintaan', 'Staf Gudang']);
                
                // Rows
                foreach ($reportData['masuk'] as $m) {
                    fputcsv($file, [
                        $m->tanggal->format('Y-m-d H:i'),
                        'Masuk',
                        $m->no_masuk,
                        $m->barang->kode_barang ?? '-',
                        $m->snapshot_nama_barang ?? ($m->barang->nama_barang ?? '-'),
                        $m->jumlah,
                        $m->no_surat_jalan ?? '-',
                        $m->user->name ?? '-'
                    ]);
                }

                foreach ($reportData['keluar'] as $k) {
                    fputcsv($file, [
                        $k->tanggal->format('Y-m-d H:i'),
                        'Keluar',
                        $k->permintaanBarang->no_permintaan ?? 'MANUAL',
                        $k->barang->kode_barang ?? '-',
                        $k->snapshot_nama_barang ?? ($k->barang->nama_barang ?? '-'),
                        $k->jumlah,
                        $k->permintaanBarang->no_permintaan ?? '-',
                        $k->user->name ?? '-'
                    ]);
                }
            } elseif ($reportType === 'pembelian') {
                // Header
                fputcsv($file, ['No. PO', 'Tanggal', 'Supplier', 'Staf Purchasing', 'No. PR', 'Status PO', 'Kode Barang', 'Nama Barang', 'Jumlah Pesan', 'Harga Satuan', 'Total Harga']);
                
                foreach ($reportData['pos'] as $po) {
                    foreach ($po->details as $d) {
                        fputcsv($file, [
                            $po->no_po,
                            $po->tanggal ? \Carbon\Carbon::parse($po->tanggal)->format('Y-m-d') : '-',
                            $po->supplier->nama_supplier ?? '-',
                            $po->user->name ?? '-',
                            $po->permintaanPembelian->no_pr ?? '-',
                            ucfirst($po->status),
                            $d->barang->kode_barang ?? '-',
                            $d->snapshot_nama_barang ?? ($d->barang->nama_barang ?? '-'),
                            $d->jumlah,
                            $d->harga_satuan,
                            $d->jumlah * $d->harga_satuan
                        ]);
                    }
                }
            } else { // produksi
                // Header
                fputcsv($file, ['No. Permintaan', 'Tanggal', 'Staf Pemohon', 'Status Permintaan', 'Kode Barang', 'Nama Barang', 'Jumlah Diminta']);
                
                foreach ($reportData['permintaans'] as $req) {
                    foreach ($req->details as $d) {
                        fputcsv($file, [
                            $req->no_permintaan,
                            $req->tanggal ? \Carbon\Carbon::parse($req->tanggal)->format('Y-m-d') : '-',
                            $req->user->name ?? '-',
                            ucfirst($req->status),
                            $d->barang->kode_barang ?? '-',
                            $d->barang->nama_barang ?? '-',
                            $d->jumlah
                        ]);
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getReportData($type, $startDate, $endDate)
    {
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();

        $data = [];

        if ($type === 'gudang') {
            $data['masuk'] = BarangMasuk::with(['barang', 'user'])
                ->whereBetween('tanggal', [$start, $end])
                ->orderBy('tanggal', 'asc')
                ->get();

            $data['keluar'] = BarangKeluar::with(['barang', 'user', 'permintaanBarang'])
                ->whereBetween('tanggal', [$start, $end])
                ->orderBy('tanggal', 'asc')
                ->get();
        } elseif ($type === 'pembelian') {
            $data['pos'] = PurchaseOrder::with(['supplier', 'user', 'permintaanPembelian', 'details.barang'])
                ->whereBetween('tanggal', [$start, $end])
                ->orderBy('tanggal', 'asc')
                ->get();
        } else { // produksi
            $data['permintaans'] = PermintaanBarang::with(['user', 'details.barang'])
                ->whereBetween('tanggal', [$start, $end])
                ->orderBy('tanggal', 'asc')
                ->get();
        }

        return $data;
    }
}
