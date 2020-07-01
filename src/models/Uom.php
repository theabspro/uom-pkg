<?php

namespace Abs\UomPkg;

use Abs\HelperPkg\Traits\SeederTrait;
use App\BaseModel;
use App\Company;
use Illuminate\Database\Eloquent\SoftDeletes;

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

	public static function saveFromObject($record_data) {
		$record = [
			'Company Code' => $record_data->company_code,
			'Code' => $record_data->code,
			'Name' => $record_data->name,
			'Short Name' => $record_data->short_name,
			'Description' => $record_data->description,
		];
		return static::saveFromExcelArray($record);
	}

	public static function saveFromExcelArray($record_data) {
		$errors = [];
		$company = Company::where('code', $record_data['Company Code'])->first();
		if (!$company) {
			return [
				'success' => false,
				'errors' => ['Invalid Company : ' . $record_data['Company Code']],
			];
		}

		if (!isset($record_data['created_by_id'])) {
			$admin = $company->admin();

			if (!$admin) {
				return [
					'success' => false,
					'errors' => ['Default Admin user not found'],
				];
			}
			$created_by_id = $admin->id;
		} else {
			$created_by_id = $record_data['created_by_id'];
		}

		if (empty($record_data['Code'])) {
			$errors[] = 'Code is emtpy';
		}

		$record = static::firstOrNew([
			'company_id' => $company->id,
			'code' => $record_data['Code'],
		]);

		$result = Self::validateAndFillExcelColumns($record_data, Static::$excelColumnRules, $record);
		if (!$result['success']) {
			return $result;
		}
		$record->company_id = $company->id;
		$record->created_by_id = $created_by_id;
		$record->save();
		return [
			'success' => true,
		];
	}
	/*public static function createFromObject($record_data) {

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
	*/
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
