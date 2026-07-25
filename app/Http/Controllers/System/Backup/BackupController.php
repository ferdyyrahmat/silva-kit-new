<?php

namespace App\Http\Controllers\System\Backup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupController extends Controller
{
    public function index()
    {
        $backupFiles = $this->getBackupFileList();
        return view('admin.backups.index', compact('backupFiles'));
    }

    public function create(Request $request)
    {
        $type = $request->input('type', 'db'); // 'db' or 'full'

        try {
            // Attempt 1: Spatie Backup Package
            if ($type === 'db') {
                $exitCode = Artisan::call('backup:run', ['--only-db' => true]);
            } else {
                $exitCode = Artisan::call('backup:run');
            }

            if ($exitCode !== 0) {
                // Attempt 2: Fallback native database SQL dumper
                $this->createNativeDbBackup();
            }

            audit_log('Created system backup (' . strtoupper($type) . ')', 'create', 'backup');
            send_notification('Backup Created', 'New system backup generated successfully.');

            $msg = 'System backup created successfully!';

            return response()->json([
                'success'  => true,
                'message'  => $msg,
                'redirect' => route('admin.backups.index')
            ]);
        } catch (\Throwable $e) {
            // Fallback native dumper if Spatie fails on Windows
            try {
                $filePath = $this->createNativeDbBackup();
                audit_log('Created native SQL fallback backup: ' . basename($filePath), 'create', 'backup');
                send_notification('Backup Created', 'New database fallback backup archive generated.');

                $msg = 'Backup archive generated successfully!';

                return response()->json([
                    'success'  => true,
                    'message'  => $msg,
                    'redirect' => route('admin.backups.index')
                ]);
            } catch (\Throwable $ex) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Backup creation failed: ' . $ex->getMessage(),
                    'redirect' => route('admin.backups.index')
                ], 500);
            }
        }
    }

    public function download(Request $request)
    {
        $request->validate(['file' => 'required|string']);
        $fileName = basename($request->file);

        // Search in local storage backup folder
        $disk = Storage::disk('local');
        $possiblePaths = [
            'Silva-Kit/' . $fileName,
            'backups/' . $fileName,
            $fileName
        ];

        foreach ($possiblePaths as $path) {
            if ($disk->exists($path)) {
                audit_log("Downloaded backup archive: {$fileName}", 'download', 'backup');
                return $disk->download($path);
            }
        }

        abort(404, 'Backup file not found.');
    }

    public function destroy(Request $request, string $file)
    {
        $fileName = basename($file);
        $disk = Storage::disk('local');
        $possiblePaths = [
            'Silva-Kit/' . $fileName,
            'backups/' . $fileName,
            $fileName
        ];

        $deleted = false;
        foreach ($possiblePaths as $path) {
            if ($disk->exists($path)) {
                $disk->delete($path);
                $deleted = true;
                break;
            }
        }

        if ($deleted) {
            audit_log("Deleted backup archive: {$fileName}", 'delete', 'backup');
            return response()->json([
                'success'  => true,
                'message'  => 'Backup file deleted successfully.',
                'redirect' => route('admin.backups.index')
            ]);
        }

        return response()->json([
            'success'  => false,
            'message'  => 'File not found.',
            'redirect' => route('admin.backups.index')
        ], 404);
    }

    private function getBackupFileList(): array
    {
        $disk = Storage::disk('local');
        $backupFiles = [];

        $directories = ['Silva-Kit', 'backups', ''];
        foreach ($directories as $dir) {
            $files = $disk->files($dir);
            foreach ($files as $file) {
                if (str_ends_with($file, '.zip')) {
                    $fileName = basename($file);
                    // Avoid duplicate listings
                    $exists = false;
                    foreach ($backupFiles as $bf) {
                        if ($bf['name'] === $fileName) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $backupFiles[] = [
                            'path'          => $file,
                            'name'          => $fileName,
                            'size_mb'       => round($disk->size($file) / 1024 / 1024, 2),
                            'last_modified' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                        ];
                    }
                }
            }
        }

        usort($backupFiles, fn($a, $b) => strcmp($b['last_modified'], $a['last_modified']));
        return $backupFiles;
    }

    private function createNativeDbBackup(): string
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $key = 'Tables_in_' . $dbName;

        $sql = "-- Silva Kit Database Dump\n-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            if (!isset($table->$key)) continue;
            $tableName = $table->$key;

            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $values = array_map(function ($val) {
                    if (is_null($val)) return 'NULL';
                    return DB::getPdo()->quote($val);
                }, array_values($rowArray));

                $sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', array_keys($rowArray)) . "`) VALUES (" . implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $dateStr = date('Y-m-d-H-i-s');
        $fileName = "silva-kit-backup-{$dateStr}.zip";
        $dirPath = storage_path('app/Silva-Kit');
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        $zipPath = $dirPath . '/' . $fileName;
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFromString("db-dump-{$dateStr}.sql", $sql);
            $zip->close();
        }

        return $zipPath;
    }
}
