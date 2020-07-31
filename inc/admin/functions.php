<?php

function ig_consent_required() {
	return ig_get_setting('consent_required');
}

function ig_get_setting( $name ) {
	$settings = ig_get_settings();

	if( !isset( $settings[$name] ) ) {
		throw new \Exception('Setting do not exist.');
	}

	return $settings[ $name ];
}

function ig_get_settings() {
	$plugin = ignico();
	return $plugin['admin/settings']->get_settings();
}
