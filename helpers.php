<?php

use App\BaseModel;
use App\Models\Auth;
use App\Models\District;
use App\Models\Province;
use App\Models\Users;
use App\Models\Verification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

const ROLE_SUPERADMIN = "SuperAdmin";
const ROLE_ADMIN_DPP = "Admin Pusat";
const ROLE_ADMIN_DPD = "Admin Daerah";
const ROLE_ADMIN_DPC = "Admin Cabang";
const ROLE_CANDIDATE = "Calon Legislatif";

const PERM_EDIT_PROFILE = "Edit Profile";
const PERM_ADD_EDUCATION = "Add Education History";
const PERM_DELETE_EDUCATION = "Delete Education History";
const PERM_ADD_OCCUPATIONS = "Add Occupation History";
const PERM_DELETE_OCCUPATIONS = "Delete Occupation History";
const PERM_ADD_ORGANIZATION = "Add Organization History";
const PERM_DELETE_ORGANIZATION = "Delete Organization History";
const PERM_UPLOAD_FORM = "Generate Form";
const PERM_GENERATE_FORM = "Upload Form";
const PERM_DOWNLOAD_FORM = "Download Form";

const CANDIDATE_PERMISSION = [PERM_EDIT_PROFILE, PERM_ADD_EDUCATION, PERM_ADD_OCCUPATIONS,
	PERM_ADD_ORGANIZATION, PERM_DELETE_EDUCATION, PERM_DELETE_OCCUPATIONS, PERM_DELETE_ORGANIZATION,
	PERM_UPLOAD_FORM, PERM_GENERATE_FORM, PERM_DOWNLOAD_FORM];

define("ADMIN_PERMISSION", array_merge(CANDIDATE_PERMISSION, [ROLE_CANDIDATE]));

const ADMIN_ROLES = [ROLE_SUPERADMIN, ROLE_ADMIN_DPP, ROLE_ADMIN_DPD, ROLE_ADMIN_DPC];
define("ALL_ROLES", array_merge(ADMIN_ROLES, [ROLE_CANDIDATE]));

