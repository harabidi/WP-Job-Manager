<?php

class WP_Test_WPJM_REST_API_Settings extends WPJM_REST_TestCase {

    /**
     * @var int
     */
    private $admin_id;
    /**
     * @var int
     */
    private $default_user_id;

    function setUp() {
        parent::setUp();
        $admin = get_user_by( 'email', 'rest_api_admin_user@test.com' );
        if ( false === $admin ){
            $this->admin_id = wp_create_user(
                'rest_api_admin_user',
                'rest_api_admin_user',
                'rest_api_admin_user@test.com' );
            $admin = get_user_by( 'ID', $this->admin_id );
            $admin->set_role( 'administrator' );
        }

        $this->default_user_id = get_current_user_id();
        $this->login_as_admin();
    }

    function login_as_admin() {
        return $this->login_as( $this->admin_id );
    }

    function login_as( $user_id ) {
        wp_set_current_user( $user_id );
        return $this;
    }

    function test_responds_when_no_sufficient_permissions() {
        $this->login_as( $this->default_user_id );
        $response = $this->get( '/wpjm/v1/settings' );
        $this->assertResponseStatus( $response, 403 );
    }

    function test_get_response_status_success() {
        $response = $this->get( '/wpjm/v1/settings' );
        $this->assertResponseStatus( $response, 200 );
    }

    function test_post_response_status_created() {
        $settings = $this->get_settings();

        $previous_setting = $settings->get( 'job_manager_per_page' );

        $new_settings = array(
            'per_page' => $previous_setting + 1
        );

        $response = $this->post( '/wpjm/v1/settings', $new_settings );
        $this->assertResponseStatus( $response, 201 );
    }

    function test_put_response_status_success() {
        $settings = $this->get_settings();

        $previous_setting = $settings->get( 'job_manager_per_page' );

        $new_settings = array(
            'per_page' => $previous_setting + 1
        );

        $response = $this->put( '/wpjm/v1/settings', $new_settings );
        $this->assertResponseStatus( $response, 200 );
    }

    function test_post_updates_settings() {
        $settings = $this->get_settings();

        $previous_setting = $settings->get( 'job_manager_per_page' );

        $new_settings = array(
            'per_page' => $previous_setting + 1
        );

        $this->post( '/wpjm/v1/settings', $new_settings );

        $response = $this->get( '/wpjm/v1/settings' );
        $data = $response->get_data();
        $this->assertArrayHasKey( 'per_page', $data );
        $this->assertEquals( $previous_setting + 1, $data['per_page'] );
    }

    function test_put_updates_settings() {
        $settings = $this->get_settings();

        $previous_setting = $settings->get( 'job_manager_per_page' );

        $new_settings = array(
            'per_page' => $previous_setting + 1
        );

        $this->put( '/wpjm/v1/settings', $new_settings );

        $response = $this->get( '/wpjm/v1/settings' );
        $data = $response->get_data();
        $this->assertArrayHasKey( 'per_page', $data );
        $this->assertEquals( $previous_setting + 1, $data['per_page'] );
    }

    function test_put_validation_error_bad_request_no_setting_change() {
        $settings = $this->get_settings();

        $previous_setting = $settings->get( 'job_manager_job_dashboard_page_id' );

        $new_settings = array(
            'job_dashboard_page_id' => -1
        );

        $response = $this->put( '/wpjm/v1/settings', $new_settings );
        $this->assertResponseStatus( $response, 400 );

        $response = $this->get( '/wpjm/v1/settings' );
        $data = $response->get_data();
        $this->assertArrayHasKey( 'job_dashboard_page_id', $data );
        $this->assertEquals( $previous_setting, $data['job_dashboard_page_id'] );
    }

    function test_delete_not_found() {
        $response = $this->delete( '/wpjm/v1/settings' );
        $this->assertResponseStatus( $response, 404 );
    }

    private function get_settings() {
        return $this->environment()
            ->get()
            ->model( WP_Job_Manager_Models_Settings::class )
            ->get_data_store()->get_entity(-1);
    }
}