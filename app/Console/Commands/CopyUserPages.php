<?php
//$ipAddress = $request->ip();

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/*
php artisan make:command copy:u
	//aquipues
	//aquipuesSide
php artisan optimize:clear ; php artisan ziggy:generate resources/js/ziggy.js ; npm run dev

*/

class CopyUserPages extends Command {
	
	use Constants;
	
	const string MSJ_EXITO = ' fue realizada con exito ';
	const string MSJ_FALLO = ' Fallo';
	public $generando;
	public $signature = 'copy:u';
	public $description = 'Copia de la entidad generica';
	public int $contadorMetodos;
	
	//notacion de notas:
	// //todo:
	// very usefull
	// heyRemember: --> quiero borrar esta notacion
	// ts has this
	// nexttochange:
	// todo: sync: añadir a los demas repos
	// justtesting: cuando hay que qutiar cosas que solo deberian aparecer en la version de pruebas
	//thisisnew!!!
	
		/**
	 * Para anotar que es hijo de una funcion ===>>>  s( watch(() => data.equipos)
	 * donde s() significa hijo (son) y watch() es la funcion hija
	 */
	
	public function aagenerateAttributes(): array {
		//string text number dinero date datetime boolean foreign json
		return [
			'nombre'         => 'string',
			'descripcion'    => 'string',
			'cantidad_horas' => 'number',
			
			//			'fecha_ultima_actuacion' => 'datetime',
			//			'sujetos_procesales'     => 'text',
			//			'es_privado'             => 'string',
			//			'cant_filas'             => 'number',
			//			'validacioncini'         => 'bool',
		];
	}
	
