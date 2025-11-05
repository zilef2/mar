<?php
//$ipAddress = $request->ip();

namespace App\Console\Commands;

class FinalFunctions {
	
	const string MSJ_EXITO = ' fue realizada con exito ';
	const string MSJ_FALLO = ' Fallo';
	
	public static function getClientIp() {
		$ipAddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
			$ipAddress = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			foreach ($ipList as $ip) {
				$ip = trim($ip);
				if (filter_var($ip, FILTER_VALIDATE_IP)) {
					$ipAddress = $ip;
					break;
				}
			}
		}
		elseif (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
			$ipAddress = $_SERVER['REMOTE_ADDR'];
		}
		
		return $ipAddress;
	}
	
	/*
	DoWebphp
	L2_LenguajeInsert
	DoSideBar
	DoFillable
	updateMigration
	 */
	public function DoWebphp($resource,$commandInstance): int {
		$directory = 'routes';
		$files = glob($directory . '/*.php');
		
		$insertable = "Route::resource(\"/$resource\", \\App\\Http\\Controllers\\" . ucfirst($resource) . "Controller::class);\n\t//aquipues";
		
		$pattern = '/\/\/aquipues/';
		
		$contadorVerificador = 0;
		foreach ($files as $file) {
			$content = file_get_contents($file);
			$contadorVerificador ++;
			
			if (!str_contains($content, $pattern)) {
				$content2 = preg_replace($pattern, $insertable, $content);
				//                $content2 = preg_replace($pattern, "$0$insertable", $content);
				file_put_contents($file, $content2);
				if ($content == $content2) {
					$this->info("Routes Actualizado: $file\n");
				}
				else {
					$this->info("Routes sin cambios: $file\n");
				}
			}
			else {
				$this->error("No existe aquipues en: $file\n");
				$contadorVerificador = 0;
				break;
			}
		}
		
		return $contadorVerificador;
	}
	
	public function L2_LenguajeInsert($modelName, &$submetodo,$commandInstance): int {
		if ($this->DoAppLenguaje($modelName,$commandInstance)) {
			$submetodo['Lenguaje'] = 0;
			$commandInstance->info('DoAppLenguaje' . self::MSJ_EXITO);
			$commandInstance->contadorMetodos ++;
			
			foreach ($commandInstance->aagenerateAttributes() as $key => $generateAttribute) {
				$this->DoAppLenguaje($key,$commandInstance);
				$submetodo['Lenguaje'] ++;
			}
			foreach ($commandInstance->generateForeign() as $generateAttribute) {
				$commandInstance->DoAppLenguaje($generateAttribute, $commandInstance,'mochar_id');
				$submetodo['Lenguaje'] ++;
			}
			
			return 1;
		}
		else {
			$commandInstance->error('DoAppLenguaje ' . self::MSJ_FALLO . ' Fallo en la linea: ' . __LINE__);
			$commandInstance->error('$this->contadorMetodos = ' . $commandInstance->contadorMetodos);
			$commandInstance->error('$submetodo = ' . $submetodo['Lenguaje']);
			
			return 0;
		}
	}
	
	
	
	/*
	 * REAL
	 */
	//REAL FINAL FUNCTION
	private function DoAppLenguaje($resource, $commandInstance,$mochar = 'no' ): int {
		$directory = 'lang/es/app.php';
		$files = glob($directory);
		
		if ($mochar == 'mochar_id') {
			$resource_Sin_Id = substr($resource, 0, - 3);
			$insertable = "'$resource' => '$resource_Sin_Id',\n\t\t//aquipues";
		}
		else {
			$insertable = "'$resource' => '$resource',\n\t\t//aquipues";
		}
		$pattern = '/\/\/aquipues/';
		$contadorVerificador = 0;
		foreach ($files as $file) {
			$contadorVerificador ++;
			$content = file_get_contents($file);
			if (!str_contains($content, $pattern)) {
				$content2 = preg_replace($pattern, $insertable, $content);
				// $content2 = preg_replace($pattern, "$0$insertable", $content);
				file_put_contents($file, $content2);
				if ($content == $content2) {
					$commandInstance->info("Language Actualizado: $file\n");
				}
				else {
					$commandInstance->info("Language sin cambios: $file\n");
				}
			}
			else {
				$commandInstance->error("No existe aquipues en: $file\n");
				$contadorVerificador = 0;
				break;
			}
		}
		
		return $contadorVerificador;
		
	}
	
}
