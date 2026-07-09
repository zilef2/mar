<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePermissionSeeder extends Command
{
    protected $signature = 'make:permission-seeder {modelo}';
    protected $description = 'Genera un seeder de permisos para un modelo dado';

    public function handle(): void
    {
        $modelo = $this->argument('modelo');
        $className = Str::studly($modelo) . 'Seeder';
        $path = database_path("seeders/{$className}.php");

        $stub = <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class {$className} extends Seeder
{
    public function run(): void
    {
        \$superadmin = Role::where(['name' => 'superadmin'])->first();
        \$admin = Role::where(['name' => 'admin'])->first();
        \$administrativo = Role::where(['name' => 'administrativo'])->first();

        \$vectorCRUD = ['create', 'update', 'read', 'update2', 'delete'];
        \$adminCRUD = ['create', 'update', 'read', 'update2'];
        \$vectorModelo = ['{$modelo}'];

        foreach (\$vectorCRUD as \$value) {
            foreach (\$vectorModelo as \$model) {
                \$superadmin->givePermissionTo([\$value.' '.\$model]);
            }
        }

        foreach (\$adminCRUD as \$value) {
            foreach (\$vectorModelo as \$model) {
                \$admin->givePermissionTo([\$value.' '.\$model]);
                \$administrativo->givePermissionTo([\$value.' '.\$model]);
            }
        }
    }
}
PHP;

        file_put_contents($path, $stub);
        $this->info("Seeder generado: database/seeders/{$className}.php");
    }
}