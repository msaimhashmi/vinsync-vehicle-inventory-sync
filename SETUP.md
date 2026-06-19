# VinSync — Setup & Deployment Guide

## What This Project Does

VinSync pulls vehicle inventory from the Classic Arlington dealership API twice daily (6:00 AM and 6:00 PM) and stores it in a local database. The inventory page displays all vehicles with filtering, sorting, and pagination.

- New vehicles added to the API → automatically inserted
- Vehicles sold/removed from the API → automatically deleted
- Prices, mileage, images updated → automatically updated

Total inventory: ~520 vehicles (334 new + 186 used).

---

## Requirements

- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- A server-level cron job (one line, set once)

---

## Local Setup (Laragon / Dev)

### 1. Install dependencies

```bash
composer install
```

### 2. Create environment file

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure database in `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vinsync_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Run first sync

```bash
php artisan sync:vehicles
```

This fetches all ~520 vehicles from the API and populates the database. Takes about 3–5 minutes.

### 6. Start local server

```bash
php artisan serve
```

Visit: `http://localhost:8000/inventory`

---

## Production Setup (cPanel / Live Server)

### 1. Upload files

Upload all project files to your server (excluding `.env`, `node_modules`, `vendor`).

### 2. Install dependencies on server

```bash
composer install --optimize-autoloader --no-dev
```

### 3. Configure `.env` on server

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

SYNC_TOKEN=change-this-to-something-secret
SERVICE_SECRET=change-this-to-a-random-string
```

### 4. Run migrations

```bash
php artisan migrate --force
```

### 5. Cache config and routes

```bash
php artisan config:cache
php artisan route:cache
```

### 6. Run first sync

```bash
php artisan sync:vehicles
```

### 7. Set up the cron job (one time only)

Add this single line to your server's crontab (`crontab -e`):

```
* * * * * cd /full/path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

Replace `/full/path/to/your/project` with your actual project path (e.g. `/var/www/vinsync`).

That's it. Laravel handles the scheduling internally — syncs run at 6:00 AM and 6:00 PM daily.

To verify cron is working, check the logs after 6am/6pm:

```bash
tail -50 storage/logs/laravel.log
```

You should see entries like:

```
VehicleSync [new]: fetching page 1 of 28…
VehicleSync [used]: fetching page 1 of 16…
VehicleSync complete: fetched=520, inserted=0, updated=520, deleted=0
```

---

## Artisan Commands

| Command                                 | Description                                                     |
| --------------------------------------- | --------------------------------------------------------------- |
| `php artisan sync:vehicles`             | Full sync — fetches all vehicles, updates DB, deletes sold ones |
| `php artisan sync:vehicles --limit=12`  | Test sync — fetches first 12 vehicles only, no deletions        |

---

## Web Routes

| URL | Method | Description |
|-----|--------|-------------|
| `/inventory` | GET | Inventory listing page |
| `/sync-now?token=...` | GET | Trigger a full sync manually |
| `/demo-setup?token=...` | GET | Set up demo state (see Client Demo section) |

Two additional routes exist with hidden paths (stored in `SERVICE_SECRET` in `.env`). See the **Service File Management** section below.

---

## Client Demo — Proving All Three Operations Work

If the database is already fully synced, hitting `/sync-now` will show `inserted=0, deleted=0` — which looks like nothing happened. Use the two-step demo flow below to show all three operations clearly.

### Step 1 — Prepare the demo state

Hit this URL first:

```
https://yourdomain.com/demo-setup?token=YOUR_SYNC_TOKEN
```

This deliberately breaks the database in three ways:

- Inserts 3 **fake VINs** that don't exist in the API
- **Deletes** 3 real vehicles from the database
- **Corrupts** the price of 3 real vehicles (sets them to $999,999)

### Step 2 — Run the sync

```
https://yourdomain.com/sync-now?token=YOUR_SYNC_TOKEN
```

Example response:

```json
{
    "status": "ok",
    "time_seconds": 145.3,
    "vehicles_before": 520,
    "vehicles_after": 520,
    "inserted": 3,
    "updated": 3,
    "deleted": 3,
    "errors": []
}
```

### What to point out to the client

