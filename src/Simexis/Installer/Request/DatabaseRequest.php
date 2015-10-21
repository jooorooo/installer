<?php

namespace Simexis\Installer\Request;

use Illuminate\Foundation\Http\FormRequest;
use Input;
use Lang;

class DatabaseRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$driver = Input::get('driver');
		if(!in_array($driver, ['sqlite', 'mysql', 'pgsql', 'sqlsrv']))
			return [
				'drivername' => 'required'
			];
		return call_user_func([$this, '_' . ($driver == 'sqlite' ? 'simple' : 'extended') . 'Rules'], $driver);
	}

    /**
     * Set custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
		$driver = Input::get('driver');
		if(!in_array($driver, ['sqlite', 'mysql', 'pgsql', 'sqlsrv']))
			return [
				'drivername.required' => 'There is some error wit driver!'
			];
		return call_user_func([$this, '_' . ($driver == 'sqlite' ? 'simple' : 'extended') . 'Messages'], $driver);
    }
	
	private function _simpleRules($driver) {
		return [
			$driver . '.prefix' => 'alpha_dash'
		];
	}
	
	private function _simpleMessages($driver) {
		return [
			$driver . '.prefix.alpha_dash' => 'Table Prefix may only contain letters, numbers, and dashes.'
		];
	}
	
	private function _extendedRules($driver) {
		$rules = [
			$driver . '.host' => 'required',
			$driver . '.database' => 'required',
			$driver . '.username' => 'required',
		];
		return array_merge($rules, $this->_simpleRules($driver));
	}
	
	private function _extendedMessages($driver) {
		$messages = [
			$driver . '.host.required' => 'Host is required.',
			$driver . '.database.required' => 'Database is required.',
			$driver . '.username.required' => 'Username is required.',
		];
		return array_merge($messages, $this->_simpleMessages($driver));
	}
}