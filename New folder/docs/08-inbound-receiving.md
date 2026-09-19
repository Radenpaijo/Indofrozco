# 08 Inbound Receiving

## Tujuan

Mencatat barang yang benar-benar diterima di gudang.

## Alur

```text
PO
 ↓
Barang datang
 ↓
Pemeriksaan dokumen & fisik
 ↓
Input quantity aktual
 ↓
Catat batch/tanggal jika digunakan
 ↓
Accepted / Partial / Rejected
 ↓
Put Away
```

## Aturan

Quantity receiving adalah quantity aktual, bukan otomatis disamakan dengan PO.

Contoh:

```text
PO       = 1.000 KG
Received =   950 KG
Rejected =    50 KG
```

## Pemeriksaan

Field pemeriksaan harus configurable sesuai proses client.
