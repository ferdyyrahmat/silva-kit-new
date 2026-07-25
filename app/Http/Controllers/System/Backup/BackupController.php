<?php

namespace App\Http\Controllers\System\Backup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
            $filePath = $this->createNativeBackup($type);
            $fileName = basename($filePath);

            audit_log("Created system backup ({$type}): {$fileName}", 'create', 'backup');
            send_notification('Backup Created', "New {$type} backup archive {$fileName} generated successfully.");

            return response()->json([
                'success'  => true,
                'message'  => "System backup archive {$fileName} created successfully!",
                'redirect' => route('admin.backups.index')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success'  => false,
                'message'  => 'Backup creation failed: ' . $e->getMessage(),
                'redirect' => route('admin.backups.index')
            ], 500);
        }
    }

    public function download(Request $request)
    {
        $request->validate(['file' => 'required|string']);
        $fileName = basename($request->file);

        $backupFiles = $this->getBackupFileList();
        foreach ($backupFiles as $bf) {
            if ($bf['name'] === $fileName && file_exists($bf['path'])) {
                audit_log("Downloaded backup archive: {$fileName}", 'download', 'backup');
                return response()->download($bf['path']);
            }
        }

        abort(404, 'Backup file not found.');
    }

    public function destroy(Request $request, string $file)
    {
        $fileName = basename($file);
        $backupFiles = $this->getBackupFileList();

        $deleted = false;
        foreach ($backupFiles as $bf) {
            if ($bf['name'] === $fileName && file_exists($bf['path'])) {
                @unlink($bf['path']);
                $deleted = true;
                break;
            }
        }

        if ($deleted) {
            audit_log("Deleted backup archive: {$fileName}", 'delete', 'backup');
            return response()->json([
                'success'  => true,
                'message'  => "Backup file {$fileName} deleted successfully.",
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
        $backupFiles = [];
        $searchDirs = [
            storage_path('app'),
            storage_path('app/private'),
            storage_path('app/public'),
            storage_path('app/Silva-Kit'),
            storage_path('app/private/Silva-Kit'),
        ];

        foreach ($searchDirs as $dir) {
            if (!file_exists($dir)) continue;

            try {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && str_ends_with(strtolower($file->getFilename()), '.zip')) {
                        $fileName = $file->getFilename();
                        $filePath = $file->getPathname();

                        // Avoid duplicate entries
                        $exists = false;
                        foreach ($backupFiles as $bf) {
                            if ($bf['name'] === $fileName) {
                                $exists = true;
                                break;
                            }
                        }

                        if (!$exists) {
                            $backupFiles[] = [
                                'path'          => $filePath,
                                'name'          => $fileName,
                                'size_mb'       => round($file->getSize() / 1024 / 1024, 2),
                                'last_modified' => date('Y-m-d H:i:s', $file->getMTime()),
                            ];
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore unreadable dirs
            }
        }

        usort($backupFiles, fn($a, $b) => strcmp($b['last_modified'], $a['last_modified']));
        return $backupFiles;
    }

    private function createNativeBackup(string $type = 'db'): string
    {
        $tables = DB::select('SHOW TABLES');
        $sql = "-- Silva Kit Database Dump\n-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $val = array_values((array) $table);
            if (empty($val[0])) continue;
            $tableName = $val[0];

            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createTableVal = (array) $createTable[0];
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= array_values($createTableVal)[1] . ";\n\n";

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
        $fileName = "silva-kit-backup-{$type}-{$dateStr}.zip";
        
        $dirPath = storage_path('app/private/Silva-Kit');
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        $zipPath = $dirPath . '/' . $fileName;
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFromString("database-dump-{$dateStr}.sql", $sql);
            $zip->close();
        }

        return $zipPath;
    }
}
