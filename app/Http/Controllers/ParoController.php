<?php

namespace App\Http\Controllers;

use App\Models\paro;
use App\Http\Requests\StoreparoRequest;
use App\Http\Requests\UpdateparoRequest;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;


class ParoController extends Controller {
    public array $thisAtributos;
    public string $FromController = 'Paro';


    //<editor-fold desc="Construc | filtro and dependencia">
    public function __construct() {
//        $this->middleware('permission:create paro', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read paro', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update paro', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete paro', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new Paro())->getFillable();
    }

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' paros '));
        $paros = $this->Filtros($request)->get();

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $this->PerPageAndPaginate($request, $paros),
            'total' => $paros->count(),
            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all([
                                           'search',
                                           'field',
                                           'order'
                                       ]),
            'perPage' => (int)$perPage,
            'numberPermissions' => $numberPermissions,
            'losSelect' => [],
        ]);
    }

    public function Filtros($request): Builder {
        $paros = paro::query();
        if ($request->has('search')) {
            $paros = $paros->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $paros = $paros->orderBy($request->field, $request->order);
        } else
            $paros = $paros->orderBy('updated_at', 'DESC');
        return $paros;
    }

//    public function Dependencias()
//    {
//        $dependexsSelect = deependex::all('id','nombre as name')->toArray();
//        array_unshift($dependexsSelect,["name"=>"Seleccione un dependex",'id'=>0]);

//        $ejemploSelec = CentroCosto::all('id', 'nombre as name')->toArray();
//        array_unshift($ejemploSelec, ["name" => "Seleccione un ejemploSelec", 'id' => 0]);
//        return [$dependexsSelect];
//        return [$dependexsSelect,$ejemploSelec];
//    }

    //</editor-fold>

    public function PerPageAndPaginate($request, $paros) {
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $paros->forPage($page, $perPage),
            $paros->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return $paginated;
    }

    public function store(Request $request): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:paros');
        DB::beginTransaction();
//        $dependex = $request->dependex['id'];
//        $request->merge(['dependex_id' => $request->dependex['id']]);
        $paro = paro::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:paros EXITOSO', 'paro id:' . $paro->id . ' | ' . $paro->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $paro->nombre]));
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
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:paros');
        DB::beginTransaction();
        $paro = paro::findOrFail($id);
//        $request->merge(['dependex_id' => $request->dependex['id']]);
        $paro->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:paros EXITOSO', 'paro id:' . $paro->id . ' | ' . $paro->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $paro->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($paroid) {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:paros');
        $paro = paro::find($paroid);
        $elnombre = $paro->nombre;
        $paro->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:paros', 'paro id:' . $paro->id . ' | ' . $paro->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request) {
        $paro = paro::whereIn('id', $request->id);
        $paro->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
