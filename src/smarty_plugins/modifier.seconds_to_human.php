<?php
/**
 * @copyright 2010 MAPIX Technologies Ltd, UK, http://mapix.com/
 * @license http://en.wikipedia.org/wiki/BSD_licenses  BSD License
 * @package Smarty
 * @subpackage PluginsModifier
 */


function smarty_modifier_seconds_to_human($seconds) {
	if ($seconds < 0) throw new Exception("Can't do negative numbers!");
	if ($seconds == 0) return "0 segundos";

	$dtF = new \DateTime('@0');
	$dtT = new \DateTime("@$seconds");
	$time = $dtF->diff($dtT)->format('%a,%h,%i,%s');
	$time = explode(',', $time);
	$out = [];

	if($time[0] ==1) $out[] = '1 día';
	if($time[0] >1) $out[] = $time[0] .' días';
	if($time[1] ==1) $out[] = '1 hora';
	if($time[1] >1) $out[] = $time[1] .' horas';
	if($time[2] ==1) $out[] = '1 minuto';
	if($time[2] >1) $out[] = $time[2] .' minutos';
	if($time[3] ==1) $out[] = '1 segundo';
	if($time[3] >1) $out[] = $time[1] .' segundos';
	
	
	
	return trim(implode(', ',$out));
}