	public function handle(): int { //version 4nov 2025
		try {
			$EstosAtributs = implode('\n ', array_map(fn($k, $v) => "$k ($v)", array_keys($this->aagenerateAttributes()), $this->aagenerateAttributes()));
			$this->info('Iniciando copia de entidad generica, los atributos registrados son: ' . $EstosAtributs);
			$this->generando = self::getMessage('generando');
			
			$this->contadorMetodos = 0;
			$submetodo['Lenguaje'] = 0;
			
			$modelName = $this->ask('¿Cuál es el nombre del modelo? Recuerde revisar los atributos.');
			if (!$modelName || $modelName == '') {
				$this->info('Sin modelo');
				$this->error('Fallo copyu en la linea: ' . __LINE__);
				
				return 0;
			}
			$modelName = ucfirst($modelName);
			
			$this->info(Artisan::call('optimize'));
			$this->info(Artisan::call('optimize:clear'));
			
			$progressBar = $this->output->createProgressBar(10);
			$progressBar->start();
			
			//'generic',
			if($this->MetodologiaInicial($modelName,  null,$progressBar)) {;
				$this->info('MetodologiaInicial' . self::MSJ_EXITO);
			}
			else {
				$this->error('MetodologiaInicial ' . self::MSJ_FALLO . ' Fallo copyu MAIN en la linea: ' . __LINE__);
				
				return 0;
			}
			$progressBar->advance();
			
			
			$this->AddAttributesVue($modelName);
			$progressBar->advance();
			
			$this->Paso2($modelName, $submetodo); //web, language, sidebar , fillable , migrations
			$progressBar->advance();//5
			
			$this->Paso3($modelName);
			$progressBar->advance();
			
			$this->info(Artisan::call('optimize'));
			$this->info(Artisan::call('optimize:clear'));
			$progressBar->advance();//8
			
			$progressBar->finish();
			
			return 1;
		} catch (Exception $e) {
			$this->error("Ocurrio una excepcion que no fue controlada, revise el codigo. " . "FALLO CONTADOR: " . $this->contadorMetodos . "\nFALLO Lenguaje: " . $submetodo['Lenguaje'] . " \nexcepcion::: \n" . $e->getMessage() . ' en la linea ' . $e->getLine());
			
			return 0;
		}
	}
	

	
	/**
	 * @param mixed $modelName el nombre que escribe el usuario en la consola
	 * @param mixed $depende   nose?
	 * @return int
	 */
	public function MetodologiaInicial(string $modelName, mixed $depende, $progressBar): int {
		$this->warn("Empezando make:model");
		$genericWord = 'generic';
		
		try {
			$verificadoEntorno = $this->verificarQueEstamosEnElEntornoCorrecto();
			
			if($verificadoEntorno) $progressBar->advance();//1
			else {
				$this->error('verificarQueEstamosEnElEntornoCorrecto fallo, revise los permisos de escritura' . ' Fallo copyu en la linea: ' . __LINE__);
				
				return 0;
			}
			
			Artisan::call('make:model', ['name' => $modelName, '--all' => true]);
		} catch (Exception $e) {
			$this->error('Error en make:model: ' . $e->getMessage());
			
			return 0;
		}
		$progressBar->advance();//2
		
		//comandos de dependencias
		$this->warn("Empezando copies");
		try {
			Artisan::call('copy:f');// Commands/WriteFillable.php
		} catch (Exception $e) {
			$this->error('Error en copy:f: ' . $e->getMessage());
			
			return 0;
		}
		$this->warn("Ahora Lang");
		try {
			Artisan::call('lang:u ' . $modelName);
		} catch (Exception $e) {
			$this->error('Error en lang:u: ' . $e->getMessage());
			
			return 0;
		}
		
		if ($this->ValidatePagesGeneric($genericWord)) {
			$this->info('Validacion de controller y pages exitosa');
		}
		else {
			$this->error('Validacion de paginas fallo, revise los archivos de plantilla' 
			             . ' Fallo MetodologiaInicial en la linea: ' . __LINE__);
			
			return 0;
		}
		
		$RealizoVueConExito = $this->MakeVuePages($genericWord, $modelName);
		$mensaje = $RealizoVueConExito ? 
			self::getMessage('generando') . ' Vuejs' . self::MSJ_EXITO :
			self::getMessage('generando') . ' Vuejs' . self::getMessage('fallo');
		$this->info($mensaje);
		
		$RealizoControllerConExito = $this->MakeControllerPages($genericWord, $modelName);
		$mensaje = $RealizoControllerConExito ?
			self::getMessage('generando') . 'el controlador' . self::MSJ_EXITO :
			self::getMessage('generando') . ' controlador ' . self::getMessage('fallo');
		$this->info($mensaje);
		
		if ($RealizoControllerConExito && $RealizoVueConExito) {
			$this->info('Iniciando replaceWordInFiles \n '.$genericWord.' por ' . $modelName);
			$this->replaceWordInFiles($genericWord, 
			                          ['vue' => true, 'controller' => true],                        
			                          $modelName, $depende
			);
			return 1;
		}else{
			$this->error('problema con controller o vuejs' . ' Fallo MetodologiaInicial en la linea: ' . __LINE__);
			
			return 0;
		}
		
	}
	
	public function ValidatePagesGeneric($genericwrd): bool {
		//validaciones del controlador
		$ObjetoEnMira = 'Controller.php';
		$RutaDelArchivo = 'app/Http/Controllers/';
		$ruta = $RutaDelArchivo. 'generic'. $ObjetoEnMira;
		$existe = file_exists(base_path($ruta));
		
		if (!$existe) {
			$this->error('Fallo ValidatePags Controllers, en la linea: ' . __LINE__);
			
			return false;
		}
		
		//validaciones del frontend vuejs
		$ObjetoEnMira = '';
		$RutaDelArchivo = 'resources/js/Pages/';
		$ruta = $RutaDelArchivo. 'generic'. $ObjetoEnMira;
		$existe = file_exists(base_path($ruta));
		
		if (!$existe) {
			$this->error('Fallo ValidatePags resources, en la linea: ' . __LINE__);
			
			return false;
		}
		
		return true;
	}
	

	
	public function MakeVuePages($plantillaActual, $modelName): bool {
		$sourcePath = base_path('resources/js/Pages/' . $plantillaActual);
		$destinationPath = base_path("resources/js/Pages/$modelName");
		
		// Add this validation
		if (!File::exists($sourcePath)) {
			$this->error("La carpeta de origen '$plantillaActual' no existe." . ' Fallo copyu en la linea: ' . __LINE__);
			
			return false;
		}
		if (File::exists($destinationPath)) {
			$this->warn("La carpeta de destino '$modelName' ya existe.");
			$this->error('Fallo copyu en la linea: ' . __LINE__);
			
			return false;
		}
		File::copyDirectory($sourcePath, $destinationPath);
		
		return true;
	}
	
