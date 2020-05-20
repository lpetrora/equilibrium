<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {csrf_token} function plugin
 * Type:     function<br>
 * Name:     csrf_token<br>
 * Date:     Mar 05, 2016<br>
 * Purpose:  Write CSRF token as hidden input field.<br>
 * Examples: {csrf_token}<br>
 * Output:   <input type="hidden" name="<name>" value="<value>">
 * Params:
 * 			(optional) output: 'json', 'html'
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
function smarty_function_csrf_token($params, $template)
{
	$output = isset($params['output'])?$params['output']:'html';
	$token = $template->getTemplateVars('__CSRF__');
	if ($token == null ) throw new SmartyException ("csrf_token: Missing CSRF token variable (__CSRF__)", E_USER_ERROR);
	$name = isset ($token['name'])?$token['name']:null;
	$value = isset ($token['value'])?$token['value']:null;
	
	if (($name == null)||($value==null) ) throw new SmartyException ("csrf_token: Missing CSRF token name or value", E_USER_ERROR);

	switch ($output) {
		case 'json':
			$a = json_encode([$name => $value]);
			return $a;
		break;
		
		case 'json_field':
			$a = json_encode([$name => $value]);
			return substr($a, 1,-1);
		break;
		
		case 'url':
			return ("$name=$value");
			break;
			
		case 'html':
		default:
			return '<input type="hidden" name="'.$name.'" value="'.$value.'">';
		break;
	}
}
