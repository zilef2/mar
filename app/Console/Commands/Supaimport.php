<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Supaimport extends Command
{
    protected $signature = 'supaimport {model : Nombre del modelo}';

    protected $description = 'Genera una clase Import para Maatwebsite Excel';

    public function handle(): void
    {
        $model = $this->argument('model');

        $this->info('Pega los fields (array PHP simple) y presiona Enter dos veces:');
        $this->info("Ejemplo: ['reference', 'description', 'value']");

        $input = '';
        while (true) {
            $line = $this->ask('');
            if (empty($line)) {
                break;
            }
            $input .= $line;
        }

        preg_match_all("/'([^']+)'/", $input, $matches);
        $fields = $matches[1];

        if (empty($fields)) {
            $this->error('No se detectaron fields. Verifica el formato.');

            return;
        }

        $this->info('Fields detectados: '.implode(', ', $fields));
        $fields = array_values(array_filter($matches[1], fn($f) => $f !== 'id'));
        $this->generateImport($model, $fields);
    }

    private function generateImport(string $model, array $fields): void
    {
        $className = $model.'Import';
        $path = app_path("Imports/{$className}.php");

        if (! is_dir(app_path('Imports'))) {
            mkdir(app_path('Imports'), 0755, true);
        }

        if (file_exists($path)) {
            if (! $this->confirm("Ya existe {$className}.php. ¿Sobreescribir?")) {
                $this->warn('Operación cancelada.');

                return;
            }
        }

        $content = $this->buildContent($model, $fields);
        file_put_contents($path, $content);

        $this->info("Archivo generado: app/Imports/{$className}.php");
    }

    private function buildContent(string $model, array $fields): string
    {
        $className = $model.'Import';
        $modelUse = "App\\Models\\{$model}";

        $excelComment = $this->buildExcelComment($fields);
        $modelAssign = $this->buildModelAssignment($model, $fields);

        return <<<PHP
<?php

namespace App\Imports;

use {$modelUse};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;

class {$className} implements ToModel
{
    public int \$CountFilas   = 0;
    public int \$contar1      = 0;
    public int \$contar2      = 0;
    public int \$contar3      = 0;
    public int \$contarVacios = 0;
    public array \$larow;

{$excelComment}

    public function model(array \$row)
    {
        try {
            \$this->larow = \$row;

            if (\$row[0] === '{$fields[0]}') return null;

            if (!\$this->Requeridos(\$row)) {
                \$this->contarVacios++;
                Log::info('vacio::' . implode(',', \$row));
                return null;
            }

            \$this->CountFilas++;

{$modelAssign}

            return \$instance;

        } catch (\Throwable \$th) {
            Log::error(\$th->getMessage() . ' L:' . \$th->getLine() . ' Ubi:' . \$th->getFile());
        }
    }

    public function Requeridos(array \$theRow): bool
    {
        if (empty(\$theRow[0])) return false;

        return true;
        //if (!is_string($theRow[0])) {
		//	return false;
		//}
		//if (!is_int(intval($theRow[1]))) {
		//	return false;
		//}
    }
}
PHP;
    }

    private function buildExcelComment(array $fields): string
    {
        $lines = ['    /*  Columnas del excel'];
        foreach ($fields as $index => $field) {
            $lines[] = "        {$index} => \"{$field}\"";
        }
        $lines[] = '    */';

        return implode("\n", $lines);
    }

    private function buildModelAssignment(string $model, array $fields): string
    {
        $lines = ["            \$instance = new {$model}(["];
        foreach ($fields as $index => $field) {
            $lines[] = "                '{$field}' => \$row[{$index}],";
        }
        $lines[] = '            ]);';

        return implode("\n", $lines);
    }
}
