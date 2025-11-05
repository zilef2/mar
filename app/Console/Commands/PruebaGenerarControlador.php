<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PruebaGenerarControlador extends Command
{
	
	use Constants;
	
	
    protected $signature = 'nunca';
    // protected $signature = 'config:register {activation=on}';

    protected $description = 'Command description';
	const string MSJ_EXITO = ' fue realizada con exito ';
	const string MSJ_FALLO = ' Fallo';
	public $generando;
	public int $contadorMetodos;

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
    public function handle()
    {
		
			$modelName = 'Paro';
			$this->replaceWordInFiles('generic', 
			                          ['vue' => true, 'controller' => false],                        
			                          $modelName,null
			);
			return 1;
//		
//        $codigo = '$personas = User::all();';
//        $this->info('Actualmente se tienen '. User::all()->count() . ' Usuarios en before_BD');
//
//        file_put_contents(app_path('Http/Controllers/NuevoControlador.php'), "
//            <?php\n \n
//            namespace App\Http\Controllers;\n \n
//            use App\Http\Controllers\Controller;\n
//            use App\Models\User;\n
//            \n
//            class NuevoControlador extends Controller\n
//            {\n
//                public function index()\n
//                {\n
//                    $codigo\n
//                }\n
//            }
//        ");
//
//        $this->info('El controlador ha sido generado exitosamente.');
//        return Command::SUCCESS;
    }
	
}