const MONTH_ID = [
	'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
	'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

if (!function_exists('dateUI')) {
	function dateUI(string $date): string {
		$dateTime = new DateTime($date);
		$date = $dateTime->format('d');
		$month = MONTH_ID[$dateTime->format('n') - 1];
		$year = $dateTime->format('Y');
		return "$date $month $year";
	}
}

if (!function_exists('initials')) {
	function initials(string $text, string $s1 = '', string $s2 = ' '): string {
		$results = implode($s1, array_map(fn($v) => $v[0], explode($s2, $text)));
		return strtoupper($results);
	}
}

if (!function_exists('idrFormat')) {
	function idrFormat(float $amount, string $prefix = 'Rp. '): string {
		return $amount < 0 ? idrFormat(abs($amount), "-$prefix")
			: $prefix . number_format($amount, 2, ',', '.');
	}
}

if (!function_exists('amountSays')) {
	function amountSays(float $amount, string $currency = 'rupiah', bool $final = true): string {
		$value = abs($amount);
		$letters = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
		$data = ($value < 12) ? " " . $letters[$value] : (($value < 20) ? amountSays($value - 10, $currency, false) . " belas"
			: (($value < 100) ? amountSays($value / 10, $currency, false) . " puluh " . amountSays($value % 10, $currency, false)
				: (($value < 200) ? " seratus " . amountSays($value - 100, $currency, false)
					: (($value < 1000) ? amountSays($value / 100, $currency, false) . " ratus " . amountSays($value % 100, $currency, false)
						: (($value < 2000) ? " seribu " . amountSays($value - 1000, $currency, false)
							: (($value < 1000000) ? amountSays($value / 1000, $currency, false) . " ribu " . amountSays($value % 1000, $currency, false)
								: (($value < 1000000000) ? amountSays($value / 1000000, $currency, false) . " juta " . amountSays($value % 1000000, $currency, false)
									: (($value < 1000000000000) ? amountSays($value / 1000000000, $currency, false) . " milyar " . amountSays(fmod($value,1000000000), $currency, false)
										: (($value < 1000000000000000) ? amountSays($value / 1000000000000, $currency, false) . " trilyun " . amountSays(fmod($value,1000000000000), $currency, false)
											: $letters[0])))))))));
		return $final ? ucwords(($amount < 0 ? "minus " : "") . trim($data) . " $currency") : trim($data);
	}
}

if (!function_exists('implodeWithAppendPrepend')) {
	function implodeWithAppendPrepend(array &$arr, string $prepend = '', string $append = '', string $separator = ','): string {
		foreach ($arr as $i => &$item) $arr[$i] = $prepend . $item . $append;
		return implode($separator, $arr);
	}
}

if (!function_exists('downloadUri')) {
	function downloadUri(string $path): string {
		$uTime = round(microtime(true) * 1000);
		return config('app.cdn') . $path . '?' . $uTime;
	}
}

if (!function_exists('randomString')) {
	function randomString(int $len): string {
		$randomString = '';
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		for ($i = 0; $i < $len; $i++) $randomString .= $characters[rand(0, $charactersLength - 1)];
		return $randomString;
	}
}

if (!function_exists('writeFile')) {
	function writeFile(string $location, string $contents, array $config = []): void {
		Storage::disk('sftp')->write($location, $contents, $config);
	}
}

if (!function_exists('toTable')) {
	function toTable(BaseModel $model): array {
		return array_filter($model->toArray(), function ($attr) use ($model) {
			$filter = array_merge(array_keys($model->getRelations()), [
				'id', 'add_rt', 'add_rw', 'village', 'postal_code', 'subdistrict', 'district', 'province'
			]);
			return !in_array($attr, $filter);
		}, ARRAY_FILTER_USE_KEY);
	}
}

if (!function_exists('paginateCollection')) {
	function paginateCollection(array|Collection $items, int $perPage = 15, int $page = null, $getValues = true): LengthAwarePaginator {
		$pageName = 'page';
		$page = $page ?: (Paginator::resolveCurrentPage($pageName) ?: 1);
		$items = $items instanceof Collection ? $items : Collection::make($items);
		$values = $getValues ? $items->forPage($page, $perPage)->values() : $items->forPage($page, $perPage);

		return new LengthAwarePaginator($values, $items->count(), $perPage, $page, [
			'path' => Paginator::resolveCurrentPath(), 'pageName' => $pageName
		]);
	}
}

if (!function_exists('adminLocation')) {
	function adminLocation(Auth|Authenticatable $auth, string &$default = 'DPP Partai Demokrat'): string {
		if (strlen(adminUsers($auth)?->code ?: '') == 2) {
			$data = Province::where('id', adminUsers($auth)?->code)->first();
			$default = "DPD Partai Demokrat " . ucwords(strtolower($data->name));
		} elseif (strlen(adminUsers($auth)?->code ?: '') == 4) {
			$data = District::where('id', adminUsers($auth)?->code)->first();
			$default = "DPC Partai Demokrat " . ucwords(strtolower($data->name));
		}
		return $default;
	}
}

if (!function_exists('adminUsers')) {
	function adminUsers(Auth|Authenticatable $auth): ?Users {
		return $auth->load('users')->users;
	}
}

if (!function_exists('arrPlusValue')) {
	function arrPlusValue(array &$array, string|int $key, int $value): void {
		if (!array_key_exists($key, $array)) $array[$key] = 0;
		$array[$key] += $value;
	}
}

if (!function_exists('getGeneratedFormPath')) {
	function getGeneratedFormPath(Collection $forms, string $name): string {
		$form = array_filter($forms->toArray(), fn($f) => $f['name'] == $name);
		return Arr::first($form) ? Arr::first($form)['path'] : '' ;
	}
}

if (!function_exists('curlToSahabat')) {
	function curlToSahabat(string $path, array $data, bool $isPost = false): object {
		$host = config('app.sahabat') . "/akudemokrat/$path";
		$headers = ['key-akudemokrat: YWt1ZGVtb2tyYXQyMDIy'];
		if (!$isPost) $host .= "?" . http_build_query($data);
		$curl = curl_init($host);
		curl_setopt($curl, CURLOPT_POST, intval($isPost));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if ($isPost) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$curl_exec = curl_exec($curl);
		curl_close($curl);
		abort_if(!$response = json_decode($curl_exec), 200, 'Failed to parse data!');
		abort_if(!$response->data or !$response->status, 200, 'KTA tidak valid!');
		$memberData = (array) $response->data;
		$memberData['nation_id'] = $response->data->nik;
		$memberData['name'] = $response->data->member_name;
		$memberData['membership_id'] = $response->data->new_membership_id;
		$memberData['cellphone'] = $response->data->cellular_phone_number;
		$memberData['province'] = $response->data->id_prov;
		$memberData['district'] = $response->data->id_kab;
		$memberData['subDistrict'] = $response->data->id_kec;
		$memberData['village'] = $response->data->id_kel;
		$memberData['education'] = $response->data->id;
		$memberData['postal_code'] = $response->data->kodepos;
		return (object) ['success' => true, 'data' => $memberData];
	}
}

if (!function_exists('getImageData')) {
	function getImageData(string $path): string {
		try {
			return file_get_contents($path);
		} catch (Exception $exception) {
			Log::error($exception->getTraceAsString());
			if (strpos($path, 'cards')) {
				return getImageData(resource_path('images/template/front_kta.jpg'));
			} elseif (strpos($path, 'identity')) {
				return getImageData(resource_path('images/template/front_ktp.jpg'));
			} else return getImageData(resource_path('images/template/no-photo.jpeg'));
		}
	}
}

if (!function_exists('sendUOWA')) {
	function sendUOWA(string $from, string $to, string $message) {
		$data = [
			'api_key' => 'b2d95af932eedb4de92b3496f338aa5f97b36ae0',
			'sender' => $from, 'number' => $to, 'message' => $message
		];
		$response = ['status' => false, 'msg' => 'Failed to call WA APIs!'];
		$curl = curl_init('https://wa.rsvpdemokrat.com/app/api/send-message');
		curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_POST, true);
		$call = curl_exec($curl);
		curl_close($curl);
		if ($data = json_decode($call)) $response = $data;
		return $response;
	}
}

