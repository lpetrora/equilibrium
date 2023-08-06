<?php

function smarty_modifier_json_encode(mixed $obj) {
	return json_encode($obj);
}