<?php 

namespace Simexis\Installer\Helpers;

use PDO;
use Lang;
use Input;
use Exception;
use ReflectionClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Simexis\Installer\Request\DatabaseRequest;
use Illuminate\Contracts\Foundation\Application;

class DatabaseManager {
	
	/*
	 * @param \Illuminate\Contracts\Foundation\Application $app
	 * @param \Simexis\Installer\Request\DatabaseRequest $request
	 *
	 * @return string
	 */
	public function setConfig(Application $app, DatabaseRequest $request) {
		$db = $app['config']->get('database');
		if(!$db)
			return false;
		$driver = $db['default'];
		$post_data = [
			'connections' => [
					$driver => [
						'host' => $request->get('host'),
						'database' => $request->get('database'),
						'username' => $request->get('username'),
						'password' => $request->get('password')
					]
				]
			];
			
		$config = array_merge(
			Arr::dot($db), 
			Arr::dot($post_data)
		);
		
		$a = [];
		foreach($config AS $k=> $v)
			Arr::set($a, $k, $v);
		$app['config']->set('database', $a);
		
		return $driver;
	}
	
	public function parseEnv($path) {
		if(!file_exists($path) || !is_file($path))
			return [];
		$lines = array_map('trim', file($path));
		$result = [];
		foreach($lines AS $row => $line) {
			$parts = explode('=', $line, 2);
			$result[$parts[0] ? : $row] = isset($parts[1]) ? $parts[1] : '';
		}
		return $result;
	}
	
	public function writeConfig(Application $app, DatabaseRequest $request) {
		$lines = $this->parseEnv(base_path('.env'));
		$data = [
			$this->formateKey('host') 		=> $request->get('host'),
			$this->formateKey('database') 	=> $request->get('database'),
			$this->formateKey('username') 	=> $request->get('username'),
			$this->formateKey('password') 	=> $request->get('password')
		];
		
		$lines = array_merge($lines, $data);
		
		$fp = @fopen(base_path('.env'), 'w+');
		if(!$fp)
			throw new Exception(Lang::get('installer::installer.database.error_db_write'));
		
		foreach($lines AS $key => $data) {
			if(is_int($key)) {
				fwrite($fp, implode('',["\n"]));
			} else {
				fwrite($fp, implode('',[$key,'=',$data,"\n"]));
			}
		}
		fclose($fp);
		return true;
	}
    /**
     * Migrate and seed the database.
     *
     * @return array
     */
    public function migrateAndSeed()
    {
        return $this->migrate();
    }
	
    /**
     * Execute migrations and seeders.
     *
     * @return array
     */
    public function updateDatabaseAndSeedTables()
    {
        return $this->updateDatabase();
    }
	
    /**
     * Run the migration and call the seeder.
     *
     * @return array
     */
    private function migrate()
    {
        try{
            Artisan::call('migrate');
        }
        catch(Exception $e){
            return $this->response($e->getMessage());
        }
        return $this->seed();
    }
	
    /**
     * Seed the database.
     *
     * @return array
     */
    private function seed()
    {
        try{
            Artisan::call('db:seed');
        }
        catch(Exception $e){
            return $this->response($e->getMessage());
        }
        return $this->response(Lang::get('installer::installer.final.finished'), 'success');
    }
	
    /**
     * Update the database.
     *
     * @return array
     */
    private function updateDatabase()
    {
        if ( ! empty(app('installer')->config('upgrade.migrations'))) {
            try {
                Artisan::call('migrate', ['--path' => $this->getMigrationPath()]);
            } catch (Exception $e) {
                return $this->response($e->getMessage(), 'danger');
            }
        }
        return $this->updateSeed();
    }
	
    /**
     * Seed the database.
     *
     * @return array
     */
    private function updateSeed()
    {
        if ( ! empty(app('installer')->config('upgrade.seeds'))) {
            try {
                $this->seedSpecificClasses();
            }
            catch
                (Exception $e){
                return $this->response($e->getMessage(), 'danger');
            }
        }
        return $this->response(trans('installer::installer.upgrade.finished'), 'success');
    }
	
    /**
     * Return a formatted error messages.
     *
     * @param $message
     * @param string $status
     * @return array
     */
    private function response($message, $status = 'danger')
    {
        return array(
            'status' => $status,
            'message' => $message
        );
    }

	
    /**
     * Return formated dot ket for database configuration
     *
     * @param string $key
     * @return string
     */
	private function formateKey($key) {
		return implode('_', ['DB', strtoupper($key)]);
	}
}
