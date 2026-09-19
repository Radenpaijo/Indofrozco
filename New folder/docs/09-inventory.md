# 09 Inventory

## Prinsip

Inventory adalah hasil dari transaksi resmi, bukan angka yang diubah langsung oleh user.

## Stok bertambah

- Receiving yang valid.

## Stok berkurang

- Outbound yang valid.

## Stok berubah tanpa inbound/outbound

- Adjustment yang valid.
- Mutation hanya mengubah lokasi, bukan total quantity.

## Inventory detail

Jika batch/tanggal digunakan:

```text
Item
 └── Batch
      ├── tanggal masuk
      ├── quantity
      └── lokasi
```

## Satu item dan satu lokasi

Jika operasional client menetapkan satu jenis bahan pada satu rak, sistem harus mendukung aturan tersebut.

Namun automatic assignment rak tidak boleh dibuat kecuali disepakati.
