# AI PROJECT CONTEXT

## Project

INDOFROZ WMS — Warehouse & Inventory Management System.

## Objective

Build a maintainable Laravel/MySQL warehouse and inventory application based on documented client business processes.

## Source of Truth

The `/docs` directory is the primary functional specification.

Before coding:
1. Read the relevant documentation.
2. Identify defined rules.
3. Identify TBD items.
4. Do not invent missing business rules.

## Core Flow

PR → PO → Receiving → Put Away → Inventory → Material Request → Picking → Outbound → Reporting.

## Critical Rules

- Actual receiving quantity must be recorded.
- Requesting material does not reduce stock.
- Outbound posting reduces stock.
- Mutation changes location, not total stock.
- Adjustment requires a reason and audit trail.
- FIFO must follow the documented requirement.
- Automatic rack assignment is not allowed unless explicitly required.

## Architecture

Use Laravel conventions and keep business logic maintainable. Prefer service/domain logic for complex inventory operations instead of putting all rules inside controllers.

## Safety

Never silently alter existing data or schema. Explain migration-impacting changes before implementing them.
