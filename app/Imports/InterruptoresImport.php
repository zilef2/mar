<?php

namespace App\Imports;

use App\Models\Interruptores;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;

class InterruptoresImport implements ToModel
{
    public int $CountFilas   = 0;
    public int $contar1      = 0;
    public int $contar2      = 0;
    public int $contar3      = 0;
    public int $contarVacios = 0;
    public array $larow;

    /*  Columnas del excel
        0 => "id"
        1 => "reference"
        2 => "description"
        3 => "value"
        4 => "discounted_value"
        5 => "unit_price"
    */

    public function model(array $row)
    {
        try {
            $this->larow = $row;

            if ($row[0] === 'reference' || $row[0] === 'REFERENCIA') return null;

            if (!$this->Requeridos($row)) {
                $this->contarVacios++;
                Log::info('vacio::' . implode(',', $row));
                return null;
            }

            $this->CountFilas++;

            $instance = new Interruptores([
                'reference' => $row[0],
                'description' => $row[1],
                'value' => $row[2],
                'discounted_value' => $row[3],
                'unit_price' => $row[4],
            ]);

            return $instance;

        } catch (\Throwable $th) {
            Log::error($th->getMessage() . ' L:' . $th->getLine() . ' Ubi:' . $th->getFile());
        }
    }

    public function Requeridos(array $theRow): bool
    {
        if (empty($theRow[0])) return false;
        if (!is_string($theRow[0])) {
			return false;
		}
		if (!is_int(intval($theRow[2]))) {
			return false;
		}
		if (!is_int(intval($theRow[3]))) {
			return false;
		}
		if (!is_int(intval($theRow[4]))) {
			return false;
		}

        return true;
    }
}