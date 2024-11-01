<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;
function emd_calculate_if($check,$firstval,$secondval){
	if($check){
		return $firstval;
	}
	else {
		return $secondval;
	}
}
function emd_calculate_concat($params){
	return implode("",$params);
}