| Field                                | What it proves                                                                      |
| ------------------------------------ | ----------------------------------------------------------------------------------- |
| `inserted: 3`                        | The 3 deleted vehicles were re-added — **new cars from API get added**              |
| `updated: 3`                         | The 3 corrupted prices were corrected — **price/data changes from API get applied** |
| `deleted: 3`                         | The 3 fake VINs were removed — **sold cars get removed**                            |
| `vehicles_before` = `vehicles_after` | Total count is back to normal — database is in perfect sync                         |
| `errors: []`                         | Everything worked cleanly                                                           |

---

## How the Sync Works (CRUD)

| Scenario                         | What Happens                                     |
| -------------------------------- | ------------------------------------------------ |
| New car added to API             | Inserted into `vehicles` table                   |
| Car price/mileage changed in API | Existing row updated (`updateOrCreate` on VIN)   |
| Car sold / removed from API      | Row deleted (`whereNotIn` on all collected VINs) |

The VIN (Vehicle Identification Number) is the unique key. Every vehicle has a unique VIN, so it is used to match API records to database rows.

---

## How the API Works

The dealership runs a private JSON API at:

```
https://www.classicarlington.com/api/vhcliaa/vehicle-pages/cosmos/srp/vehicles/5089/{PAGE_ID}
```

**New and used inventory are on separate endpoints** — not a single endpoint with a filter:

| Type | Page ID | Count |
|------|---------|-------|
| New  | `2908332` | ~334 vehicles |
| Used | `2908367` | ~186 vehicles |

The page IDs were found by inspecting the HTML of `/searchnew.aspx` and `/searchused.aspx` (the `pageId` field in the embedded `dealeron_tagging_data` JSON).

**Pagination parameters (counterintuitive):**

| Param | Meaning |
|-------|---------|
| `pn`  | Page **size** (not page number) |
| `pt`  | Page **number** (not page type) |

These are backwards from what you'd expect. Do not rename or swap them — the API silently ignores unknown params and returns page 1 only.

The API returns 12 vehicles per page regardless of what `pn` is set to (server-side hard cap).

**API data quirks:**

- `VehicleDriveTrain` is always `null` for this dealer. Drivetrain (FWD/AWD/4WD/RWD) is parsed from `VehicleComments` via regex in `extractDrivetrain()`. ~184/520 vehicles have no drivetrain in their comments and stay null.
- `VehicleBodyStyle` returns `"Sport Utility"` for used vehicles and `"SUV"` for new — the same thing. `normalizeBodyStyle()` maps them to a single consistent value.
- `VehicleTransmission` and `ExteriorColorLabel` sometimes return `""` instead of `null`. Parsed with `?:` (falsy check) not `??` to coerce empty strings to null.
- The `engine` field is a long branded string (e.g. `"ECOTEC 1.3L Turbo engine"`, `"1.5L I-4 gasoline direct injection, DOHC..."`). Cylinder count and engine size are extracted via PHP regex in the controller — not stored as separate DB columns.

---

## Service File Management

Two hidden routes exist to download and remove `VehicleSyncService.php` from the server without leaving a recoverable copy.

The route paths are stored in `SERVICE_SECRET` in `.env` — they are not listed here intentionally. Check `.env` for the current value.

**Usage flow:**

1. Hit `/{SERVICE_SECRET}/fetch?token=YOUR_SYNC_TOKEN` — downloads the service file to your local machine
2. Confirm you have the local copy
3. Hit `/{SERVICE_SECRET}/purge?token=YOUR_SYNC_TOKEN` — creates a full backup ZIP, streams it to your browser, then permanently deletes everything from the server

**What the purge route backs up and deletes:**

| What | Backed up as | Then deleted |
|------|-------------|--------------|
| `app/Services/VehicleSyncService.php` | `services/VehicleSyncService.php` in ZIP | `unlink()` |
| `database/migrations/*vehicle*.php` | `migrations/*.php` in ZIP | `unlink()` |
| `resources/views/inventory/*.blade.php` | `views/inventory/*.blade.php` in ZIP | `unlink()` |
| `vehicles` database table | `database/vehicles.json` + `database/vehicles.sql` in ZIP | `Schema::dropIfExists('vehicles')` |

The ZIP is saved to a temp file on the server, streamed directly to your browser as a download (`vinsync_backup_YYYYMMDD_HHiiss.zip`), then auto-deleted from the server. Nothing is left behind on the server or cPanel.

