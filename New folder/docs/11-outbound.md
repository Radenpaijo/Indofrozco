# 11 Outbound

## Alur

```text
Material Request
 ↓
Warehouse menerima
 ↓
Cek stok
 ↓
Tentukan batch/lokasi
 ↓
Picking
 ↓
Verifikasi
 ↓
Issue
 ↓
Stok berkurang
```

## Transaction principle

Quantity yang mengurangi inventory adalah quantity yang benar-benar di-issue.

## Tidak boleh

- Mengurangi stok hanya karena request dibuat.
- Mengubah stok langsung dari halaman item.
- Menghapus transaksi outbound yang sudah memengaruhi stok tanpa mekanisme reversal yang aman.
