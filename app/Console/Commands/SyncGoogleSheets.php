<?php

namespace App\Console\Commands;

use App\Helpers\GoogleSheetHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncGoogleSheets extends Command
{
    protected $signature   = 'sheets:sync {--force : Force full sync even if recently synced}';
    protected $description = 'Sync entire DB to Google Sheets (auto-runs every 5 minutes)';

    public function handle(): int
    {
        $this->info('[sheets:sync] Starting Google Sheet sync...');

        try {
            $results = GoogleSheetHelper::syncFullDB();

            $total   = collect($results)->filter(fn($v) => is_int($v))->sum();
            $skipped = collect($results)->filter(fn($v) => $v === 'skipped')->count();

            $this->info("[sheets:sync] Done — {$total} records synced across " . count($results) . " sheets ({$skipped} skipped).");
            Log::info("[SyncGoogleSheets] Synced {$total} records.");

        } catch (\Throwable $e) {
            $this->error('[sheets:sync] Failed: ' . $e->getMessage());
            Log::error('[SyncGoogleSheets] Error: ' . $e->getMessage());
        }

        return self::SUCCESS;
    }
}
