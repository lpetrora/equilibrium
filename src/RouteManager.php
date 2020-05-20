<?php
namespace equilibrium;
use Equilibrium;

class RouteManager
{
	protected static $routes = [];
	protected static $controllerNamespace = 'app\controllers\\';
	
	protected static function parse($parts)
	{
		$controller = Equilibrium::toUpperCamelcase(array_shift($parts));
		$controller = empty($controller)?'IndexController':$controller.'Controller';
		$fallback = $parts;
		$action = Equilibrium::toUpperCamelcase(array_shift($parts));
		$route = static::setRoute($controller, $action, $parts);
		$route ['fallbackParams'] = $fallback;
		$route ['canFallBack'] = true;
		return $route;
	}
	
	protected static function setRoute($controller, $action, $params = [])
	{
		$result = [];
		$result['namespace'] = 'app\controllers\\';
		$result['class'] = $controller;
		$result['action'] = Equilibrium::toUpperCamelcase($action);
		$result['params'] = $params;
		$result['action'] = (empty($result['action']))? 'actionIndex' : 'action'.$result['action'];
		$result['canFallBack'] = false;
		return $result;
	}
	
	static public function makeRoute ($_http)
	{
		if ((is_readable(Equilibrium::$appPath.'/config/routing.php')) && (static::$routes == [])) 
			static::$routes = include Equilibrium::$appPath.'/config/routing.php';

		$request = explode('?',$_SERVER['REQUEST_URI']);
		$request = $request[0];
		
		$route = null;
		if (isset(static::$routes[$request]))
		{
			$route = static::$routes[$request];
			$route = static::setRoute($route['controller'], $route['action']);
		} else {
			
			$request = (substr($request, 0, strlen($_http)) == $_http)?substr($request, strlen($_http)):$request;
			$route = ($request == '/')?'index':$request;
			$route = trim($route, '/');
			$parts = explode('/', $route);
			$route = static::parse($parts);
		}
		
		return $route;
	}
	/* ****************************************************************************************************** */
	/**
	 * Returns an array with namespace, class, method and params information
	 * @param string $controller	Must be a valid controller name, for example, FooController
	 * @param string $action		Action name without 'action' prefix
	 * @param array $params
	 */
	protected static function buildRoute($controller = '', $action = '', $params = [])
	{
		$result = [];
		$result ['namespace'] = static::$controllerNamespace;
		$result ['class'] = empty($controller)?'IndexController':$controller;
		$result ['method'] = empty($action)? 'actionIndex' : 'action'.Equilibrium::toUpperCamelcase($action);
		$result ['params'] = $params;
		return $result;
	}
	
	/**
	 * Load static routing definitions
	 */
	protected static function loadStaticRoutes()
	{
		$file = Equilibrium::$appPath.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'routing.php';
		if (empty(static::$routes) && is_readable($file)) static::$routes = include $file;
	}
	
	/**
	 * Takes HTTP request and returns array with routing information
	 * @param string $http
	 * @return array
	 */
	static public function makeRoutes($http)
	{
		$routes = []; // Array of ['namespace', 'class', 'method', 'params'];
		static::loadStaticRoutes();
		
		//Remove query part
		$request = explode('?',$_SERVER['REQUEST_URI']);
		$request = $request[0];
		
		//TODO: Advanced static routing maches (like wildcards)
		if (isset(static::$routes[$request]))
		{
			//Static route
			$route = static::$routes[$request];
			$routes[] = static::buildRoute($route['controller'], $route['action'], explode('/', $request));
			
		} else {
			//Dynamic route
			
		}
		
		return $routes;
	}
}