if (!function_exists('dirSize')) {
	function dirSize(string $dir): int {
		$size = 0;
		foreach(glob($dir . '/*') as $path){
			is_file($path) && $size += filesize($path);
			is_dir($path)  && $size += dirSize($path);
		}
		return $size;
	}
}

if (!function_exists('dirOrFileSizeFormat')) {
	function dirOrFileSizeFormat(int $size): string {
		$unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
		for ($i = 0; $size > 1024; $i++) $size /= 1024;
		$endIndex = strpos($size, ".") + 3;
		return substr( $size, 0, $endIndex).' '.$unit[$i];
	}
}

if (!function_exists('scanFiles')) {
	function scanFiles(string $dir = "app", int $paginate = 0, bool $recursive = true): LengthAwarePaginator|array {
		$results = [];
		foreach (scandir(storage_path($dir)) as $file) {
			$filename = storage_path($dir) . DIRECTORY_SEPARATOR . $file;
			$size = dirOrFileSizeFormat(is_file($filename)
				? filesize($filename) : dirSize($filename));
			if (!in_array($file, ['.', '.DS_Store', '.gitignore'])) {
				if (is_file($filename) or ($file == '..') or !$recursive) {
					$files = str_replace(storage_path('app/public'), '/pub', $filename);
					$results[] = (object) ['files' => $files, 'name' => $file, 'size' => $size];
				} elseif (is_dir($filename)) {
					$directory = scanFiles($dir . DIRECTORY_SEPARATOR . $file);
					$dirValues = $paginate ? paginateCollection($directory, $paginate) : $directory;
					$results[] = (object) ['files' => $dirValues, 'name' => $file, 'size' => $size];
				}
			}
		}
		return $paginate ? paginateCollection($results, $paginate)->onEachSide(1) : $results;
	}
}

if (!function_exists('verificationRowView')) {
	function verificationRowView(string $label, string $hid, string $sid, Verification $verification): HtmlString {
		$hValue = $verification->$hid ? __('Tersedia') : __('Tidak Tersedia');
		$sValue = $verification->$sid ? __('Tersedia') : __('Tidak Tersedia');
		$disabled = auth()->user()->hasRole(ROLE_SUPERADMIN) ? '' : 'disabled';
		$iconCls = $verification->$sid ? 'primary cursor-pointer' : 'danger';
		$hChecked = $verification->$hid ? 'checked' : '';
		$sChecked = $verification->$sid ? 'checked' : '';
		$icon = $verification->$sid ? 'view' : 'x';
		return new HtmlString("<tr>
			<td class=\"border-b dark:border-darkmode-400 w-32\">$label</td>
			<td class=\"text-center border-b dark:border-darkmode-400 w-32\">
				<div class=\"form-check form-switch\">
					<input id=\"$hid\" name=\"$hid\" class=\"form-check-input\" type=\"checkbox\" $hChecked/>
					<label class=\"form-check-label\" for=\"$hid\">$hValue</label>
				</div>
			</td>
			<td class=\"text-center border-b dark:border-darkmode-400 w-32\">
				<div class=\"form-check form-switch\">
					<input id=\"$sid\" name=\"$sid\" class=\"form-check-input\" type=\"checkbox\" $sChecked $disabled/>
					<label class=\"form-check-label\" for=\"$sid\">$sValue</label>
				</div>
			</td>
			<td data-id=\"$sid\" class=\"text-center border-b dark:border-darkmode-400 w-32\">
				<i id=\"show\" data-name=\"$sid\" data-icon=\"$icon\" class=\"w-8 h-8 text-$iconCls mx-auto\"></i>
			</td>
		</tr>");
	}
}

if (!function_exists('userCode')) {
	function userCode(): ?int {
		if (auth()->user())
			return adminUsers(auth()->user())?->code ?: 0;
		return null;
	}
}