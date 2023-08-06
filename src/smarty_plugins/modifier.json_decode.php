<?php

function smarty_modifier_json_decode(string $str) {
	return json_decode($str,true);
}