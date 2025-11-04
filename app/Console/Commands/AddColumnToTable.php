<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddColumnToTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan add:column
     *
     * @var string
     */
    protected $signature = 'add:column';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea una migración para agregar una columna a una tabla existente, con opciones interactivas y un dato divertido.';

    /**
     * Column types suggestions.
     */
    protected $types = [
        'string' => 'Cadena (string)',
        'integer' => 'Entero',
        'bigInteger' => 'Entero grande',
        'decimal' => 'Decimal',
        'text' => 'Texto',
        'boolean' => 'Booleano',
        'date' => 'Fecha',
        'dateTime' => 'Fecha y hora',
        'json' => 'JSON',
        'float' => 'Flotante',
        'enum' => 'Enum',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('¡Bienvenido al creador de columnas mágicas! 🪄✨');
        $database = 'demcocomercial';

        // Obtener las tablas de la base de datos proporcionada
        $tables = DB::select("SHOW TABLES FROM `$database`");

        if (empty($tables)) {
            $this->error("No se encontraron tablas en la base de datos '$database'.");
            return;
        }

        // Obtener el nombre de las tablas de manera universal
        $tableNames = array_map(fn($t) => array_values((array)$t)[0], $tables);

        // Preguntar por la tabla
        $table = $this->anticipate('¿A cuál tabla quieres agregar una columna?', $tableNames);

        // Verificar si existe la tabla
        if (!in_array($table, $tableNames)) {
            $this->error("La tabla '$table' no existe en la base de datos '$database'. ¡Intenta de nuevo!");
            return;
        }

        // Preguntar por el nombre de la columna
        $column = $this->ask('¿Cuál es el nombre de la nueva columna?');

        // Preguntar por el tipo de columna
        $type = $this->choice(
            '¿Qué tipo de columna quieres agregar?',
            array_map(fn($k, $v) => "$k ($v)", array_keys($this->types), $this->types),
            0
        );
        $typeKey = explode(' ', $type)[0];

        // Extra: Para enum, preguntar por los valores
        $enumValues = null;
        if ($typeKey === 'enum') {
            $values = $this->ask('Introduce los valores permitidos para el enum, separados por coma (ejemplo: activo,inactivo,pending)');
            $enumValues = array_map('trim', explode(',', $values));
        }

        // Confirma los datos
        $this->info("Vas a agregar la columna '$column' de tipo '$typeKey' a la tabla '$table'.");
        if ($typeKey === 'enum') {
            $this->info("Valores del enum: " . implode(', ', $enumValues));
        }

        // Generar el nombre de la migración
        $migrationName = "add_{$column}_to_{$table}_table";
        $migrationFile = "database/migrations/" . date('Y_m_d_His') . "_{$migrationName}.php";

        // Generar la migración
        $stub = $this->getMigrationStub($table, $column, $typeKey, $enumValues);

        file_put_contents(base_path($migrationFile), $stub);

        $this->info("¡Listo! Se ha creado la migración '$migrationName'. 🎉");
        $this->line("Archivo: $migrationFile");
        $this->comment("Ahora tu base de datos es mas 🦥💨");
    }

    /**
     * Get migration stub content.
     */
    private function getMigrationStub($table, $column, $type, $enumValues)
    {
        $enumCode = '';
        if ($type === 'enum' && $enumValues) {
            $enumCode = "->enum('$column', ['" . implode("', '", $enumValues) . "'])";
        } else {
            $enumCode = "->$type('$column')";
        }

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('$table', function (Blueprint \$table) {
            \$table$enumCode;
        });
    }

    public function down()
    {
        Schema::table('$table', function (Blueprint \$table) {
            \$table->dropColumn('$column');
        });
    }
};
PHP;
    }
}