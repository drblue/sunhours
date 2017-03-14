#!/usr/bin/php
<?php

define('DEBUG', false);
define('DT_FORMAT', "%Y-%m-%d %H:%M:%S");
define('D_FORMAT', "%Y-%m-%d");
define('LINE_BREAK', "\n");
$config = array(
		'lat' => 55.717769,
		'long' => 13.234321,
		'zenit' => 90,
		'offset' => 1
	);

calc_sun(2011);
calc_sun(2012);
calc_sun(2013);
calc_sun(2014);
calc_sun(2015);

function calc_sun($year) {
	global $config;
	printf("Running calculations for %d.." . LINE_BREAK, $year);

	$sun_hours = 0;
	$date = strtotime("{$year}-01-01");

	$exec_start = microtime(true);
	for ($days = 1; $days <= 365; $days++) {
		$date = strtotime("+1 day", $date);
		$sun_hours += get_sun($date);
	}
	$exec_end = microtime(true);
	$exec_time_ms = ($exec_end-$exec_start)*1000;

	printf("%d had/will have %f hours of sun at [%f, %f], and it took me %f ms to calculate." . LINE_BREAK, $year, round($sun_hours, 3), $config['lat'], $config['long'], round($exec_time_ms, 3));
	
}

function measure_sun($date) {
	$start = microtime(true);
	$sun = get_sun($date);
	$end = microtime(true);
	$calc = ($end-$start)*1000;
	printf("%s had %f hours of sun and it took me %f ms to calculate." . LINE_BREAK,
		strftime(D_FORMAT, $date),
		round($sun, 3),
		round($calc, 3)
		);
}

function get_sun($date) {
	global $config;
	if (DEBUG) output(strftime(DT_FORMAT, $date));
	$sunrise = date_sunrise($date, SUNFUNCS_RET_STRING, $config['lat'], $config['long'], $config['zenit'], $config['offset']);
	$sunset = date_sunset($date, SUNFUNCS_RET_STRING, $config['lat'], $config['long'], $config['zenit'], $config['offset']);
	$dt_sunrise = new DateTime(strftime(D_FORMAT, $date) . " " . $sunrise);
	$dt_sunset = new DateTime(strftime(D_FORMAT, $date) . " " . $sunset);
	if (DEBUG) output("Sunrise: ");
	if (DEBUG) var_dump($dt_sunrise);
	if (DEBUG) output("Sunset: ");
	if (DEBUG) var_dump($dt_sunset);
	$diff = $dt_sunrise->diff($dt_sunset);
	if (DEBUG) var_dump($diff);
	$sun = $diff->h + ($diff->i/60);
	if (DEBUG) var_dump($sun);
	return $sun;
}

function output($str) {
	echo "{$str}\n";
}
