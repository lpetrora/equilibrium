<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {button} function plugin
 * Type:     function<br>
 * Name:     make_url<br>
 * Date:     Mar 05, 2016<br>
 * Purpose:  Create a bootstrap button<br>
 * Examples: {button type="button"  class="btn-primary" caption="Enviar" Name="bt" id="bt2"}<br>
 * Output:   <button type="button" class="btn btn-primary" name="bt" id="bt2">Enviar</button>
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
function smarty_function_button($params, $template)
{
	$class = '';
	$type = '';
	$attrs = [
			'name' =>null,
			'id' => null,
			'caption' => null,
			'title' => null,
	];

	foreach ($attrs as $attr => $val)
	{
		$attrs[$attr] = isset($params[$attr])?$params[$attr]:null; 
	}
	$class = isset($params['class'])?$params['class']:'btn-default';
	$type = isset($params['type'])?$params['type']:'button';
	
	$button = '<button type="'.$type.'" class="btn '. $class . '"';
	foreach ($attrs as $attr => $val)
	{
		if ($attr == 'caption') continue;
		if (!empty($val)) $button .= ' ' . $attr . ' = "' . $val . '"'; 
	}
	$button .= '>'.$attrs['caption'].'</button>';
	return $button;
}