	private function MakeControllerPages($plantillaActual, $modelName): bool {
		$folderMayus = ucfirst($modelName);
		$sourcePath = base_path('app/Http/Controllers/' . $plantillaActual . 'Controller.php');
		if (!File::exists($sourcePath)) {
			$this->error("El controlador de origen '$sourcePath' no existe." . ' Fallo copyu en la linea: ' . __LINE__);
			
			return false;
		}
		
		$destinationPath = base_path("app/Http/Controllers/" . $folderMayus . "sController.php");
		
		if (File::exists($destinationPath)) {
			$this->warn("La carpeta de destino '$destinationPath' ya existe.");
			$this->error('Fallo copyu en la linea: ' . __LINE__);
			
			return false;
		}
		File::copyDirectory($sourcePath, $destinationPath);
		$this->info("- " . $sourcePath);
		$this->info("- " . $destinationPath);
		
		return true;
	}
	
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
	
	private function AddAttributesVue($modelName): int {
		$vueFilePath = resource_path("js/Pages/$modelName/Index.vue");
		
		if (!File::exists($vueFilePath)) {
			$this->error('El archivo Index.vue no existe.' . ' Fallo copyu en la linea: ' . __LINE__);
			
			return 0;
		}
		
		$content = File::get($vueFilePath);
		
		// Convertir los atributos en formato Vue
		$titulosArray = collect($this->aagenerateAttributes())->map(function ($type, $key) {
			return "    { order: '$key', label: '$key', type: '$type' },";
		})->implode("\n");
		
		// Expresión regular para encontrar la sección `const titulos = [`
		$pattern = '/const titulos = \[\s*([\s\S]*?)\s*\];/';
		
		// Reemplazar con los nuevos valores
		$replacement = "const titulos = [\n$titulosArray\n];";
		
		$newContent = preg_replace($pattern, $replacement, $content);
		
		if ($newContent) {
			File::put($vueFilePath, $newContent);
			$this->info('Archivo Index.vue actualizado correctamente.');
		}
		else {
			$this->error('No se pudo actualizar Index.vue.' . ' Fallo copyu en la linea: ' . __LINE__);
			
			return 0;
		}
		
		return 1;
	}
	
	private function Paso2($modelName, &$submetodo): int {
		//estos metodos para abajo tienen validacion
		$finalfunctions = new FinalFunctions();
		if ($finalfunctions->DoWebphp($modelName,$this)) {
			
			$this->info('DoWebphp' . self::MSJ_EXITO);
			$this->contadorMetodos ++; //1
		}
		else {
			$this->error('DoWebphp ' . self::MSJ_FALLO . ' Fallo copyu en la linea: ' . __LINE__);
			
			return 0;
		}
		
		if ($finalfunctions->L2_LenguajeInsert($modelName, $submetodo,$this) === 0) {
			$this->error('Fallo copyu en la linea: ' . __LINE__);
			
			return 0;
		}
		
		if ($this->DoSideBar($modelName)) {
			
			$this->info('DoSideBar' . self::MSJ_EXITO);
			$this->contadorMetodos ++;//3
		}
		else {
			$this->error('DoSideBar ' . self::MSJ_FALLO . ' Fallo copyu en la linea: ' . __LINE__);
			
			return 0;
		}
		$this->DoFillable($modelName);
		$this->contadorMetodos ++;//4
		$this->updateMigration($modelName);
		$this->contadorMetodos ++;//5
		
		return 1;
	}
	
	
	
	
	
