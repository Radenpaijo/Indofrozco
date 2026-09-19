# 20 Business Rules

## BR-001

Stok hanya berubah melalui transaksi inventory yang sah.

## BR-002

Receiving menggunakan quantity aktual yang diterima.

## BR-003

Request produksi tidak langsung mengurangi stok.

## BR-004

Picking tidak harus mengurangi stok sebelum proses issue/posting final.

## BR-005

Mutation lokasi tidak mengubah total quantity.

## BR-006

Adjustment wajib memiliki alasan.

## BR-007

Transaksi yang sudah posted tidak boleh diedit sembarangan.

## BR-008

Setiap transaksi penting harus memiliki user dan timestamp.

## BR-009

FIFO digunakan jika requirement client menetapkannya.

## BR-010

Automatic rack assignment tidak digunakan kecuali disetujui client.

## BR-011

Business rule yang belum dikonfirmasi diberi status TBD.

## BR-012

Tidak boleh menambahkan workflow otomatis hanya karena secara teknis memungkinkan.
