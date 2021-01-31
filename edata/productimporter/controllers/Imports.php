<?php
namespace Edata\ProductImporter\Controllers;
use Backend\Classes\Controller;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use BackendMenu;
use BackendAuth;
class Imports extends Controller {
    public $implement     = ['Backend\Behaviors\ListController', 'Backend\Behaviors\ReorderController'];
    public $listConfig    = 'config_list.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public function __construct() {
        parent::__construct();
        BackendMenu::setContext('Edata.ProductImporter', 'main-menu-item');
    }
    public function fileImport() {
        try {
            if (!file_exists(storage_path('app/edata/'))) {
				mkdir(storage_path('app/edata/'), 0755, true);
			}
            $dateNow = date("Y-m-d H:i:s");
            $user    = BackendAuth::authenticate([
                'login' => post('login'),
                'password' => post('password')
            ]);
            if (BackendAuth::check() && isset($_FILES) && !empty($_FILES['file_name'])) {
                $org_file_name = $_FILES['file_name']['name'];
                $file_name     = Str::random(35) . ".json";
                $temp_path     = $_FILES['file_name']['tmp_name'];
                $file_size     = $_FILES['file_name']['size'];
                if (move_uploaded_file($temp_path, storage_path('app/edata/') . $file_name)) {
                    $data       = array(
                        "org_file_name" => $org_file_name,
                        "file_name" => $file_name,
                        "user_name" => $user->getFullNameAttribute(),
                        "created_at" => $dateNow,
                        "status" => "Waiting"
                    );
                    $data['id'] = DB::TABLE('edata_productimporter_files')->INSERTGETID($data);
                    Queue::push('\Edata\ProductImporter\Console\ImportsJob', $data);
                    return "File saved for import.";
                }
            } else {
                return "File couldn't saved for import, please contact administrator.";
            }
        }
        catch (\October\Rain\Auth\AuthException $e) {
            $authMessage = $e->getMessage();
            if (strrpos($authMessage, 'hashed credential') !== false) {
                return 'Login/Password missmatched';
            } elseif (strrpos($authMessage, 'user was not found') !== false) {
                return 'Login/Password missmatched';
            } elseif (strrpos($authMessage, 'not activated') !== false) {
                return 'User account not yet activated';
            } else {
                return "Something weird happened, please contact administrator.";
            }
        }
    }
}