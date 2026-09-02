<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ArchiveLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive the log files into date-based folders and delete logs older than one month';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logPath = storage_path('logs');
        $archivePath = storage_path('logs/archive');

        // Ensure the archive directory exists
        if (!File::exists($archivePath)) {
            File::makeDirectory($archivePath, 0755, true);
        }

        // Get all log files
        $files = File::glob($logPath . '/*.log');

        // Archive logs into date-based folders
        foreach ($files as $file) {
            $fileName = basename($file);
            $today = Carbon::now()->format('Y-m-d');
            $dateFolder = $archivePath . '/' . $today;

            // Create a folder for today's date if it doesn't exist
            if (!File::exists($dateFolder)) {
                File::makeDirectory($dateFolder, 0755, true);
            }

            // Move the log file into the date folder
            File::move($file, $dateFolder . '/' . $fileName);
        }

        $this->info('Logs have been archived into date-based folders.');

        // Delete logs older than one month
//        $this->deleteOldLogs($archivePath);

        return 0;
    }

    /**
     * Delete logs older than one month.
     *
     * @param string $archivePath
     */
    protected function deleteOldLogs($archivePath): void
    {
        $folders = File::directories($archivePath);

        foreach ($folders as $folder) {
            $folderName = basename($folder);
            $folderDate = Carbon::createFromFormat('Y-m-d', $folderName);

            // If the folder is older than one month, delete it
            if ($folderDate->lt(Carbon::now()->subMonth())) {
                File::deleteDirectory($folder);
                $this->info("Deleted old logs from: $folderName");
            }
        }
    }
}