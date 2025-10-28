<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class nuevo_campo_googlesheets extends Command {
    // Definimos el nombre y la firma del comando, incluyendo el parámetro
    protected $signature = 'zilef:nuevocampo {nombre_campo}';

    protected $description = 'Agrega un nuevo campo en el archivo ReadGoogleSheets.php';

    public function handle()
    {
        // Obtenemos el valor del parámetro "nombre_campo"
        $nombreCampo = $this->argument('nombre_campo');

        $filePath = app_path('Http/Controllers/ReadGoogleSheets.php');

        // Verificamos si el archivo existe
        if (!File::exists($filePath)) {
            $this->error("El archivo $filePath no existe.");
            return;
        }

        // Leemos el contenido del archivo
        $content = File::get($filePath);

        // Buscamos la línea que contiene "//aquinuevocampo"
        if (preg_match('/\/\/aquinuevocampo/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            // Buscamos el número anterior al "?? ''"
            if (preg_match('/\[(\d+)\] \?\? \'\'/', $content, $numberMatches, PREG_OFFSET_CAPTURE, $matches[0][1])) {
                $lastNumber = intval($numberMatches[1][0]);
                $newNumber = $lastNumber + 1;

                // Preparamos la nueva línea a insertar
                $newLine = "        '$nombreCampo' => \$valueCabeza[$newNumber] ?? '',\n        //aquinuevocampo";

                // Reemplazamos "//aquinuevocampo" con la nueva línea
                $newContent = str_replace('//aquinuevocampo', $newLine, $content);

                // Escribimos el nuevo contenido en el archivo
                File::put($filePath, $newContent);

                $this->info("Nuevo campo '$nombreCampo' agregado con el número $newNumber.");
            } else {
                $this->error("No se pudo encontrar el número anterior al '?? ''.");
            }
        } else {
            $this->error("No se encontró el marcador '//aquinuevocampo' en el archivo.");
        }
    }
}