	public function generateForeign(): array {
		return [//			'oferta_id' => 'oferta_id',
		];
		/*
	//            'valor_consig' => 'biginteger',
	//            'texto' => 'text',
	//            'fecha_legalizacion' => 'datetime',
	//            'descripcion' => 'text',
	//            'precio' => 'decimal',
		*/
		
	}
	
	private function DoSideBar($resource): int {
		$directory = 'resources/js/Components/SideBarMenu.vue';
		$files = glob($directory);
		
		$insertable = "'" . $resource . "',\n\t//aquipuesSide";
		$pattern = '/\/\/aquipuesSide/';
		
		$contadorVerificador = 0;
		foreach ($files as $file) {
			$content = file_get_contents($file);
			
			if (!str_contains($content, $pattern)) {
				$contadorVerificador ++;
				$content2 = preg_replace($pattern, $insertable, $content);
				//$content2 = preg_replace($pattern, "$0$insertable", $content);
				file_put_contents($file, $content2);
				if ($content != $content2) {
					$this->info("SideBarMenu.vue Actualizado: $file\n");
				}
				else {
					$this->info("SideBarMenu.vue sin cambios: $file\n");
				} //todo: revisar si ya existe
			}
			else {
				$this->error("No existe aquipues en: $file\n");
				$contadorVerificador = 0;
				break;
			}
		}
		
		return $contadorVerificador;
	}
	
	public function DoFillable($modelName): int {
		$attributes = array_merge($this->aagenerateAttributes(), $this->generateForeign());
		
		// Generar el fillable
		$fillable = array_keys($attributes);
		$fillableString = implode("', '", $fillable);
		
		// Ruta del modelo
		$modelPath = app_path("Models/$modelName.php");
		
		// Verificar si el modelo existe
		if (!File::exists($modelPath)) {
			$this->error("El modelo $modelName no existe." . ' Fallo copyu en la linea: ' . __LINE__);
			
			return 0;
		}
		
		// Leer el contenido del modelo
		$modelContent = File::get($modelPath);
		
		// Añadir el fillable y SoftDeletes
		$modelContent = preg_replace('/public \$fillable = \[.*?\];/s', "public \$fillable = ['$fillableString'];", $modelContent);
		if (!str_contains($modelContent, 'use SoftDeletes;')) {
			$modelContent = preg_replace('/class ' . $modelName . ' extends/', "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n\n    class $modelName extends", $modelContent);
		}
		
		// Generar relaciones belongsTo
		$foreignKeys = $this->generateForeign();
		foreach ($foreignKeys as $key => $value) {
			$functionName = lcfirst(str_replace('_id', '', $key)); // Nombre de la función
			$relatedModel = ucfirst(str_replace('_id', '', $key)); // Nombre del modelo relacionado
			
			// Verificar si la relación ya existe
			if (!str_contains($modelContent, "function $functionName(")) {
				$relationMethod = "\n    public function $functionName(): BelongsTo\n    {\n        return \$this->belongsTo($relatedModel::class);\n    }\n";
				
				// Insertar la relación antes del final de la clase
				$modelContent = preg_replace('/}\s*$/', "$relationMethod\n}", $modelContent);
			}
		}
		
		// Guardar el contenido modificado
		File::put($modelPath, $modelContent);
		
		$this->info("El fillable, SoftDeletes y relaciones han sido añadidos al modelo $modelName.");
		
		return 1;
	}
	
