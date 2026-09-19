# INDOFROZ WMS

## Warehouse & Inventory Management System

Dokumentasi utama untuk pengembangan sistem inventory dan warehouse pada operasional cold storage.

> **Prinsip utama:** sistem harus mengikuti proses bisnis yang disepakati dengan client. Jangan menambahkan otomatisasi, approval, perhitungan, atau business rule yang belum didefinisikan.

## Tujuan

- Mencatat proses pengadaan sampai barang diterima.
- Mengelola stok berdasarkan barang, batch/tanggal, gudang, dan lokasi.
- Mencatat barang keluar untuk kebutuhan produksi.
- Mendukung pelacakan transaksi dan histori.
- Menyediakan laporan operasional.

## Alur utama

```text
PR
 ↓
Approval
 ↓
PO
 ↓
Barang Datang
 ↓
Receiving / Pemeriksaan
 ↓
Put Away / Penempatan
 ↓
Inventory
 ↓
Material Request Produksi
 ↓
Picking
 ↓
Barang Keluar
 ↓
Inventory Berkurang
 ↓
Report / Audit Trail
```

## Modul

1. Authentication & Authorization
2. Dashboard
3. Master Data
4. Purchase Request
5. Purchase Order
6. Receiving
7. Put Away
8. Inventory
9. Material Request
10. Picking & Outbound
11. FIFO
12. Stock Opname
13. Stock Adjustment
14. Stock Mutation
15. Reporting
16. Audit Log

## Status dokumentasi

- **DEFINED**: sudah menjadi requirement.
- **TBD**: belum diputuskan client.
- **OPTIONAL**: hanya dibuat jika dibutuhkan.

## Teknologi

- Laravel
- PHP
- MySQL
- HTML/CSS
- JavaScript

## Aturan pengembangan

Sebelum mengubah database atau business logic, baca dokumen terkait di `/docs`.

