<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Class WP_Job_Manager_REST_API_V1
 * @package rest-api
 */
class WP_Job_Manager_REST_API_V1 extends Mixtape_Rest_Api_Controller_Bundle {
    protected $api_prefix = 'wpjm/v1';

    public function get_endpoints() {
        return array(
            new WP_Job_Manager_Rest_Api_Endpoint_Version( $this ),
        );
    }
}