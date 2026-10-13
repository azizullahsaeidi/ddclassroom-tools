<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Spatie\Backup\Config\Config;

class BackupController extends Controller
{
    /**
     * Display a listing of backups.
     */
    public function index()
    {
        $backupConfig = Config::fromArray(config('backup'));
        $backupDestinations = BackupDestinationFactory::createFromArray($backupConfig);

        $backups = [];

        foreach ($backupDestinations as $backupDestination) {
            if ($backupDestination->disk() !== null) {
                $allBackups = $backupDestination->backups();

                foreach ($allBackups as $backup) {
                    $backups[] = [
                        'path' => $backup->path(),
                        'date' => $backup->date(),
                        'size' => $backup->sizeInBytes(),
                        'disk' => $backupDestination->diskName(),
                    ];
                }
            }
        }

        // Sort backups by date (newest first)
        usort($backups, function ($a, $b) {
            return $b['date']->timestamp <=> $a['date']->timestamp;
        });

        return view('backup.index', compact('backups'));
    }

    /**
     * Download a backup file.
     */
    public function download(Request $request, $disk, $path)
    {
        // Decode the path if it's URL encoded
        $path = urldecode($path);

        // Verify the file exists
        if (! Storage::disk($disk)->exists($path)) {
            abort(404, 'Backup file not found.');
        }

        // Get the file path
        $filePath = Storage::disk($disk)->path($path);

        // Get the filename from the path
        $filename = basename($path);

        return response()->download($filePath, $filename);
    }
}
