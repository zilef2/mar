<?php

namespace App\Http\Controllers;

use App\Models\Ordenproduccion;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrdenproduccionController extends Controller {
    public array $getFillableWithTypes;
    public string $FromController = 'Ordenproduccion';


    //<editor-fold desc="Construc | filtro and dependencia">
    public function __construct() {
        $this->getFillableWithTypes = (new Ordenproduccion())->getFillableWithTypes();
    }

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Ordenproduccions '));
        $Ordenproduccions = $this->Filtros($request)->get();

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $this->PerPageAndPaginate($request, $Ordenproduccions),
            'total' => $Ordenproduccions->count(),
            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all(['search', 'field', 'order']),
            'perPage' => (int)$perPage,
            'numberPermissions' => $numberPermissions,
            'losSelect' => $losSelect ?? [],
            'getFillableWithTypes' => $this->getFillableWithTypes ?? [],
        ]);
    }

    public function Filtros($request): Builder {
        $Ordenproduccions = Ordenproduccion::query();
        if ($request->has('search')) {
            $Ordenproduccions = $Ordenproduccions->where(function ($query) use ($request) {
                $query->where('pedido', 'LIKE', "%" . $request->search . "%")
                                        ->orWhere('op', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $Ordenproduccions = $Ordenproduccions->orderBy($request->field, $request->order);
        } else
            $Ordenproduccions = $Ordenproduccions->orderBy('updated_at', 'DESC');
        return $Ordenproduccions;
    }


    //</editor-fold>

    public function PerPageAndPaginate($request, $Ordenproduccions) {
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $page = request('page', 1); // Current page number
        $paginated = new LengthAwarePaginator(
            $Ordenproduccions->forPage($page, $perPage),
            $Ordenproduccions->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return $paginated;
    }

    public function store(Request $request): RedirectResponse {
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:Ordenproduccions');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Ordenproduccion = Ordenproduccion::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:Ordenproduccions EXITOSO', 'Ordenproduccion id:' . $Ordenproduccion->id . ' | ' . $Ordenproduccion->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $Ordenproduccion->nombre]));
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
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:Ordenproduccions');
        DB::beginTransaction();
        $Ordenproduccion = Ordenproduccion::findOrFail($id);
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Ordenproduccion->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:Ordenproduccions EXITOSO', 'Ordenproduccion id:' . $Ordenproduccion->id . ' | ' . $Ordenproduccion->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Ordenproduccion->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($Ordenproduccionid) {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:Ordenproduccions');
        $Ordenproduccion = Ordenproduccion::find($Ordenproduccionid);
        $elnombre = $Ordenproduccion->nombre;
        $Ordenproduccion->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:Ordenproduccions', 'Ordenproduccion id:' . $Ordenproduccion->id . ' | ' . $Ordenproduccion->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request) {
        $Ordenproduccion = Ordenproduccion::whereIn('id', $request->id);
        $Ordenproduccion->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
