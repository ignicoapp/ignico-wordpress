<?php
/**
 * File provided for custom plugin my account functions
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/woocommerce/myaccount
 */

/**
 * Check if current page is rewards program page
 *
 * @return bool
 */
function ig_is_rewards_program_page() {
	$page_id = ig_get_rewards_program_page_id();

	return ( $page_id && is_page( $page_id ) );
}

/**
 * Get rewards program page ID
 *
 * @return int
 */
function ig_get_rewards_program_page_id() {
	return ig_get_page_id( 'rewards_program' );
}

/**
 * Check if current page is consent page
 *
 * @return bool
 */
function ig_is_consent_page() {
	$page_id = ig_get_consent_page_id();

	return ( $page_id && is_page( $page_id ) );
}

/**
 * Get consent page ID
 *
 * @return int
 */
function ig_get_consent_page_id() {
	return ig_get_page_id( 'consent' );
}

/**
 * Check if current page is coupon page
 *
 * @return bool
 */
function ig_is_coupon_page() {
	$page_id = ig_get_coupon_page_id();

	return ( $page_id && is_page( $page_id ) );
}

/**
 * Get coupon page ID
 *
 * @return int
 */
function ig_get_coupon_page_id() {
	return ig_get_page_id( 'coupon' );
}

/**
 * Check if current page is payout page
 *
 * @return bool
 */
function ig_is_payout_page() {
	$page_id = ig_get_payout_page_id();

	return ( $page_id && is_page( $page_id ) );
}

/**
 * Get payout page ID
 *
 * @return int
 */
function ig_get_payout_page_id() {
	return ig_get_page_id( 'payout' );
}

/**
 * Get ignico page ID by name
 *
 * @param string $page Ignico page name
 *
 * @return int
 */
function ig_get_page_id( $page ) {
	$page = get_option( 'ignico_' . $page . '_page_id' );

	return $page ? absint( $page ) : -1;
}

/**
 * Check if payout withdrawal is available
 *
 * @return bool
 */
function ig_is_payout_available() {
	$plugin = ignico();

	return $plugin['admin/settings']->is_payout_available();
}

/**
 * Check if coupon generation is available
 *
 * @return bool
 */
function ig_is_coupon_available() {
	$plugin = ignico();

	return $plugin['admin/settings']->is_coupon_available();
}
