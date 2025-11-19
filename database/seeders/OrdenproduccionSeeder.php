<?php

namespace Database\Seeders;

use App\Models\Ordenproduccion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdenproduccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
		Ordenproduccion::create(['op' => 'OP123']);
		Ordenproduccion::create(['op' => 'OP124']);
		Ordenproduccion::create(['op' => 'OP125']);
		
    }
}
