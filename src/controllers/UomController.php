<?php

namespace Abs\UomPkg;
use App\Http\Controllers\Controller;
use App\Uom;
use Auth;
use Carbon\Carbon;
use DB;
use Entrust;
use Illuminate\Http\Request;
use Validator;
use Yajra\Datatables\Datatables;

class UomController extends Controller {

	public function __construct() {
		$this->data['theme'] = config('custom.theme');
	}

	public function getUomList(Request $request) {
		$uoms = Uom::withTrashed()

			->select([
				'uoms.id',
				'uoms.name',
				'uoms.short_name',
				'uoms.description',
				DB::raw('IF(uoms.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('uoms.company_id', Auth::user()->company_id)

			->where(function ($query) use ($request) {
				if (!empty($request->name)) {
					$query->where('uoms.name', 'LIKE', '%' . $request->name . '%');
				}
			})
			->where(function ($query) use ($request) {
				if ($request->status == '1') {
					$query->whereNull('uoms.deleted_at');
				} else if ($request->status == '0') {
					$query->whereNotNull('uoms.deleted_at');
				}
			})
		;

		return Datatables::of($uoms)
			->rawColumns(['name', 'action', 'status'])
			->addColumn('name', function ($uom) {
				$status = $uom->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indicator ' . $status . '"></span>' . $uom->name;
			})
			->addColumn('status', function ($uom) {
				$status = $uom->status == 'Active' ? 'green' : 'red';
				return '<span class="status-indigator ' . $status . '"></span>' . $uom->status;
			})
			->addColumn('action', function ($uom) {
				$img1 = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow.svg');
				$img1_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/edit-yellow-active.svg');
				$img_delete = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-default.svg');
				$img_delete_active = asset('public/themes/' . $this->data['theme'] . '/img/content/table/delete-active.svg');
				$output = '';
				if (Entrust::can('edit-uom')) {
					$output .= '<a href="#!/uom-pkg/uom/edit/' . $uom->id . '" id = "" title="Edit"><img src="' . $img1 . '" alt="Edit" class="img-responsive" onmouseover=this.src="' . $img1 . '" onmouseout=this.src="' . $img1 . '"></a>';
				}
				if (Entrust::can('delete-uom')) {
					$output .= '<a href="javascript:;" data-toggle="modal" data-target="#uom-delete-modal" onclick="angular.element(this).scope().deleteUom(' . $uom->id . ')" title="Delete"><img src="' . $img_delete . '" alt="Delete" class="img-responsive delete" onmouseover=this.src="' . $img_delete . '" onmouseout=this.src="' . $img_delete . '"></a>';
				}
				return $output;
			})
			->make(true);
	}

	public function getUomFormData(Request $request) {
		$id = $request->id;
		if (!$id) {
			$uom = new Uom;
			$action = 'Add';
		} else {
			$uom = Uom::withTrashed()->find($id);
			$action = 'Edit';
		}
		$this->data['success'] = true;
		$this->data['uom'] = $uom;
		$this->data['action'] = $action;
		return response()->json($this->data);
	}

	public function saveUom(Request $request) {
		// dd($request->all());
		try {
			$error_messages = [
				'short_name.required' => 'Short Name is Required',
				'short_name.unique' => 'Short Name is already taken',
				'short_name.min' => 'Short Name is Minimum 2 Charachers',
				'short_name.max' => 'Short Name is Maximum 32 Charachers',
				'name.required' => 'Name is Required',
				'name.unique' => 'Name is already taken',
				'name.min' => 'Name is Minimum 3 Charachers',
				'name.max' => 'Name is Maximum 191 Charachers',
			];
			$validator = Validator::make($request->all(), [
				'short_name' => [
					'required:true',
					'min:2',
					'max:32',
					'unique:uoms,short_name,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
				'name' => [
					'required:true',
					'min:3',
					'max:191',
					'unique:uoms,name,' . $request->id . ',id,company_id,' . Auth::user()->company_id,
				],
			], $error_messages);

			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
			}

			DB::beginTransaction();

			if (!$request->id) {
				$uom = new Uom;
				$uom->company_id = Auth::user()->company_id;
			} else {
				$uom = Uom::withTrashed()->find($request->id);
			}
			$uom->code = $request->short_name;
			$uom->fill($request->all());
			if ($request->status == 'Inactive') {
				$uom->deleted_at = Carbon::now();
			} else {
				$uom->deleted_at = NULL;
			}
			$uom->save();

			DB::commit();
			if (!($request->id)) {
				return response()->json([
					'success' => true,
					'message' => 'Uom Added Successfully',
				]);
			} else {
				return response()->json([
					'success' => true,
					'message' => 'Uom Updated Successfully',
				]);
			}
		} catch (Exceprion $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'error' => $e->getMessage(),
			]);
		}
	}

	public function deleteUom(Request $request) {
		DB::beginTransaction();
		// dd($request->id);
		try {
			$uom = Uom::withTrashed()->where('id', $request->id)->forceDelete();
			if ($uom) {
				DB::commit();
				return response()->json(['success' => true, 'message' => 'Uom Deleted Successfully']);
			}
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'errors' => ['Exception Error' => $e->getMessage()]]);
		}
	}

	public function getUoms(Request $request) {
		$uoms = Uom::withTrashed()
			->with([
				'uoms',
				'uoms.user',
			])
			->select([
				'uoms.id',
				'uoms.name',
				'uoms.short_name',
				DB::raw('IF(uoms.deleted_at IS NULL, "Active","Inactive") as status'),
			])
			->where('uoms.company_id', Auth::user()->company_id)
			->get();

		return response()->json([
			'success' => true,
			'uoms' => $uoms,
		]);
	}
}