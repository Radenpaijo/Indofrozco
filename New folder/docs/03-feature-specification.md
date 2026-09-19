# 03 Feature Specification

## Dashboard

Menampilkan ringkasan:

- Total item/barang
- Total stok
- Barang masuk
- Barang keluar
- Transaksi terbaru
- Informasi stok minimum jika requirement disetujui

Dashboard tidak boleh membuat keputusan operasional otomatis.

## Master Barang

Field minimum:

- code
- name
- category_id
- unit_id
- minimum_stock (TBD)
- status
- description

## Supplier

- code
- name
- address
- phone
- email
- contact_person
- status

## Gudang

- code
- name
- type
- status
- description

## Lokasi/Rak

- warehouse_id
- code
- name
- capacity (TBD)
- status

## PR

- nomor
- tanggal
- requester
- detail barang
- quantity
- status
- catatan

## PO

- nomor
- tanggal
- supplier
- referensi PR
- detail barang
- quantity
- harga (TBD sesuai kebutuhan)
- status

## Receiving

- nomor receiving
- PO
- tanggal
- barang
- quantity dipesan
- quantity diterima
- batch/tanggal
- kondisi
- hasil pemeriksaan
- catatan

## Inventory

Stok harus dapat ditelusuri minimal berdasarkan:

- barang
- quantity
- unit
- gudang
- lokasi
- batch/tanggal bila digunakan

## Material Request

- nomor
- tanggal
- requester
- tujuan
- detail barang
- quantity diminta
- status

## Picking

Mencatat:

- material request
- barang
- batch
- lokasi
- quantity diminta
- quantity aktual
- user
- waktu

## Outbound

Mencatat barang yang benar-benar keluar dan mengurangi inventory.

