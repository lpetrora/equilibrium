<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {make_url} function plugin
 * Type:     function<br>
 * Name:     make_url<br>
 * Date:     Mar 05, 2016<br>
 * Purpose:  Create an URL to Controller/method or resource.<br>
 * Examples: {make_url controller="controller\IndexController" method="list" query=['page'=>1]}<br>
 * Output:   http://host.domain/Index/list?page=1
 * Params:
 *
 * @author  Leonardo Petrora for Equilibrium
 * @version 1.0
 *
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 *
 * @throws SmartyException
 * @return string
 */
function smarty_function_make_url($params, $template)
{
	$prefix = \Equilibrium::config()->application->prefix??'/';
	$resource = null;
	$controller = null;
	$method = null;
	$query = '';
	$args = '';
	
	if (isset($params['controller']))
	{
		if (empty($params['controller']))
		{
			$controller = Equilibrium::getCurrentControllerName();
		} else {
			$controller = $params['controller'];
		}
	}
	
	$method = isset($params['method'])?$params['method']:'';
	$resource = isset($params['resource'])?$params['resource']:null;
	$query = isset($params['query'])?'?'.http_build_query($params['query']):'';
	$args = isset($params['arguments'])?'/' . implode('/', $params['arguments']):'';
	
	if ($controller !== null) {
		
	
		$directory = explode('\\',$controller);
		$directory = end($directory);
		$directory = preg_replace('/Controller$/','',$directory);
		$directory = lcfirst($directory);
		
		return $prefix . $directory . '/' . $method . $args . $query;

	} else {
		return $prefix . $resource;
	}
	
}