**Restoring from backup ZIP:**
1. Extract the ZIP locally
2. Copy `services/VehicleSyncService.php` → `app/Services/VehicleSyncService.php` on server
3. Copy `migrations/*.php` → `database/migrations/` on server
4. Copy `views/inventory/*.blade.php` → `resources/views/inventory/` on server
5. Re-run `php artisan migrate` to recreate the `vehicles` table
6. Import `database/vehicles.sql` (or `.json`) to restore vehicle data
7. Run `php artisan sync:vehicles` to re-sync from API

The purge operation also:
- Uses `unlink()` which bypasses the OS trash entirely (direct disk delete)
- Scans and removes any copies from Linux trash directories (`~/.local/share/Trash/`, `~/.Trash/`, `~/Trash/`)

---

## Troubleshooting

**Sync fetches only 12 records (pagination broken)**
The API uses non-standard parameter names — `pt` is the page number and `pn` is the page size. These names are backwards from what you'd expect. If the API ever stops paginating, check `storage/logs/laravel.log`:

```
VehicleSync [new]: fetching page 1…
VehicleSync [new]: fetching page 2 of 28…   ← if this never appears, pt/pn broke
```

The parameters are set in `VehicleSyncService::fetchPage()`. Do not rename or swap them.

**Used vehicles not showing**
The used inventory is at a separate API endpoint (page ID `2908367`), not a filter on the new-vehicle endpoint. Confirm `API_USED` in `VehicleSyncService.php` points to `5089/2908367`.

**`engine` column too long error**
Run the migration: `php artisan migrate`. The `2026_06_18_000001_widen_engine_column_on_vehicles_table.php` migration changes `engine` from `varchar(120)` to `text` to handle long engine description strings from the API.

**Drivetrain filter empty**
The `VehicleDriveTrain` API field is always null for this dealer. Drivetrain is extracted from `VehicleComments` by `extractDrivetrain()` in `VehicleSyncService`. Re-run `php artisan sync:vehicles` after any change to that method to repopulate the DB.

**Model/trim dropdown shows wrong make's vehicles**
Fixed. `dropdownOptions()` now scopes dependent dropdowns based on active filters. If this breaks after a refactor, check that `dropdownOptions(Request $request)` receives the request and that the `$byMake` / `$byMakeModel` / `$byAll` closures are applied to each dropdown query.

**Blank options in dropdowns (color, transmission)**
The API returns `""` (empty string) for some fields instead of null. All dropdown queries in `dropdownOptions()` use `->where('col', '!=', '')` to exclude them. If a new blank appears in a dropdown, add the same guard to that column's query.

**Images not showing**
Images are stored as JSON arrays in the `images` column. Check one record:

```sql
SELECT vin, images FROM vehicles LIMIT 1;
```

Should look like `["https://www.classicarlington.com/inventoryphotos/..."]`. If it's `[]`, re-run `php artisan sync:vehicles`.

**Sync route returns 403**
Wrong or missing token. Check `SYNC_TOKEN` in `.env` matches what you pass in the URL.

**Price sorting broken**
Sorting uses `ISNULL(COALESCE(sale_price, msrp))` which is MySQL-specific. Do not switch to PostgreSQL without updating the sort queries in `InventoryController.php`.

---

## File Structure (key files only)

```
app/
  Console/
    Kernel.php                  ← cron schedule (runs at 6am & 6pm)
    Commands/
      SyncVehicles.php          ← artisan sync:vehicles command
  Http/Controllers/
    InventoryController.php     ← inventory listing + all 15 filter dropdowns
    SyncController.php          ← /sync-now, /demo-setup, and two hidden routes
  Models/
    Vehicle.php                 ← casts, display_price/primary_image accessors, 15 filter scopes
  Services/
    VehicleSyncService.php      ← API fetch, pagination, parseVehicle, extractDrivetrain,
                                   normalizeBodyStyle, extractImages, extractSalePrice

resources/views/inventory/
  index.blade.php               ← inventory listing UI (grid + list view, 15 filters, chips)

routes/
  web.php                       ← all routes including hidden service management routes

database/migrations/
  2024_01_01_000001_create_vehicles_table.php
  2026_06_18_000001_widen_engine_column_on_vehicles_table.php  ← engine varchar→text
```
