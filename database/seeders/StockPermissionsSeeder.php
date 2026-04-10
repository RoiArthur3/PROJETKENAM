<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Ce seeder est maintenant simplifié - les permissions sont gérées via les rôles
        // dans AclSeeder.php

        $this->command?->info('StockPermissionsSeeder : permissions simplifiées.');
    }
}
