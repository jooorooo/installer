<?php 

namespace Simexis\Installer;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Simexis\Installer\Helpers\Render;

class InstallerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
	 * Register the service provider.
	 *
	 * @return void
	 */
    public function register()
    { 
		$this->registerProvider();
    }

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
    public function boot()
    { 
		
		$this->loadViewsFrom(__DIR__.'/Views', 'installer');
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'installer');
		
        $this->publishes([
            __DIR__.'/config/installer.php' => config_path('installer.php'),
        ], 'config');
		
        $this->publishes([
            __DIR__.'/Views' => base_path('resources/views/vendor/installer'),
        ], 'views');
		
        $this->publishes([
            __DIR__.'/Lang' => base_path('resources/lang'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/config/installer.php', 'installer'
        );

		$this->registerMiddleware();
		$this->registerAssets();
		$this->registerIfNotInstalled();
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['installer', 'Simexis\Installer\Helpers\Render'];
	}
	
    /**
	 * Register the service provider.
	 *
	 * @return void
	 */
	private function registerProvider() {
		
		$this->app->singleton('installer', function ($app) {
            return new Installer($app);
        });
		
		$this->app->singleton('Simexis\Installer\Helpers\Render', function ($app) {
            return new Render($app);
        });
	}
	
    /**
	 * Register the service provider.
	 *
	 * @return void
	 */
	private function registerAssets() {
		foreach(app('files')->allFiles(__DIR__ . '/Assets') AS $file)
			app('Simexis\Installer\Helpers\Render')->setAssets($file->getPathname());
	}
	
    /**
	 * Register the service provider.
	 *
	 * @return void
	 */
	private function registerMiddleware() { 
		$this->app['router']->middleware('installerCanInstall', '\Simexis\Installer\Middleware\CanInstall');
		$this->app['router']->middleware('installerCanUpdate', '\Simexis\Installer\Middleware\CanUpdate');
	}
	
    /**
	 * Register the service provider.
	 *
	 * @return void
	 */
	private function registerIfNotInstalled() {
		$filemanager = $this->app['installer']->getFileManager();
		include_once __DIR__ . '/Routes/routes.php';
		if(($install = $filemanager->isInstalled()) !== false ? $filemanager->isUpdatable() : true) {
			if(!$this->app['request']->is('Installer@*')) {
				header('location:' . route(!$install ? 'installer::welcome' : 'installer::upgrade'));
				exit;
			}
		}
	}

}
