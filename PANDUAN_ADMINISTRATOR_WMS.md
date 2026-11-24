# PANDUAN LENGKAP INDOFROZ WMS: SISTEM, HALAMAN, CRUD & ALUR OPERASIONAL (ROLE ADMINISTRATOR)

---

## DAFTAR ISI
1. [Hak Akses & Peran Administrator](#1-hak-akses--peran-administrator)
2. [Alur Bisnis & Siklus Operasional Gudang (End-to-End Workflow)](#2-alur-bisnis--siklus-operasional-gudang-end-to-end-workflow)
3. [Penjelasan Lengkap Setiap Halaman & Operasi CRUD](#3-penjelasan-lengkap-setiap-halaman--operasi-crud)
   - [Modul Dashboard & Finansial](#31-modul-dashboard--finansial)
   - [Modul Master Data (7 Halaman)](#32-modul-master-data-7-halaman)
   - [Modul Pengadaan & Pembelian (2 Halaman)](#33-modul-pengadaan--pembelian-2-halaman)
   - [Modul Operasional Gudang (5 Halaman)](#34-modul-operasional-gudang-5-halaman)
   - [Modul Produksi & QC Defrost (1 Halaman)](#35-modul-produksi--qc-defrost-1-halaman)
   - [Modul Laporan & Analisis (1 Halaman)](#36-modul-laporan--analisis-1-halaman)
   - [Modul Pengaturan & Pengawasan (4 Halaman)](#37-modul-pengaturan--pengawasan-4-halaman)
4. [Tabel Matriks Otoritas & CRUD Administrator](#4-tabel-matriks-otoritas--crud-administrator)
5. [Otomatisasi Sistem Penting yang Dikawal Administrator](#5-otomatisasi-sistem-penting-yang-dikawal-administrator)

---

## 1. Hak Akses & Peran Administrator

Sebagai **Administrator (`admin`)**, Anda memegang tingkat akses tertinggi (*Super Privilege*) di dalam sistem INDOFROZ WMS:
- **Bypass Otorisasi Penuh**: Dapat mengakses, membuat, mengedit, dan mengeksekusi semua modul tanpa batasan departemen.
- **Wewenang Approval & Override**: Memiliki hak menyetujui (*approve*) atau menolak (*reject*) Permintaan Barang Produksi, membatalkan (*cancel*) dokumen Purchase Order (PO) maupun Permintaan Pembelian (PR), serta melakukan penyesuaian stok (*Stock Adjustment*).
- **Pengawasan Finansial & Mutasi**: Memantau perputaran uang stok, selisih susut bobot (*defrost shrinkage*), kartu stok buku besar (*ledger*), hingga jejak audit digital (*Audit Logs*) seluruh staf.

---

## 2. Alur Bisnis & Siklus Operasional Gudang (End-to-End Workflow)

Sistem dirancang mengintegrasikan rantai pasok makanan beku (*Cold Chain Management*) dengan metode **FEFO (First Expired, First Out)**:

```
[MASTER DATA SETUP]
   │
   ├─► 1. Kategori, Satuan, Tipe Gudang, & Rak Cold Storage
   ├─► 2. Master Barang (Batas Min/Max, Suhu Simpan, Harga Beli)
   └─► 3. Data Rekanan Supplier
         │
         ▼
[PENGADAAN / PROCUREMENT]
   │
   ├─► Permintaan Pembelian (PR) diterbitkan gudang saat stok kritis
   └─► Purchase Order (PO) disahkan Admin/Purchasing & dikirim ke Supplier
         │
         ▼
[PENERIMAAN GUDANG / INBOUND]
   │
   ├─► Truk pendingin tiba di dermaga bongkar
   ├─► Input Barang Masuk (Tarik referensi nomor PO)
   ├─► Pemeriksaan QC Inbound (Suhu kedatangan wajib beku & kondisi fisik)
   ├─► Pencatatan Nomor Batch/Lot & Tanggal Kedaluwarsa (Expired Date)
   └─► Alokasi penempatan ke Lokasi Rak Cold Room
         │
         ▼
[PENGELOLAAN INTERNAL GUDANG]
   │
   ├─► Pindah Rak / Lokasi (Mutasi internal tanpa mengubah total stok)
   ├─► Stock Opname (Audit fisik berkala)
   └─► Stock Adjustment (Penyesuaian resmi jika ditemukan selisih opname/rusak)
         │
         ├────────────────────────────────────────┬────────────────────────────────────────┐
         ▼                                        ▼                                        ▼
[PRODUKSI & QC DEFROST]                  [PENGELUARAN UMUM (OUTBOUND)]            [LAPORAN & AUDIT]
   │                                        │                                        │
   ├─► Produksi buat Permintaan (REQ)       ├─► Barang Keluar (Jual/Sampel/Musnah)   ├─► Kartu Stok Digital
   ├─► Admin/Gudang Approve                 └─► Sistem potong otomatis via FEFO      ├─► Laporan PDF & CSV
   ├─► Sistem alokasikan batch via FEFO                                              ├─► Rekonsiliasi Keuangan
   ├─► Ambil barang di Cold Room                                                     └─► Audit Log Staf
   ├─► QC Defrost (Timbang susut es)
   └─► Verifikasi selesai: Stok resmi terpotong
```

---

## 3. Penjelasan Lengkap Setiap Halaman & Operasi CRUD

---

### 3.1 Modul Dashboard & Finansial

#### Halaman: Dashboard Utama (`/dashboard`)
* **Fungsi Utama**: Pusat komando untuk memantau kesehatan operasional gudang dan ringkasan finansial inventaris.
* **Operasi Administrator**:
  * **Read (Pemantauan Data)**:
    * **Kartu Ringkasan Stok**: Total Kuantitas Fisik (Kg/Pcs), Total SKU Barang Aktif, Nilai Valuasi Inventaris (Rp).
    * **Peringatan Dini (Alerts)**: Indikator merah untuk stok di bawah batas minimum (*Understock*) dan batch yang mendekati masa kedaluwarsa (*Expiring Soon*).
    * **Panel Rekonsiliasi Keuangan**: Menghitung secara otomatis `Total Pembayaran PO` - `Nilai Barang Keluar (HPP)` - `Nilai Rusak/Susut/Selisih Opname` = **Selisih Bersih (Deviasi Finansial)**.
    * **Grafik Transaksi 7 Hari**: Visualisasi perbandingan barang masuk vs keluar.
    * **Aktivitas Terkini**: Rekap dokumen dan mutasi paling mutakhir.

---

### 3.2 Modul Master Data (7 Halaman)

*Catatan: Pada semua form edit master data, kode unik (`BRG-xxx`, `KAT-xxx`, `GDG-xxx`, dll.) dikunci sebagai readonly agar integritas relasi data transaksi tetap terjaga.*

#### 1. Kategori Barang (`/categories`)
* **Fungsi**: Mengelompokkan jenis produk (misal: Daging Sapi, Unggas, Seafood, Bumbu).
* **Operasi Administrator**:
  * **Create** (`/categories/create`): Tambah kategori baru. Input: Nama, Deskripsi. Kode `KAT-xxx` otomatis.
  * **Read**: Tabel daftar kategori, pencarian, dan pagination.
  * **Update** (`/categories/{id}/edit`): Ubah nama atau deskripsi kategori.
  * **Delete**: Hapus kategori (dicegah sistem jika sudah digunakan oleh barang).
  * **Toggle Status**: Aktifkan / Nonaktifkan kategori dengan satu klik.

#### 2. Satuan Barang (`/units`)
* **Fungsi**: Standarisasi ukuran dan satuan kemasan (misal: Kg, Gram, Karton, Pcs).
* **Operasi Administrator**:
  * **Create** (`/units/create`): Tambah satuan. Input: Nama Satuan, Simbol Singkatan (`Kg`, `Box`), Keterangan. Kode `STN-xxx` otomatis.
  * **Read**: Daftar seluruh satuan yang terdaftar.
  * **Update** (`/units/{id}/edit`): Perbarui nama atau simbol satuan.
  * **Delete**: Hapus satuan yang belum pernah terikat transaksi.
  * **Toggle Status**: Mengubah status aktif/nonaktif satuan.

#### 3. Tipe Gudang (`/warehouse-types`)
* **Fungsi**: Menentukan klasifikasi temperatur dan fungsi ruang (misal: *Cold Storage*, *Chiller*, *Dry Store*, *Ante Room*).
* **Operasi Administrator**:
  * **Create** (`/warehouse-types/create`): Tambah tipe gudang. Input: Nama Tipe, Keterangan. Kode `TIPE-xxx` otomatis.
  * **Read**: Melihat daftar tipe fasilitas penyimpanan.
  * **Update** (`/warehouse-types/{id}/edit`): Koreksi nama dan deskripsi tipe.
  * **Delete**: Hapus tipe fasilitas.
  * **Toggle Status**: Mengubah status aktif/nonaktif.

#### 4. Gudang (`/warehouses`)
* **Fungsi**: Manajemen gedung atau ruangan fisik penyimpanan berpendingin.
* **Operasi Administrator**:
  * **Create** (`/warehouses/create`): Tambah gudang baru. Input: Nama Gudang, Tipe Gudang, Alamat/Lokasi, Kapasitas Maksimum (Ton/Kg), Standar Suhu Operasional (misal: `-18°C s/d -22°C`). Kode `GDG-xxx` otomatis.
  * **Read**: Monitoring kapasitas tampung dan kondisi suhu operasional.
  * **Update** (`/warehouses/{id}/edit`): Koreksi data fasilitas atau penyesuaian kapasitas.
  * **Delete**: Hapus gudang (hanya jika gudang kosong tanpa data transaksi).
  * **Toggle Status**: Nonaktifkan gudang sementara jika sedang maintenance.

#### 5. Lokasi / Rak (`/locations`)
* **Fungsi**: Pemetaan titik simpan presisi (*Bin Location*) hingga ke nomor Rak, Baris (*Row*), dan Tingkat (*Level*).
* **Operasi Administrator**:
  * **Create** (`/locations/create`): Tambah titik rak. Input: Gudang Induk, Kode Rak/Label (misal: `RAK-A-01`), Baris, Tingkat (*Level*), Kapasitas Maksimum. Kode `LOK-xxx` otomatis.
  * **Read**: Filter lokasi per gudang untuk melihat ketersediaan rak.
  * **Update** (`/locations/{id}/edit`): Perbarui kapasitas dan koordinat rak.
  * **Delete**: Hapus rak yang kosong.
  * **Toggle Status**: Kunci rak jika rusak atau dalam perbaikan.

#### 6. Master Barang & Kartu Stok (`/barangs`)
* **Fungsi**: Master katalog seluruh produk, harga pokok beli, standar penyimpanan, serta kartu stok digital.
* **Operasi Administrator**:
  * **Create** (`/barangs/create`): Daftarkan barang. Input: Nama Barang, Kategori, Satuan, Batas Stok Minimum, Batas Stok Maksimum, Harga Pokok Beli, Estimasi Harga Jual, Rekomendasi Suhu Simpan (°C), Ambang Batas Hari Peringatan Kedaluwarsa (*Expiry Warning Days*). Kode `BRG-xxx` otomatis.
  * **Read**: Monitoring status stok (Merah = Kritis, Hijau = Aman), pencarian instan, dan rincian batch.
  * **Update** (`/barangs/{id}/edit`): Perbarui harga beli, batas minimum stok, atau deskripsi barang.
  * **Delete**: Hapus barang jika belum pernah ada transaksi.
  * **Aksi Khusus - Kartu Stok Digital** (`/barangs/{id}/stock-card`): Buku besar riwayat mutasi per item: Saldo Awal, Masuk (+), Keluar (-), Saldo Akhir, nomor referensi dokumen, dan operator yang mencatat.
  * **Aksi Khusus - Cetak Kartu Stok** (`/barangs/{id}/print-stock-card`): Cetak kartu stok ke format siap print/PDF untuk arsip fisik.

#### 7. Data Supplier (`/suppliers`)
* **Fungsi**: Basis data vendor dan pemasok bahan baku beku.
* **Operasi Administrator**:
  * **Create** (`/suppliers/create`): Tambah supplier. Input: Nama Perusahaan, Nama Kontak (PIC), Nomor Telepon/WhatsApp, Alamat Email, Alamat Lengkap, Syarat Pembayaran (*Term of Payment*, contoh: COD, Net 30). Kode `SUP-xxx` otomatis.
  * **Read**: Daftar seluruh supplier rekanan beserta kontak aktif.
  * **Update** (`/suppliers/{id}/edit`): Perbarui nomor kontak, PIC, atau alamat.
  * **Delete**: Hapus data supplier yang tidak aktif.

---

### 3.3 Modul Pengadaan & Pembelian (2 Halaman)

#### 1. Permintaan Pembelian / PR (`/permintaan-pembelian`)
* **Fungsi**: Pengajuan kebutuhan pengadaan bahan dari gudang sebelum diterbitkan pesanan resmi.
* **Operasi Administrator**:
  * **Create** (`/permintaan-pembelian/create`): Buat pengajuan PR. Input: Tanggal Kebutuhan, Gudang Pemohon, Alasan Pengadaan, dan Baris Barang Dinamis (Barang, Qty, Catatan Kebutuhan).
  * **Read**: Monitoring status PR (`Draft`, `Submitted`, `Approved`, `Cancelled`).
  * **Show** (`/permintaan-pembelian/{id}`): Tinjau rincian item barang yang diajukan.
  * **Aksi Khusus - Cancel PR**: Membatalkan pengajuan PR jika stok mencukupi atau ada perubahan anggaran.

#### 2. Pesanan Pembelian / PO (`/purchase-orders`)
* **Fungsi**: Dokumen kontrak pemesanan resmi ke pihak Supplier yang memuat harga beli dan pajak.
* **Operasi Administrator**:
  * **Create** (`/purchase-orders/create`): Terbitkan PO resmi. Dapat ditarik langsung dari referensi PR atau input manual. Input: Supplier, Gudang Tujuan, Tanggal Kirim, Ketentuan Pajak (PPN/PPH), Diskon, dan Rincian Barang (Item, Qty, Harga Satuan yang dihitung otomatis ke Grand Total). Nomor `PO-YYYYMMDD-xxx` otomatis.
  * **Read**: Monitoring status PO (`Draft`, `Sent`, `Partial`, `Completed`, `Cancelled`).
  * **Show** (`/purchase-orders/{id}`): Rincian lengkap nilai finansial PO dan histori penerimaan barangnya.
  * **Aksi Khusus - Print PO Resmi** (`/purchase-orders/{id}/print`): Mencetak Purchase Order profesional dengan kop perusahaan, rincian biaya, dan kolom tanda tangan otorisasi.
  * **Aksi Khusus - Cancel PO**: Membatalkan pesanan jika terjadi kendala pasokan dari supplier.

---

### 3.4 Modul Operasional Gudang (5 Halaman)

#### 1. Barang Masuk / Inbound Receipt (`/barang-masuk`)
* **Fungsi**: Penerimaan barang di dermaga bongkar, inspeksi QC suhu dingin, dan penetapan batch & kedaluwarsa.
* **Operasi Administrator**:
  * **Create** (`/barang-masuk/create`): Catat penerimaan.
    * *Mode PO*: Pilih No. PO untuk otomatis mengisi daftar barang dan kuantitas pesanan.
    * *Mode Manual*: Input langsung tanpa PO.
    * *Data QC & Cold Chain*: Wajib mengisi **Nomor Batch / Lot**, **Tanggal Kedaluwarsa**, **Suhu Kedatangan (°C)**, dan **Status QC (Lolos / Karantina / Tolak)**.
    * *Dampak Sistem*: Seketika menambah stok gudang, mengalokasikan batch baru, dan mencatat transaksi masuk di kartu stok.
  * **Read**: Histori seluruh dokumen penerimaan dan filter status QC.
  * **Show**: Rincian nomor batch dan bukti catatan suhu saat tiba.

#### 2. Barang Keluar / Outbound Issue (`/barang-keluar`)
* **Fungsi**: Pengeluaran barang umum non-produksi (Penjualan langsung, Sampel laboratorium, Pemusnahan rusak).
* **Operasi Administrator**:
  * **Create** (`/barang-keluar/create`): Input transaksi keluar. Pilih Kategori Pengeluaran, Gudang Asal, Tanggal, Item Barang, dan Qty.
  * *Otomatisasi FEFO*: Sistem otomatis memilih dan memotong batch yang tanggal kedaluwarsanya paling dekat terlebih dahulu.
  * **Read**: Daftar seluruh dokumen barang keluar beserta operator penanggung jawab.

#### 3. Cek Fisik Stok / Stock Opname (`/stock-opnames`)
* **Fungsi**: Audit pencocokan jumlah fisik nyata di Cold Storage dengan angka di sistem.
* **Operasi Administrator**:
  * **Create** (`/stock-opnames/create`): Pelaksanaan opname.
    * Pilih Gudang dan Lokasi Rak yang akan diperiksa.
    * Sistem otomatis menampilkan daftar seluruh barang dan stok sistemnya.
    * Masukkan angka hasil timbangan/hitung fisik riil.
    * Sistem secara instan menghitung selisih varian (positif/negatif).
  * **Read**: Arsip seluruh dokumen pelaksanaan stock opname.
  * **Show** (`/stock-opnames/{id}`): Tinjauan detail barang yang mengalami surplus atau defisit.

#### 4. Penyesuaian Stok / Stock Adjustment (`/stock-adjustments`)
* **Fungsi**: Menindaklanjuti selisih opname atau kerusakan fisik agar saldo sistem kembali sinkron dengan fisik riil.
* **Operasi Administrator**:
  * **Create** (`/stock-adjustments/create`): Input adjustment. Pilih Barang, Gudang, Tipe Penyesuaian (Penambahan / Pengurangan), Jumlah Koreksi, dan Berita Acara Alasan (misal: "Penyesuaian susut es hasil opname").
  * *Dampak Sistem*: Memperbarui saldo stok akhir dan mencatat jurnal koreksi ke buku besar kartu stok.
  * **Read**: Arsip berita acara penyesuaian stok.
  * **Show** (`/stock-adjustments/{id}`): Rincian dokumen adjustment beserta catatan pemeriksa.

#### 5. Pindah Rak / Lokasi (`/stock-mutations`)
* **Fungsi**: Memindahkan barang dan batch tertentu dari satu rak/gudang ke lokasi lain tanpa mengubah total stok agregat.
* **Operasi Administrator**:
  * **Create** (`/stock-mutations/create`): Input mutasi. Pilih Gudang Asal, Rak Asal, Barang & Batch spesifik, Qty yang dipindahkan, serta Gudang Tujuan dan Rak Tujuan.
  * **Read**: Catatan log riwayat pemindahan fisik barang di area gudang.
  * **Show** (`/stock-mutations/{id}`): Detail surat perintah pemindahan rak.

---

### 3.5 Modul Produksi & QC Defrost (1 Halaman)

#### Halaman: Permintaan Barang Produksi (`/permintaan-barang`)
* **Fungsi**: Pengajuan bahan baku beku untuk proses masak/repack produksi, disertai pencatatan susut timbangan setelah pencairan (*defrost*).
* **Operasi Administrator**:
  * **Create** (`/permintaan-barang/create`): Buat permintaan bahan. Input: No. Batch Target Produksi, Tanggal Pemakaian, dan Rincian Barang serta Qty yang diminta.
  * **Read**: Monitoring status permintaan (`Menunggu Persetujuan`, `Disetujui`, `Ditolak`, `Verifikasi QC Selesai`).
  * **Show** (`/permintaan-barang/{id}`): Tinjauan rinci bahan yang diminta dan alokasi batch yang disiapkan.
  * **Aksi Khusus - Approve (Persetujuan)**: Menyetujui pengajuan bahan. Sistem otomatis mengunci batch terbaik berdasarkan prinsip FEFO.
  * **Aksi Khusus - Reject (Penolakan)**: Menolak pengajuan dengan mencantumkan alasan.
  * **Aksi Khusus - Verify QC Defrost (`verify-qc`)**: Memasukkan **Berat Aktual Setelah Pencairan (Defrost Weight)**. Sistem otomatis menghitung **Persentase Susut Air (Shrinkage %)**. Data ini otomatis masuk ke laporan rendemen dan panel keuangan di Dashboard sebagai nilai susut.
  * **Aksi Khusus - Print Bukti Serah Terima** (`/permintaan-barang/{id}/print`): Cetak surat bukti serah terima bahan baku dari gudang ke tim produksi.

---

### 3.6 Modul Laporan & Analisis (1 Halaman)

#### Halaman: Pusat Laporan ERP (`/reports`)
* **Fungsi**: Rekapitulasi mutasi stok, laporan pengadaan, dan audit pemakaian bahan produksi.
* **Operasi Administrator**:
  * **Read (Filter & Analisis)**:
    * Filter berdasarkan rentang Tanggal Mulai dan Tanggal Akhir.
    * Memilih tipe: **Laporan Mutasi Barang Gudang**, **Laporan Pembelian & PO**, atau **Laporan Penggunaan Produksi**.
    * Tampilan data terpaginasi rapi dan responsif.
  * **Aksi Khusus - Cetak Laporan PDF** (`/reports/print`): Membuka tampilan siap cetak (*print-ready view*) untuk dicetak fisik atau disimpan ke format PDF.
  * **Aksi Khusus - Export Data CSV/Excel** (`/reports/export`): Mengunduh seluruh data mentah transaksi ke file CSV/Spreadsheet untuk kebutuhan analisis akuntansi lebih mendalam.

---

### 3.7 Modul Pengaturan & Pengawasan (4 Halaman)

#### 1. Manajemen Pengguna / Users (`/users`)
* **Fungsi**: Mengatur hak akses akun staf seluruh divisi perusahaan.
* **Operasi Administrator**:
  * **Create** (`/users/create`): Tambah staf baru. Input: Nama Lengkap, Username/Email, Password, dan Peran (*Role*): `admin`, `gudang`, `purchasing`, `produksi`, `manager`, `owner`.
  * **Read**: Daftar seluruh pengguna aktif dan rolenya.
  * **Update** (`/users/{id}/edit`): Ubah profil staf, ganti role, atau **Reset Password**.
  * **Delete**: Hapus akun staf yang sudah tidak bertugas (dilindungi agar tidak bisa menghapus akun sendiri).

#### 2. Audit Logs / Jejak Digital (`/audit-logs`)
* **Fungsi**: Perekam aktivitas seluruh pengguna (*black-box audit*) untuk transparansi dan pencegahan kecurangan.
* **Operasi Administrator**:
  * **Read**: Memantau kronologi aktivitas (Waktu, Nama Staf, Tipe Aksi: `Created`, `Updated`, `Deleted`, Modul yang disentuh, dan Alamat IP).
  * **Show** (`/audit-logs/{id}`): Tinjauan mendalam perbandingan nilai sebelum diubah (*old values*) versus nilai setelah diubah (*new values*).

#### 3. Keyboard Shortcuts (`/shortcuts`)
* **Fungsi**: Panduan tombol pintasan keyboard untuk mempercepat navigasi dan pengoperasian sistem oleh staf gudang tanpa mouse.

#### 4. Profil Administrator (`/profile`)
* **Fungsi**: Mengubah nama, alamat email, dan kata sandi pribadi Administrator yang sedang aktif.

---

## 4. Tabel Matriks Otoritas & CRUD Administrator

| Modul / Halaman | Create | Read | Update | Delete | Aksi Khusus Administrator |
| :--- | :---: | :---: | :---: | :---: | :--- |
| **Dashboard** (`/dashboard`) | - | ✓ | - | - | Analisis Deviasi Finansial & Peringatan Stok Kritis |
| **Kategori** (`/categories`) | ✓ | ✓ | ✓ (Nama/Ket) | ✓ | Toggle Status Aktif/Nonaktif |
| **Satuan** (`/units`) | ✓ | ✓ | ✓ (Nama/Simbol) | ✓ | Toggle Status Aktif/Nonaktif |
| **Tipe Gudang** (`/warehouse-types`) | ✓ | ✓ | ✓ | ✓ | Toggle Status Aktif/Nonaktif |
| **Gudang** (`/warehouses`) | ✓ | ✓ | ✓ | ✓ | Pengaturan Standar Suhu & Kapasitas |
| **Lokasi / Rak** (`/locations`) | ✓ | ✓ | ✓ | ✓ | Pemetaan Tingkat & Baris Rak |
| **Master Barang** (`/barangs`) | ✓ | ✓ | ✓ (Harga/Batas) | ✓ | **Kartu Stok Digital** & **Cetak PDF** |
| **Supplier** (`/suppliers`) | ✓ | ✓ | ✓ | ✓ | Rekanan Bisnis & Term of Payment |
| **Permintaan Pembelian (PR)** (`/permintaan-pembelian`) | ✓ | ✓ | - | - | **Cancel PR** & Konversi ke PO |
| **Purchase Order (PO)** (`/purchase-orders`) | ✓ | ✓ | - | - | **Cetak PO Resmi PDF** & **Cancel PO** |
| **Barang Masuk** (`/barang-masuk`) | ✓ | ✓ | - | - | Tarik Data PO, **Input Batch & QC Suhu** |
| **Barang Keluar** (`/barang-keluar`) | ✓ | ✓ | - | - | Auto-Deduct **Mesin FEFO** |
| **Permintaan Barang Produksi** (`/permintaan-barang`) | ✓ | ✓ | - | - | **Approve / Reject**, **QC Defrost Susut**, Cetak |
| **Stock Opname** (`/stock-opnames`) | ✓ | ✓ | - | - | Perhitungan Selisih Varian Fisik vs Sistem |
| **Stock Adjustment** (`/stock-adjustments`) | ✓ | ✓ | - | - | Sinkronisasi Saldo Stok Riil |
| **Mutasi Rak** (`/stock-mutations`) | ✓ | ✓ | - | - | Pemindahan Fisik Antar Rak/Gudang |
| **Laporan** (`/reports`) | - | ✓ | - | - | Filter Mutasi/PO/Produksi, **Print PDF**, **Export CSV** |
| **Manajemen User** (`/users`) | ✓ | ✓ | ✓ | ✓ | Atur Role & **Reset Kata Sandi** |
| **Audit Logs** (`/audit-logs`) | - | ✓ | - | - | Inspeksi Jejak Audit Perubahan Data |

---

## 5. Otomatisasi Sistem Penting yang Dikawal Administrator

1. **Mesin Alokasi FEFO (First Expired, First Out)**:
   Pada setiap transaksi pengeluaran (baik untuk produksi maupun barang keluar umum), sistem otomatis mengurutkan dan memotong saldo dari batch yang tanggal kedaluwarsanya paling mendekati hari ini guna mencegah produk makanan beku kadaluarsa di gudang.
2. **Integritas Penomoran Otomatis**:
   Seluruh dokumen transaksi (`PO-xxx`, `IN-xxx`, `OUT-xxx`, `REQ-xxx`, `OPN-xxx`, `ADJ-xxx`, `MUT-xxx`) dan master (`BRG-xxx`, `KAT-xxx`, `GDG-xxx`) di-generate oleh sistem generator unik sehingga tidak akan terjadi nomor ganda.
3. **Rekonsiliasi Nilai Keuangan Real-Time**:
   Dashboard secara otomatis menghitung selisih antara nilai barang yang dibeli (PO), nilai barang yang keluar resmi (HPP), dan nilai penyusutan (hasil QC Defrost + selisih opname), memberikan gambaran akurat terhadap potensi kerugian barang di gudang pendingin.

