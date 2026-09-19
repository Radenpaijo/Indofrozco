# 18 Database Design

## Kandidat tabel utama

```text
users
roles
permissions
role_user
permission_role

categories
units
items
suppliers

warehouses
locations

purchase_requests
purchase_request_items

purchase_orders
purchase_order_items

receivings
receiving_items

inventory
inventory_batches

material_requests
material_request_items

pickings
picking_items

outbounds
outbound_items

stock_mutations
stock_mutation_items

stock_opnames
stock_opname_items

stock_adjustments
stock_adjustment_items

audit_logs
```

## Prinsip database

- Gunakan foreign key.
- Gunakan decimal untuk quantity bila satuan memungkinkan pecahan.
- Nomor dokumen unik.
- Timestamp transaksi.
- Jangan menyimpan stok sebagai satu angka tanpa sumber transaksi jika histori dibutuhkan.

## Inventory ledger

Untuk sistem yang membutuhkan audit kuat, pertimbangkan ledger transaksi:

```text
inventory_transactions
```

dengan tipe:

```text
IN
OUT
ADJUSTMENT
TRANSFER
```

Saldo dapat direkonstruksi dari transaksi.

Final schema harus dibuat setelah requirement detail dikunci.
