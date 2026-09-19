# CODING RULES

## General

- Follow Laravel conventions.
- Use clear naming.
- Avoid duplicated business logic.
- Validate all input server-side.
- Use authorization/policies for permissions.
- Use database transactions for stock-affecting operations.

## Inventory

Any operation that changes inventory must be atomic.

Example:

```text
BEGIN TRANSACTION
 → validate
 → lock/check relevant inventory
 → create transaction records
 → update balance/ledger
 → audit log
COMMIT
```

Rollback on failure.

## Controllers

Controllers should orchestrate requests, not contain large blocks of business rules.

## Validation

Use Form Request classes for non-trivial validation.

## Database

- Foreign keys.
- Appropriate indexes.
- Unique document numbers.
- Avoid destructive migrations.
- Never drop production data casually.

## UI

Keep forms readable and consistent.

## Testing

Every stock-affecting feature should have feature tests covering:

- success
- insufficient stock
- invalid status
- duplicate submission
- rollback on failure
- authorization
