<?php
//esto es main
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\OrdenproduccionController;
use App\Http\Controllers\ParametrosController;
use App\Http\Controllers\ParoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActividadsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReprocesosController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

//Route::get('/', function () { return "El sistema responde correctamente."; });
Route::get('/', function () { return redirect('/login'); });

Route::get('/dashboard', [UserController::class, 'Dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/RRepor', [UserController::class, 'RRepor'])->middleware(['auth', 'verified'])->name('RRepor');

Route::get('/setLang/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return back();
})->name('setlang');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //# user
    Route::resource('/user', UserController::class)->except('create', 'show', 'edit');
    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');

    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');
    Route::post('/role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');

    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');
    Route::post('/permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])->name('permission.destroy-bulk');


    Route::resource('/parametro', ParametrosController::class);

    //# SIDEBARMENU
    Route::resource('/reporte', ReportesController::class);
    Route::post('/reporte/destroy-bulk', [ReportesController::class, 'destroyBulk'])->name('reporte.destroy-bulk');
    Route::post('/uploadUser', [ExcelController::class, 'uploadUser'])->name('uploadUser');
    Route::post('/uploadOP', [ExcelController::class, 'uploadOP'])->name('uploadOP');
     Route::get('/subirexceles', [ExcelController::class, 'subirexceles'])->name('subirexceles');

    Route::get('/reporte/createdev', [ReportesController::class, 'createdev'])->name('createdev');


    Route::resource('/actividad', ActividadsController::class);

    Route::resource('/Paro', ParoController::class);
    Route::resource('/reproceso', ReprocesosController::class);

    Route::resource('/Ordenproduccion', OrdenproduccionController::class);
	//aquipues

    //# EXCEL
    Route::get('/IMI', [UserController::class,'todaBD'])->name('IMIdb');
});


require __DIR__ . '/auth.php';

// <editor-fold desc="Artisan">
Route::get('/exception', function () {
    throw new Exception('Probando excepciones y enrutamiento. La prueba ha concluido exitosamente.');
});

Route::get('/foo', function () {
    if (file_exists(public_path('storage'))) {
        return 'Ya existe';
    }
    App('files')->link(
        storage_path('App/public'),
        public_path('storage')
    );
    return 'Listo';
});

Route::get('/clear-c', function () {
    Artisan::call('optimize');
    Artisan::call('optimize:clear');
    return "Optimizacion finalizada";
    // throw new Exception('Optimizacion finalizada!');
});

Route::get('/tmantenimiento', function () {
    echo Artisan::call('down --secret="token-it"');
    return "Aplicación abajo: token-it";
});
Route::get('/Arriba', function () {
    echo Artisan::call('up');
    return "Aplicación funcionando";
});
Route::get('/test-email', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Este es un correo de prueba.', function ($message) {
            $message->to('ajelof2@gmail.com')
                ->subject('Correo de prueba');
        });
        return 'Correo enviado con éxito.';
    } catch (\Exception $e) {
        return 'Error al enviar el correo: ' . $e->getMessage();
    }
});
//</editor-fold>