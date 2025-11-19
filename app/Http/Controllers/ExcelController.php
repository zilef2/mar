<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;

use App\helpers\HelpExcel;
use App\Http\Requests\ReporteRequest;
use App\Http\Requests\ReporteUpdateRequest;
use App\Imports\PersonalImport;
use App\Jobs\ImportGenericousChunkJob;
use App\Models\Ordenproduccion;
use App\Models\Reporte;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller {
	
	public function uploadOP(Request $request): \Illuminate\Http\RedirectResponse //import
	{
		//		ini_set('max_execution_time', 360); // 6 minutos
		
		ini_set('max_execution_time', 1800); // 30 minutos
		
		$pesoMaximo = 8192;
		$pesoString = (int)($pesoMaximo / 1000) . 'MB';
		$VariablesEsteProyecto = [
			'log'          => 'Este es el log de la importación de Genericous',
			'Validaciones' => [
				'formatos1'           => 'xlsx',
				'formatos2'           => 'xls',
				'mensajeErrorFormato' => 'xls',
				'pesoMaximo'          => $pesoMaximo,
				'pesoMaximoerror'     => 'El archivo debe pesar menos de ' . $pesoString,
			],
		];
		
		$exten = $request->archivo2->getClientOriginalExtension();
		if ($exten != 'xlsx' && $exten != 'xls') {
			return back()->with('warning', 'El archivo debe ser de Excel');
		}
//		Myhelp::EscribirEnLog($this, $VariablesEsteProyecto['log'], ' Subir a excel, paso las primeras validaciones');
		$pesoKilobyte = ((int)($request->archivo2->getSize())) / (1024);
		if ($pesoKilobyte > $pesoMaximo) {
			return back()->with('warning', $VariablesEsteProyecto['Validaciones']['pesoMaximoerror']);
		}
		
		try {
			$ruta = $request->file('archivo2')->storeAs('importGenericous', uniqid() . '_' . $request->file('archivo2')->getClientOriginalName());
			$theEmail = Myhelp::AuthU()->email;
//			$request->file('archivo2')->move(storage_path('app/importOrden'), $ruta);
		
			ImportGenericousChunkJob::dispatch($ruta, $theEmail);
			
			return back()->with('success', 'La importación está en proceso. Recibirás un correo al finalizar.');
			
		} catch (ValidationException $e) {
			ini_set('max_execution_time', 180); // 3 minutos
			
			$failures = $e->failures();
			$errorRows = collect($failures)->map(function ($failure) {
				$rowNumber = $failure->row();
				$attribute = $failure->attribute();
				$errors = $failure->errors();
				$values = $failure->values();
				
				$errorMessage = "Fila {$rowNumber}, Columna '{$attribute}': ";
				$errorMessage .= implode(', ', $errors);
				$errorMessage .= " (Valores: " . json_encode($values) . ")";
				
				return $errorMessage;
			});
			
			$errorSummary = $errorRows->implode('; ');
			
			$message = 'Se encontraron errores en el archivo Excel.';
			if ($errorSummary) {
				$message .= ' Detalles: ' . $errorSummary;
			}
			else {
				$message .= ' Detalles: ' . $e->getMessage(); // Mostrar el mensaje original si no hay fallas detalladas
			}
			
			return back()->with('warning', 'Error Excel. ' . $e->getMessage() . '. filas con errores: ' . $message);
		}
	}
	
	public function subirexceles() { //just  a view
		$permissions = Myhelp::EscribirEnLog($this, ' subir exceles GET');
		$numberPermissions = Myhelp::getPermissionToNumber($permissions);
		
		return Inertia::render('User/subirExceles', [
			'numUsuarios' => count(User::all()) - 1,
			'numOrdenes' => count(Ordenproduccion::all()),
			 'numberPermissions'   => $numberPermissions
		]);
	}
	
	
	public function uploadUser(Request $request) {
		Myhelp::EscribirEnLog($this, get_called_class(), 'Empezo a importar', false);
		$countfilas = 0;
		try {
			if ($request->archivo1) {
				
				$helpExcel = new HelpExcel();
				$mensageWarning = $helpExcel->validarArchivoExcel($request);
				if ($mensageWarning != '') {
					return back()->with('warning', $mensageWarning);
				}
				
				$personalimpo = new PersonalImport();
				Excel::import($personalimpo, $request->archivo1);
				$countfilas = $personalimpo->CountFilas;
			
				$MensajeWarning = $this->MensajeWar($personalimpo);
				
				if ($MensajeWarning !== '') {
					return back()->with('success', 'Usuarios nuevos: ' . $countfilas)->with('warning', $MensajeWarning);
				}
				
				Myhelp::EscribirEnLog($this, 'IMPORT:reportes', ' finalizo con exito', false);
				
				if ($countfilas == 0) {
					return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
				}else {
					return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
				}
			}else {
				return back()->with('error', __('app.label.op_not_successfully') . ' archivo no seleccionado');
			}
			
		} catch (\Throwable $th) {
			Myhelp::EscribirEnLog($this, 'IMPORT:reportes', ' Fallo importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile(), false);
			
			return back()->with('error', __('app.label.op_not_successfully') . ' Usuario del error: ' . $personalimpo->larow[0] . ' error en la iteracion ' . $countfilas . ' ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
		}
	}
	
	private function MensajeWar($import) {
		$contares = [
			$import->contar1,
			$import->contar2,
			$import->contar3,
			$import->contarVacios,
		];
		$mensajesWarnings = [
			'Cedulas repetidas: ',
			'nombre vacio: ',
			'#identificacions no numericas: ',
			
			'#filas con celdas vacias: ',
		];
		
		$mensaje = '';
		foreach ($mensajesWarnings as $key => $value) {
			if ($contares[$key] > 0) {
				$mensaje .= $value . $contares[$key] . '. ';
			}
		}
		
		return $mensaje;
	}
}
