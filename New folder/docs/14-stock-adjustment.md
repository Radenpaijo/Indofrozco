# 14 Stock Adjustment

## Tujuan

Memperbaiki perbedaan stok yang memiliki alasan resmi.

## Data

- item
- lokasi
- batch
- system quantity
- adjustment quantity
- final quantity
- reason
- user
- timestamp
- approval jika diperlukan

## Alur

```text
Identifikasi selisih
 ↓
Buat adjustment
 ↓
Review/Approval
 ↓
Posting
 ↓
Inventory diperbarui
 ↓
Audit log
```

## Prinsip

Tidak boleh ada perubahan stok tanpa histori.
