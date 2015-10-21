<?php

namespace Simexis\Installer\Controllers\Install;

use Illuminate\Routing\Controller;
use Simexis\Installer\Helpers\RequirementsChecker;

class HomeController extends Controller {
	
	public function index(RequirementsChecker $checker) {
		$requirements = $checker->check(
            config('installer.requirements')
        );
        return view('installer::install.home', compact('requirements'));
	}
	
}