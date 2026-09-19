# 05 Master Data

## Barang

Relasi:

```text
Category 1---N Item
Unit     1---N Item
```

## Supplier

Supplier dapat memiliki banyak PO.

```text
Supplier 1---N PO
```

## Gudang & Lokasi

```text
Warehouse 1---N Location
```

## Data status

Gunakan status aktif/nonaktif agar data lama tidak harus dihapus.

## Soft delete

Gunakan soft delete untuk master yang sudah pernah dipakai transaksi jika diperlukan agar histori tetap aman.
