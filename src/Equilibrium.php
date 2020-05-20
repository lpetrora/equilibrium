<?php
use equilibrium\Config;
use equilibrium\Controller;
use equilibrium\Request;
use equilibrium\Session;
use equilibrium\IUserHandler;
use equilibrium\FileWrapper;
use equilibrium\Log;

class Equilibrium
{
	//Manejadores de requests
	private static $_post = null;
	private static $_get = null;
	private static $_server = null;
	private static $_session = null;
	private static $_config = null;
	private static $_user = null;
	private static $_files = null;
	private static $_log = null;
	
	//Ruta a la aplicación
	public static $appPath = '';
	
	public static $prefix = '/';
	
	//Ruta al directorio de equilibrium
	public static $equilibriumPath = '';
	
	//Controlador al que se ha llamado
	public static $currentController = null;
	
	//Sesion con cosas de Equilibrium
	private static $_eq_session;
	
	/**
	 * Returns access to configuration
	 * @return Config
	 */
	static public function config()
	{
		if (static::$_config == null) static::$_config = new Config();
		return static::$_config;
	}
	
	/**
	 * Returns access to POST variables
	 * @return \equilibrium\Request
	 */
	static public function post()
	{
		if(static::$_post == null) static::$_post = new Request($_POST, static::$currentController->checkCSRF, '-');
		return static::$_post;
	}
	
	/**
	 * Returns access to SERVER variables
	 * @return \equilibrium\Request
	 */
	static public function server()
	{
		if (static::$_server == null) static::$_server = new Request($_SERVER, false);
		return static::$_server;
	}
	
	/**
	 * Returns access to GET variables
	 * @return \equilibrium\Request
	 */
	static public function get()
	{
		if(static::$_get == null) static::$_get = new Request($_GET, false, '-');
		return static::$_get;
	}
	
	/**
	 * Returns access to SESSION variables
	 * @return equilibrium\Session;
	 */
	static public function session()
	{
		if (static::$_session == null) static::$_session = new Session('APP');
		return static::$_session;
	}
	
	/**
	 * Return access to Files uploaded by form
	 * @return equilibrium\FileWrapper
	 */
	static public function files()
	{
		if (static::$_files == null) static::$_files = new FileWrapper('-');
		return static::$_files;
	}
	
	/**
	 * Return access to log facilities
	 * @return equilibrium\Log
	 */
	static public function log()
	{
		if (static::$_log == null) static::$_log = new Log();
		return static::$_log;
	}
	
	/**
	 * Converts string to camelcase. First letter is upper case.
	 * @param string $str
	 * @return string
	 */
	static public function toUpperCamelcase ($str)
	{
		return ucfirst(static::toLowerCamelcase($str));
	}
	
	/**
	 * Converts string to camelcase. First letter is lower case.
	 * @param string $str
	 * @return string
	 */
	static public function toLowerCamelcase ($str)
	{
		return preg_replace_callback('/_([a-z])/',
				function($c)
				{
					return strtoupper($c[1]);
				}
				, $str);
	}
	
	/**
	 * Converts camelcase string to underscore separated string
	 * @param string $str
	 * @return string
	 */
	static public function toUnderscored ($str)
	{
		$str[0] = strtolower($str[0]);
		return preg_replace_callback('/([A-Z])/',
				function ($c)
				{
					return "_" . strtolower($c[1]);
				}
				, $str);
	}
	
	/**
	 * Converts array to StdClass instance
	 * @param array $array
	 * @return StdClass
	 */
	static public function arrayToObject($array)
	{
		if (is_array($array)) {
			return (object) array_map(array('Equilibrium', 'arrayToObject'), $array);
		}
		else {
			return $array;
		}				
	}
	
	/**
	 * Returns access to Equlibrium SESSION variabless
	 * @return equilibrium\Session
	 */
	public static function EquilibriumSession()
	{
		if (static::$_eq_session == null) static::$_eq_session = new Session('EQ');
		return static::$_eq_session;
	}
	
	/**
	 * Returns an array containgin name and value of CSRF token
	 * @return array 
	 */
	static public function getCSRFToken()
	{
		$session = static::EquilibriumSession();
		$token = [];
		if (! isset($session->__CSRF__))
		{
			$token = [
					'name' => 'chuck_'.sha1(rand()),
					'value' => hash('sha256',rand().rand().rand().rand())
				];
			$session->__CSRF__ = $token;
			
		} else {
			$token = $session->__CSRF__;
		}
		return $token;
	}
	
	/**
	 * Returns access to current user
	 * @return IUserHandler
	 */
	static public function user()
	{
		if (static::$_user == null)
		{
			$class = isset(Equilibrium::config()->application->userHandler)?Equilibrium::config()->application->userHandler:'equilibrium\User';
			static::$_user = new $class(); 
		}
		return static::$_user;			
	}
	
	static public function getCurrentControllerName()
	{
		$result = null;
		if (static::$currentController != null)
		{
			$result = get_class(static::$currentController);				
		}
		return $result;
	}
	
	static public function makeControllerUrl($controller=null, $method=null, $args=[], $query=[]) {
		
		$ctrl = null;
		$directory = null;
		if (empty($controller)) {
			$ctrl = static::getCurrentControllerName();
			
		} else {
			if (is_string($controller)) {
				$ctrl = $controller;
				
			} else {
				$ctrl = get_class($controller);
			}			
		}
		$directory = explode('\\',$ctrl);
		$directory = end($directory);
		$directory = preg_replace('/Controller$/','',$directory);
		$directory = lcfirst($directory);
		
		$method = empty($method)?'': self::toLowerCamelcase(preg_replace('/^action/','',$method));
		$args = empty($args)?'':implode('/',$args);
		$query = empty($query)?'':http_build_query($query);
		
		return static::makeUrl($directory . '/' . $method . $args . $query);
	}
	
	static public function makeResourceUrl($resource) {
		return static::makeUrl($resource);
	}
	
	static protected function makeUrl ($string) {
		$prefix = trim( (\Equilibrium::config()->application->prefix??'/') );
		if (substr($prefix, -strlen($prefix)) !== '/') $prefix .='/';
		return $prefix . $string;
	}
}