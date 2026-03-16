<?php

namespace App\Http\Controllers;

use App\Models\Interruptores;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class InterruptoresController extends Controller {
    public array $thisAtributos;
    public string $FromController = 'Interruptores';


    //<editor-fold desc="Construc | filtro and dependencia">
    public function __construct() {
//        $this->middleware('permission:create Interruptores', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read Interruptores', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update Interruptores', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete Interruptores', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new Interruptores())->getFillable(); //not using
    }

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Interruptoress '));
        $Interruptoress = $this->Filtros($request)->get();
//        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $this->PerPageAndPaginate($request, $Interruptoress),
            'total' => $Interruptoress->count(),

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
        $Interruptoress = Interruptores::query();
        if ($request->has('search')) {
            $Interruptoress = $Interruptoress->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $Interruptoress = $Interruptoress->orderBy($request->field, $request->order);
        } else
            $Interruptoress = $Interruptoress->orderBy('updated_at', 'DESC');
        return $Interruptoress;
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

    public function PerPageAndPaginate($request, $Interruptoress) {
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $Interruptoress->forPage($page, $perPage),
            $Interruptoress->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return $paginated;
    }

    public function store(Request $request): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:Interruptoress');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Interruptores = Interruptores::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:Interruptoress EXITOSO', 'Interruptores id:' . $Interruptores->id . ' | ' . $Interruptores->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $Interruptores->nombre]));
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
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:Interruptoress');
        DB::beginTransaction();
        $Interruptores = Interruptores::findOrFail($id);
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Interruptores->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:Interruptoress EXITOSO', 'Interruptores id:' . $Interruptores->id . ' | ' . $Interruptores->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Interruptores->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($Interruptoresid) {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:Interruptoress');
        $Interruptores = Interruptores::find($Interruptoresid);
        $elnombre = $Interruptores->nombre;
        $Interruptores->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:Interruptoress', 'Interruptores id:' . $Interruptores->id . ' | ' . $Interruptores->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request) {
        $Interruptores = Interruptores::whereIn('id', $request->id);
        $Interruptores->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