	public function updateMigration($modelName): int {
		// === ⚠️ IMPORTANTE: Corregir duplicación y posibles typos en funciones ===
		// Asumiendo que generateAttributes() es la función correcta para obtener los atributos.
		$atributos = $this->aagenerateAttributes();
		
		// 1. Encontrar el archivo de migración
		$migrationFile = collect(glob(database_path('migrations/*.php')))->first(fn($file) => str_contains($file, 'create_' . Str::snake(Str::plural($modelName)) . '_table'));
		
		if (!$migrationFile) {
			$this->error("No se encontró la migración para $modelName" . ' Fallo copyu en la linea: ' . __LINE__);
			
			return 0;
		}
		
		// 2. Generar el código de las columnas
		$columns = collect($atributos)->map(function ($type, $name) {
			// Validación básica de nombre (evita problemas con guiones)
			if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
				// Podrías lanzar una excepción o registrar un error si el nombre es inválido
				throw new \InvalidArgumentException("El nombre de la columna '$name' contiene caracteres no permitidos.");
			}
			
			// --- Tipos Personalizados Solicitados y Comunes ---
			
			// money => decimal(62, 2)
			if ($type === 'dinero') {
				return "\$table->decimal('$name', 62, 2)->default(0);";
			}
			
			// float1 => float con 1 decimal (ej: 10, 1)
			if ($type === 'float1') {
				return "\$table->float('$name', 10, 1)->nullable();";
			}
			
			// float3 => float con 3 decimales (ej: 10, 3)
			if ($type === 'float3') {
				return "\$table->float('$name', 10, 3)->nullable();";
			}
			// bigdecimal3 => decimal(60, 3) para números grandes con 3 decimales
			if ($type === 'bigdecimal3') {
				// 60 es la precisión total (dígitos antes y después del punto)
				// 3 es la escala (dígitos después del punto)
				return "\$table->decimal('$name', 60, 3)->nullable();";
			}
			
			// dateTime con default(now())
			if ($type === 'dateTime') {
				return "\$table->dateTime('$name')->default(now());";
			}
			
			// text (para textos largos)
			if ($type === 'text') {
				return "\$table->text('$name')->nullable();";
			}
			
			// json (para datos estructurados)
			if ($type === 'json') {
				return "\$table->json('$name')->nullable();";
			}
			
			// --- Aliases y Tipos Base ---
			
			// number => integer (tu alias solicitado/existente)
			if ($type === 'number') {
				$type = 'integer';
			}
			
			// boolean con default(false)
			if ($type === 'boolean') {
				// Se puede omitir el default(false) si se usa ->boolean() directamente,
				// pero lo mantenemos para consistencia.
				return "\$table->boolean('$name')->default(false);";
			}
			
			// Catch-all: Usa el tipo tal cual (string, integer, etc.) y permite nulls.
			return "\$table->$type('$name')->nullable();";
		})->implode("\n            ");
		
		// 3. Insertar las columnas en el archivo
		$content = file_get_contents($migrationFile);
		// Nota: El regex busca 'Schema::create(...) {', lo que funciona si el archivo está formateado así.
		$content = preg_replace('/Schema::create\(.*?\{/', "$0\n            $columns", $content);
		file_put_contents($migrationFile, $content);
		
		$this->info("Migración actualizada para $modelName");
		
		return 1;
	}
	
	/**
	 * @param mixed $modelName
	 * @return int|mixed
	 */
	public function Paso3(mixed $modelName): int {
		$this->info("Iniciando generación de fillable para el modelo: $modelName");
		
		$result = $this->call('generate:fillable', [
			// El primer elemento es el nombre del argumento definido en la firma del comando
			'modelName' => $modelName,
		]);
		if ($result) {
			$this->info("Comando 'generate:fillable' ejecutado con éxito.");
		}
		else {
			$this->error("El comando 'generate:fillable' falló." . ' Fallo copyu en la linea: ' . __LINE__);
		}
		
		return $result;
	}
	
	/**
	 * @return void
	 * @throws \Exception
	 */
	public function verificarQueEstamosEnElEntornoCorrecto(): int {
		// Verificar que estamos en el entorno correcto
		if (!app()->runningInConsole()) {
			// Forzar entorno de consola
			app()->instance('runningInConsole', true);
		}
		
		// Verificar permisos de escritura
		$paths = [
			app_path('Models'),
			app_path('Http/Controllers'),
			database_path('migrations'),
			database_path('factories'),
			database_path('seeders')
		];
		
		foreach ($paths as $path) {
			$this->info("Verificando permisos de escritura en: {$path}");
			if (!is_writable($path)) {
				$this->error("Directorio no escribible: {$path}");
				return 0;
			}
		}
		return 1;
	}
	
}
