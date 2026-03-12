# Billing Application Documentation

This document describes the web routes and Artisan commands available in the billing application.

---

## Web Routes

### Public Routes

| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `/` | Welcome page |
| `GET` | `/hello` | Development/test route — generates billing for SimcardB ID 1 |
| `GET` | `/home` | Home page (named route: `home`) |

### Authentication

- Laravel's default `Auth::routes()` are registered for login, registration, password reset, etc.

### Protected Routes (require authentication)

All routes below require the `auth` middleware.

| Method | URI | Controller | Action | Description |
|--------|-----|------------|--------|-------------|
| `GET` | `admin/waiting-billings` | `WaitingBillingController` | `index` | List waiting billings with optional filters (status, simcard_type, month) |
| `GET` | `admin/generate-billing` | `BillingController` | `showGenerateBilling` | Show the billing generation form |
| `POST` | `admin/generate-billing` | `BillingController` | `generateBilling` | Generate billing for specified simcards (named route: `generate-billing`) |
| `GET` | `admin/operation` | `BillingController` | `showOperation` | Show operation page with simcard status types (named route: `show-operation`) |

### Generate Billing (POST) — Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `simcard_type` | string | Yes | One of: `simcard`, `datasim`, `rakuten`, `rakuten_call`, `simcard_b` |
| `simcard_ids` | string | Yes | Comma-separated list of simcard IDs (max 10) |
| `date` | date | Yes | Billing date (Y-m-d format) |

### Waiting Billings Index — Query Parameters

| Parameter | Description |
|-----------|-------------|
| `status` | Filter by waiting status |
| `simcard_type` | Filter by simcard type key |
| `month` | Filter by month (format: `YYYY-MM`) |

---

## Artisan Commands

### `generate-single-card`

Generate billing for a single simcard.

```bash
php artisan generate-single-card {type} {id} [date]
```

| Argument | Required | Description |
|----------|----------|-------------|
| `type` | Yes | Simcard type: `simcard`, `datasim`, `rakuten`, `rakuten_call`, `simcard_b` |
| `id` | Yes | Simcard ID |
| `date` | No | Billing date (defaults to current date) |

**Example:**
```bash
php artisan generate-single-card simcard_b 123 2025-03-01
```

---

### `generate-billing`

Process waiting billings from the queue. Fetches records from `waiting_billing_generate_sims` with status `waiting` and generates billings.

```bash
php artisan generate-billing {type} [count]
```

| Argument | Required | Description |
|----------|----------|-------------|
| `type` | Yes | Simcard type: `simcard`, `datasim`, `rakuten`, `rakuten_call`, `simcard_b` |
| `count` | No | Number of records to process (default: from `config('billings.records_per_generate')`, typically 80) |

**Behavior:**
- Joins simcard table with waiting table
- Processes records with `status = 'waiting'`
- Updates waiting record to `done` on success, `error` on failure
- Links billing to `RakutenPurchaseLog` or `requests` when applicable

**Example:**
```bash
php artisan generate-billing datasim 50
```

---

### `app:add-waiting`

Add simcards to the waiting billing queue for batch processing. Populates `waiting_billing_generate_sims` based on simcard type and eligibility rules.

```bash
php artisan app:add-waiting {type}
```

| Argument | Required | Description |
|----------|----------|-------------|
| `type` | Yes | One of: `simcard`, `datasim`, `rakuten`, `rakuten_call`, `simcard_b` |

**Type-specific logic:**

| Type | Criteria |
|------|----------|
| `simcard` | Active simcards (activation_date 2022-09-01 to last month end); merchant 20 and non-20; deactivated simcards (merchant 2, deactivation in last month) |
| `datasim` | Simcards from `DataSim::generateable()` scope |
| `rakuten` | `RakutenDataSim` with status `active` |
| `rakuten_call` | `RakutenCallSim` active (activation before current month) or deactivated in previous month |
| `simcard_b` | `SimcardB` with type `call`, active (activation before current month) or deactivated in previous month |

**Example:**
```bash
php artisan app:add-waiting simcard_b
```

---

### `app:custom`

Placeholder command with no implementation.

```bash
php artisan app:custom
```

---

## Simcard Types (config/billings.php)

| Key | Model |
|-----|-------|
| `simcard` | `App\Models\mobile\Simcard` |
| `datasim` | `App\Models\mobile\DataSim` |
| `rakuten` | `App\Models\RakutenDataSim` |
| `rakuten_call` | `App\Models\RakutenCallSim` |
| `simcard_b` | `App\Models\mobile\SimcardB` |

---

## Operation Page Status Types

The operation page (`admin/operation`) displays these status options by type:

- **simcard**: OtaWait, MnpWait, unactive, active, pfd, deactivate
- **datasim**: otaWait, active, instock, deactivate
- **rakuten**: otaWait, activeWait, active, stop, deactivate, deWait, instock
- **rakuten_call**: OtaWait, MnpWait, unactive, active, pfd, deactivate, processing
- **simcard_b**: OtaWait, MnpWait, unactive, active, pfd, deactivate, processing, inactive
