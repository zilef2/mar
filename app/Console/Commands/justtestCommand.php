<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class justtestCommand extends Command {
	
	use Constants;
	
	const string MSJ_EXITO = ' fue realizada con exito ';
	// protected $signature = 'config:register {activation=on}';
	const string MSJ_FALLO = ' Fallo';
	public $generando;
	public int $contadorMetodos;
	protected $signature = 'nunca';
	protected $description = 'Command description';
	
	// public function handle()
	// {
	//     $config = Config::allowRegistrations($this->argument('activation') == 'on');
	
	//     $this->info($config->allow_registrations
	//         ? 'El registro de nuevos usuarios está activado'
	//         : 'El registro de nuevos usuarios está desactivado'
	//     );
	// }
	
	public function replaceWordInFiles($oldWord, $permiteRemplazo, $modelName, $depende): int {
		$folderMayus = ucfirst($modelName);
		$files = File::allFiles(base_path("resources/js/Pages/$modelName"));
		$controller = base_path("app/Http/Controllers/$folderMayus" . 'Controller.php');
		
		$depende = $depende == '' || $depende == null ? 'no_nada' : $depende;
		
		if ($permiteRemplazo['vue']) {
			foreach ($files as $file) {
				
				$content = file_get_contents($file);
				$content = str_replace(array($oldWord, ucfirst($oldWord), 'geeneric'),//ojo aqui, es estatico
				                       [$modelName, $folderMayus, $folderMayus], $content);
				file_put_contents($file, $content);
			}
		}
		
		//reemplazo de controlador
		if ($permiteRemplazo['controller']) {
			$sourcePath = base_path('app/Http/Controllers/' . ucfirst($oldWord) . 'Controller.php');
			$content = file_get_contents($sourcePath);
			$content = str_replace(array($oldWord, 'dependex', 'deependex', 'geeneric'),//TODO:ojo aqui, es estatico
			                       array($modelName, $depende, ucfirst($depende), ucfirst($modelName)), $content);
			file_put_contents($controller, $content);
		}
		
		return 1;
	}
	
	public function handle() {
		//validaciones del controlador
		$finalawnser = '';
		$ObjetoEnMira = 'Controller.php';
		$RutaDelArchivo = 'app/Http/Controllers/';
		$ruta = $RutaDelArchivo . 'generic' . $ObjetoEnMira;
		$this->info('verificando:: ' . $ruta);
		$existe = file_exists(base_path($ruta));
		
		if (!$existe) {
			$this->error('Fallo ValidatePags Controllers, en la linea: ' . __LINE__);
			
			$finalawnser .= 'error controller';
		}
		else {
			$finalawnser .= 'ok controller | ';
			
		}
		
		//validaciones del frontend vuejs
		$ObjetoEnMira = '';//es una carpeta
		$RutaDelArchivo = 'resources/js/Pages/';
		
		$ruta = $RutaDelArchivo . 'generic' . $ObjetoEnMira;
		$this->info('verificando:: ' . $ruta);
		$existe = file_exists(base_path($ruta));
		
		if (!$existe) {
			$this->error('Fallo ValidatePags resources, en la linea: ' . __LINE__);
			
			$finalawnser .= 'error vue';
		}
		else {
			$finalawnser .= 'ok vue  ';
			
		}
		$this->info($finalawnser);
	}
	
}
