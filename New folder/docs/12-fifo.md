# 12 FIFO

## Definisi

FIFO = First In First Out.

Barang yang masuk lebih dahulu diprioritaskan untuk dikeluarkan lebih dahulu.

## Dasar FIFO

Jika batch/tanggal digunakan, urutan dapat berdasarkan tanggal masuk/tanggal yang ditetapkan dalam requirement.

Contoh:

```text
Batch A — 01 Jan — 500 KG
Batch B — 10 Jan — 700 KG
```

Request:

```text
600 KG
```

Maka:

```text
Batch A → 500 KG
Batch B → 100 KG
```

## Catatan penting

FIFO harus dikonfirmasi apakah benar-benar wajib otomatis oleh sistem atau hanya SOP warehouse.

Jika client menginginkan warehouse memilih batch secara manual, sistem harus tetap memungkinkan pemilihan manual sambil menyediakan informasi umur/tanggal stok.



untuk fifo ini kita buat otomatis atau rekomendasi saja biar warehouse bisa pilih manual soalnya nntiw ada kasus untuk hal hal khusus yang mengharuskan mengunakan batch tertentu untuk datanya ada dimana raknya tanggal masuk nya dan tanggal kadaluarsa nya dan jumlah stok tiap tiap batch nya ada di tabel mana