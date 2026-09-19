# 17 Audit Log

## Tujuan

Menyediakan histori perubahan data penting.

## Minimal

- user
- action
- module
- record
- old value (jika relevan)
- new value (jika relevan)
- timestamp
- IP/device information hanya jika diperlukan dan sesuai kebijakan

## Event penting

- Login
- Create
- Update
- Delete/void
- Approval
- Receiving posting
- Outbound posting
- Adjustment posting
- Mutation posting

Audit log sebaiknya immutable bagi user biasa.
