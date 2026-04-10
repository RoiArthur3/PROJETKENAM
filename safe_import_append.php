<?php

/**
 * Import SQL in append-only mode (non destructive):
 * - Keeps existing tables/data
 * - Skips DROP TABLE
 * - Converts CREATE TABLE to CREATE TABLE IF NOT EXISTS
 * - Converts INSERT INTO to INSERT IGNORE INTO
 *
 * Usage:
 *   php safe_import_append.php path/to/dump.sql
 */

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if ($argc < 2) {
    fwrite(STDERR, "Usage: php safe_import_append.php <sql_file>\n");
    exit(1);
}

$sourceFile = $argv[1];
if (!is_file($sourceFile)) {
    fwrite(STDERR, "SQL file not found: {$sourceFile}\n");
    exit(1);
}

$sql = file_get_contents($sourceFile);
if ($sql === false) {
    fwrite(STDERR, "Unable to read SQL file.\n");
    exit(1);
}

// Non-destructive transformations
$sql = preg_replace('/^\s*DROP\s+TABLE\s+IF\s+EXISTS\b.*?;\s*$/mi', '-- DROP TABLE removed for safe append import;', $sql);
$sql = preg_replace('/\bCREATE\s+TABLE\b/i', 'CREATE TABLE IF NOT EXISTS', $sql);
$sql = preg_replace('/\bINSERT\s+INTO\b/i', 'INSERT IGNORE INTO', $sql);

// Optional: avoid changing DB context from dump
$sql = preg_replace('/^\s*CREATE\s+DATABASE\b.*?;\s*$/mi', '-- CREATE DATABASE skipped by safe import', $sql);
$sql = preg_replace('/^\s*USE\s+`?.+?`?\s*;\s*$/mi', '-- USE skipped by safe import', $sql);

$tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'safe_append_' . uniqid('', true) . '.sql';
if (file_put_contents($tmpFile, $sql) === false) {
    fwrite(STDERR, "Unable to write temporary SQL file.\n");
    exit(1);
}

$connection = config('database.default');
$db = config("database.connections.{$connection}");

$host = $db['host'] ?? '127.0.0.1';
$port = (string) ($db['port'] ?? 3306);
$database = $db['database'] ?? '';
$username = $db['username'] ?? '';
$password = $db['password'] ?? '';

if ($database === '' || $username === '') {
    @unlink($tmpFile);
    fwrite(STDERR, "Database config missing (database/username).\n");
    exit(1);
}

$mysqlCmd = sprintf(
    'mysql --default-character-set=utf8mb4 --force -h %s -P %s -u %s %s %s < %s',
    escapeshellarg($host),
    escapeshellarg($port),
    escapeshellarg($username),
    $password !== '' ? '-p' . escapeshellarg($password) : '',
    escapeshellarg($database),
    escapeshellarg($tmpFile)
);

fwrite(STDOUT, "Import append-only in progress...\n");
passthru($mysqlCmd, $exitCode);

@unlink($tmpFile);

if ($exitCode === 0) {
    fwrite(STDOUT, "Done. Existing data preserved, new rows added when possible.\n");
    exit(0);
}

fwrite(STDERR, "Import completed with SQL warnings/errors (mysql exit code: {$exitCode}).\n");
fwrite(STDERR, "Tip: duplicates are skipped due to INSERT IGNORE.\n");
exit($exitCode);
