<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class CotizacionController extends Controller {
    public array $thisAtributos;
    public string $FromController = 'Cotizacion';


    //<editor-fold desc="Construc | filtro and dependencia">
    public function __construct() {
//        $this->middleware('permission:create Cotizacion', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read Cotizacion', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update Cotizacion', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete Cotizacion', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new Cotizacion())->getFillable(); //not using
    }

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Cotizacions '));
        $Cotizacions = $this->Filtros($request)->get();
//        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $this->PerPageAndPaginate($request, $Cotizacions),
            'total' => $Cotizacions->count(),

            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all([
                                           'search',
                                           'field',
                                           'order'
                                       ]),
            'perPage' => (int)$perPage,
            'numberPermissions' => $numberPermissions,
            'losSelect' => $losSelect ?? [],
        ]);
    }

    public function Filtros($request): Builder {
        $Cotizacions = Cotizacion::query();
        if ($request->has('search')) {
            $Cotizacions = $Cotizacions->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $Cotizacions = $Cotizacions->orderBy($request->field, $request->order);
        } else
            $Cotizacions = $Cotizacions->orderBy('updated_at', 'DESC');
        return $Cotizacions;
    }

//    public function Dependencias()
//    {
//        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
//        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);

//        $ejemploSelec = CentroCosto::all('id', 'nombre as name')->toArray();
//        array_unshift($ejemploSelec, ["name" => "Seleccione un ejemploSelec", 'id' => 0]);
//        return [$no_nadasSelect];
//        return [$no_nadasSelect,$ejemploSelec];
//    }

    //</editor-fold>

    public function PerPageAndPaginate($request, $Cotizacions) {
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $Cotizacions->forPage($page, $perPage),
            $Cotizacions->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return $paginated;
    }

    public function store(Request $request): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:Cotizacions');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Cotizacion = Cotizacion::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:Cotizacions EXITOSO', 'Cotizacion id:' . $Cotizacion->id . ' | ' . $Cotizacion->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $Cotizacion->nombre]));
    }

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function create() {
    }

    //fin store functions

    public function show($id) {
    }

    public function edit($id) {
    }

    public function update(Request $request, $id): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:Cotizacions');
        DB::beginTransaction();
        $Cotizacion = Cotizacion::findOrFail($id);
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Cotizacion->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:Cotizacions EXITOSO', 'Cotizacion id:' . $Cotizacion->id . ' | ' . $Cotizacion->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Cotizacion->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($Cotizacionid) {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:Cotizacions');
        $Cotizacion = Cotizacion::find($Cotizacionid);
        $elnombre = $Cotizacion->nombre;
        $Cotizacion->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:Cotizacions', 'Cotizacion id:' . $Cotizacion->id . ' | ' . $Cotizacion->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request) {
        $Cotizacion = Cotizacion::whereIn('id', $request->id);
        $Cotizacion->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

    public function generatePdf($id) {
        $Cotizacion = Cotizacion::findOrFail($id);
        
        $data = [
            'cotizacion' => $Cotizacion
        ];
        
        $pdf = Pdf::loadView('pdfs.cotizacion', $data);
        return $pdf->stream('cotizacion_' . $Cotizacion->id . '.pdf');
    }

}
