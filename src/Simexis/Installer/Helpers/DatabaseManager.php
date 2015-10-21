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
	
    /**
     * Check for the server requirements.
     *
     * @return array
     */
    public function connections()
    {
        $results = [];
		$connections = config('database.connections');
		if(!is_array($connections))
			$connections = [];
		
        foreach($connections as $connection)
        {
			if(!in_array($connection['driver'], ['sqlite', 'mysql', 'pgsql', 'sqlsrv']))
				continue;
			$connection['default'] = Input::old('driver', config('database.default'));
            $results[$connection['driver']] = $connection;
        }
		
        return $results;
    }
	
	/*
	 * @param \Illuminate\Contracts\Foundation\Application $app
	 * @param \Simexis\Installer\Request\DatabaseRequest $request
	 *
	 * @return string
	 */
	public function setConfig(Application $app, DatabaseRequest $request) {
		$driver = $request->get('driver');
		$connections = $this->connections();
		$config = array_merge( $connections[$driver], $request->get($driver) );
		if(isset($config['default']))
			unset($config['default']);
		$config = array_merge(
			Arr::dot($app['config']->get('database')), 
			Arr::dot(['default' => $driver, 'connections' => [$driver => $config]])
		);
		
		$a = [];
		foreach($config AS $k=> $v)
			Arr::set($a, $k, $v);
		$app['config']->set('database', $a);
		
		return $driver;
	}
	
	public function writeConfig() {
		$config = app('config');
		$template = preg_replace_callback('~\'\{\{([^\}]*)\}\}\'~i',function($match) use($config) {
			if('CONNECTIONS_SQLITE_DATABASE' == $match[1]) 
				return 'database_path(\'' . md5(env('APP_KEY', 'database')) . '.sqlite\')';
			return var_export($config->get($this->formateKey($match[1])), true);
		}, $this->getConfigTemplate());
		
		$dbFile = base_path('config/database.php');
		if(!@file_put_contents($dbFile, $template))
			throw new Exception(Lang::get('installer::installer.database.error_db_write'));
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
     * Return database configuration template
     *
     * @throw Exception
     * @return array
     */
	private function getConfigTemplate() {
		$file = dirname(__DIR__) . '/templates/database.php';
		if(is_file($file))
			return file_get_contents($file);
		throw new Exception(Lang::get('installer::installer.database.error_db_template'));
	}

	
    /**
     * Return formated dot ket for database configuration
     *
     * @param string $key
     * @return string
     */
	private function formateKey($key) {
		return implode('.', ['database', strtolower(str_replace('_', '.', $key))]);
	}
}
