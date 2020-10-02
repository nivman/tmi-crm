<?php

namespace Install\Helpers;

use App\User;
use App\Helpers\Env;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class Install
{
    protected static function dbTransaction($sql)
    {
        try {
            DB::transaction(function () use ($sql) {
                DB::unprepared(DB::raw($sql));
            });
            DB::commit();
            $result = ['success' => true, 'message' => 'Database tables are created.'];
        } catch (\Exception $e) {
            DB::rollback();
            $result = ['success' => false, 'SQL: unable to create tables, ' . $e->getMessage()];
        }

        return $result;
    }

    public static function createDemoData()
    {
        set_time_limit(300);
        return self::dbTransaction(Storage::get('demo.sql'));
    }

    public static function createEnv()
    {
        if (is_file(base_path('.env.example'))) {
            File::copy(base_path('.env.example'), base_path('.env'));
        }

        Env::update([
            'APP_KEY' => 'base64:' . base64_encode(random_bytes(32)),
            'APP_URL' => url('/'),
        ], false);
    }

    public static function createTables(Request $request, $data)
    {
        $result = self::isDbValid($data);
        if (!$result || $result['success'] == false) {
            return $result;
        }

        set_time_limit(300);
        $data['license']['id']      = '24093379';
        $data['license']['version'] = '1.0';
        $data['license']['type']    = 'install';

        $result = ['success' => false, 'message' => ''];
        $url    = 'https://api.tecdiary.net/v1/dbtables';
        $client = new Client(['headers' => ['Accept' => 'application/json'], 'verify' => false]);
        $res    = $client->request('POST', $url, ['form_params' => $data['license']])->getBody()->getContents();
        $sql    = json_decode($res);

        if (empty($sql->database)) {
            $result = ['success' => false, 'message' => 'No database received from install server, please check with developer.'];
        } else {
            $result = self::dbTransaction($sql->database);
        }

        Storage::put('code.json', '{ "key": "' . $data['license']['code'] . '" }');
        return $result;
    }

    public static function createUser($userData)
    {
        $user             = $userData;
        $user['password'] = Hash::make($userData['password']);
        $user             = User::create($user);
        $user->roles()->sync([1, 2]);
        // \Mail::to($user->email)->send(new \App\Mail\UserCreated($userData));
    }

    public static function finalize()
    {
        Env::update(['APP_INSTALLED' => 'true', 'APP_DEBUG' => 'false'], false);
        return true;
    }

    public static function isDbValid($data)
    {
        if (!File::exists(base_path('.env'))) {
            self::createEnv();
        }

        Env::update([
            'DB_HOST'     => $data['dbhost'],
            'DB_PORT'     => $data['dbport'],
            'DB_DATABASE' => $data['dbname'],
            'DB_USERNAME' => $data['dbuser'],
            'DB_PASSWORD' => $data['dbpass'] ?? '',
            'DB_SOCKET'   => $data['dbsocket'] ?? '',
        ], false);

        $result = false;
        \Config::set('database.default', 'mysql');
        \Config::set('database.connections.mysql.host', $data['dbhost']);
        \Config::set('database.connections.mysql.port', $data['dbport']);
        \Config::set('database.connections.mysql.database', $data['dbname']);
        \Config::set('database.connections.mysql.username', $data['dbuser']);
        \Config::set('database.connections.mysql.password', $data['dbpass'] ?? '');
        \Config::set('database.connections.mysql.unix_socket', $data['dbsocket'] ?? '');

        try {
            DB::reconnect();
            DB::connection()->getPdo();
            if (DB::connection()->getDatabaseName()) {
                $result = ['success' => true, 'message' => 'Yes! Successfully connected to the DB: ' . DB::connection()->getDatabaseName()];
            } else {
                $result = ['success' => false, 'message' => 'DB Error: Unable to connect!'];
            }
        } catch (\Exception $e) {
            $result = ['success' => false, 'message' => 'DB Error: ' . $e->getMessage()];
        }

        return $result;
    }

    public static function registerLicense(Request $request, $licence)
    {
        $licence['id']      = '24093379';
        $licence['path']    = $request->url();
        $licence['referer'] = $request->path();

        $url    = 'https://api.tecdiary.net/v1/license';
        $client = new Client(['headers' => ['Accept' => 'application/json'], 'verify' => false]);
        return $client->request('POST', $url, ['form_params' => $licence])->getBody()->getContents();
    }

    public static function requirements()
    {
        $requirements = [];

        if (version_compare(phpversion(), '7.4', '<')) {
            $requirements[] = 'PHP 7.4 is required! Your PHP version is ' . phpversion();
        }

        if (ini_get('safe_mode')) {
            $requirements[] = 'Safe Mode needs to be disabled!';
        }

        if (ini_get('register_globals')) {
            $requirements[] = 'Register Globals needs to be disabled!';
        }

        if (ini_get('magic_quotes_gpc')) {
            $requirements[] = 'Magic Quotes needs to be disabled!';
        }

        if (!ini_get('file_uploads')) {
            $requirements[] = 'File Uploads needs to be enabled!';
        }

        if (!class_exists('PDO')) {
            $requirements[] = 'MySQL PDO extension needs to be loaded!';
        }

        if (!extension_loaded('openssl')) {
            $requirements[] = 'OpenSSL PHP extension needs to be loaded!';
        }

        if (!extension_loaded('tokenizer')) {
            $requirements[] = 'Tokenizer PHP extension needs to be loaded!';
        }

        if (!extension_loaded('mbstring')) {
            $requirements[] = 'Mbstring PHP extension needs to be loaded!';
        }

        if (!extension_loaded('curl')) {
            $requirements[] = 'cURL PHP extension needs to be loaded!';
        }

        if (!extension_loaded('ctype')) {
            $requirements[] = 'Ctype PHP extension needs to be loaded!';
        }

        if (!extension_loaded('xml')) {
            $requirements[] = 'XML PHP extension needs to be loaded!';
        }

        if (!extension_loaded('json')) {
            $requirements[] = 'JSON PHP extension needs to be loaded!';
        }

        if (!extension_loaded('zip')) {
            $requirements[] = 'ZIP PHP extension needs to be loaded!';
        }

        if (!is_writable(base_path('storage/app'))) {
            $requirements[] = 'storage/app directory needs to be writable!';
        }

        if (!is_writable(base_path('storage/framework'))) {
            $requirements[] = 'storage/framework directory needs to be writable!';
        }

        if (!is_writable(base_path('storage/logs'))) {
            $requirements[] = 'storage/logs directory needs to be writable!';
        }

        return $requirements;
    }

    public static function routes()
    {
        Route::prefix('install')->middleware('install')->group(function () {
            Route::get('/', '\Install\Controllers\InstallController@index');
            Route::post('demo', '\Install\Controllers\InstallController@demo');
            Route::post('save', '\Install\Controllers\InstallController@save');
            Route::get('checklist', '\Install\Controllers\InstallController@show');
            Route::post('account', '\Install\Controllers\InstallController@account');
            Route::post('license', '\Install\Controllers\InstallController@license');
            Route::post('finalize', '\Install\Controllers\InstallController@finalize');
        });
    }
}
