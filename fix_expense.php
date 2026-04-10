<?php
$file = 'app/Models/Expense.php';
$content = file_get_contents($file);

// First, find and remove the incorrectly placed property
$pattern = '/class Expense extends Model\s*\{\s*use SoftDeletes;\s*public function getTable\(\)\s*\{\s*\$defaultTable = parent::getTable\(\);[\s\n]*protected \$table = [\'"]depenses[\'"]]?;/s';
$replacement = 'class Expense extends Model
{
    use SoftDeletes;

    protected $table = \'depenses\';

    public function getTable()
    {
        if (Schema::hasTable(\'expenses\')) {
            return \'expenses\';
        }
        return \'depenses\';
    }';

$content = preg_replace($pattern, $replacement, $content);

// Now remove the old getTable method if it still exists
$content = preg_replace(
    '/public function getTable\(\)\s*\{\s*if \(Schema::hasTable\(\$defaultTable\)\).*?return \$defaultTable;\s*\}/s',
    '',
    $content
);

file_put_contents($file, $content);
echo "Fixed!\n";
