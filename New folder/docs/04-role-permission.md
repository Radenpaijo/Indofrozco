# 04 Role & Permission

Role dasar yang dapat digunakan sebagai rancangan awal:

| Role | Fungsi |
|---|---|
| Admin | Pengelolaan sistem dan seluruh data |
| Purchasing | PR, PO, supplier |
| Warehouse | Receiving, inventory, picking, outbound, opname |
| Produksi | Material request |
| Manager | Monitoring dan approval sesuai kebijakan client |

> Role final dan detail permission harus dikonfirmasi client.

## Prinsip

- User hanya melihat menu yang diizinkan.
- Permission dicek di backend, bukan hanya disembunyikan di UI.
- Transaksi penting menyimpan user pelaksana.
