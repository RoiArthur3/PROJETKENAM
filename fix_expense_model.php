<?php

$file = 'app/Models/Expense.php';
$content = file_get_contents($file);

// Remove the misplaced $table property
$content = str_replace(
    'class Expense extends Model
{
    public function getTable()
    {
        $defaultTable = parent::getTable(); // expenses

    protected $table = \'depenses\';',
    'class Expense extends Model
{
    protected $table = \'depenses\';

    public function getTable()
    {
        // Check which table exists in database
        if (Schema::hasTable(\'expenses\')) {
            return \'expenses\';
        }
        return \'depenses\';
    }',
    $content
);

// Remove old getTable method if it still exists after replacement
$content = preg_replace(
    '/public function getTable\(\)\s*{[^}]*\$defaultTable = parent::getTable\(\);[^}]*if \(Schema::hasTable\(\$defaultTable\)\) {[^}]*return \$defaultTable;[^}]*}[^}]*if \(Schema::hasTable\(\'depenses\'\)\) {[^}]*return \'depenses\';[^}]*}[^}]*return \$defaultTable;[^}]*}/s',
    '',
    $content
);

file_put_contents($file, $content);
echo "Expense model fixed!\n";
