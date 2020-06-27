<?php

namespace Abs\UomPkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\Company;
use App\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;

class Uom extends BaseModel {
	use SeederTrait;
	use SoftDeletes;
	protected $table = 'uoms';
	public $timestamps = true;
	protected $fillable = [
		'company_id',
		'code',
		'short_name',
		'name',
		'description',
	];

	protected static $excelColumnRules = [
		'Code' => [
			'table_column_name' => 'code',
			'rules' => [
				'required' => [
				],
			],
		],
		'Short Name' => [
			'table_column_name' => 'short_name',
			'rules' => [
				'nullable' => [
				],
			],
		],
		'Name' => [
			'table_column_name' => 'name',
			'rules' => [
				'nullable' => [
				],
			],
		],
		'Description' => [
			'table_column_name' => 'description',
			'rules' => [
				'nullable' => [
				],
			],
		],
	];

	// Getter & Setters --------------------------------------------------------------

	// Getter & Setters --------------------------------------------------------------

	public static function createFromObject($record_data) {

		$errors = [];
		$company = Company::where('code', $record_data->company)->first();
		if (!$company) {
			dump('Invalid Company : ' . $record_data->company);
			return;
		}

		$admin = $company->admin();
		if (!$admin) {
			dump('Default Admin user not found');
			return;
		}

		$type = Config::where('name', $record_data->type)->where('config_type_id', 89)->first();
		if (!$type) {
			$errors[] = 'Invalid Tax Type : ' . $record_data->type;
		}

		if (count($errors) > 0) {
			dump($errors);
			return;
		}

		$record = self::firstOrNew([
			'company_id' => $company->id,
			'name' => $record_data->tax_name,
		]);
		$record->type_id = $type->id;
		$record->created_by_id = $admin->id;
		$record->save();
		return $record;
	}

	public static function getList($params = [], $add_default = true, $default_text = 'Select Uom') {
		$list = Collect(Self::select([
			'id',
			'code as name',
		])
				->orderBy('code')
				->get());
		if ($add_default) {
			$list->prepend(['id' => '', 'name' => $default_text]);
		}
		return $list;
	}

}
