<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * API client for the Menu API.
 */
class DMWP_Api_Client {

    private string $base_url;
    private string $token;

    public function __construct( string $base_url = '', string $token = '' ) {
        if ( $base_url === '' || $token === '' ) {
            $settings    = get_option( 'dmwp_settings', [] );
            $this->base_url = rtrim( $base_url ?: ( $settings['api_url'] ?? '' ), '/' );
            $this->token    = $token ?: ( $settings['api_token'] ?? '' );
        } else {
            $this->base_url = rtrim( $base_url, '/' );
            $this->token    = $token;
        }
    }

    /**
     * HEAD /api/v1/menu/status — check if menus have been updated.
     *
     * @return array{etag: string, updated_at: string}|WP_Error
     */
    public function status(): array|\WP_Error {
        $response = $this->request( 'HEAD', '/v1/menu/status' );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return [
            'etag'       => wp_remote_retrieve_header( $response, 'etag' ),
            'updated_at' => wp_remote_retrieve_header( $response, 'x-menu-updated-at' ),
        ];
    }

    /**
     * GET /api/v1/menu — fetch all menus.
     *
     * @return array|WP_Error
     */
    public function index( string $locale = '', string $etag = '' ): array|\WP_Error {
        $headers = [];

        if ( $locale !== '' ) {
            $headers['Accept-Language'] = $locale;
        }

        if ( $etag !== '' ) {
            $headers['If-None-Match'] = $etag;
        }

        $response = $this->request( 'GET', '/v1/menu', $headers );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );

        if ( $code === 304 ) {
            return [
                'not_modified' => true,
                'etag'         => wp_remote_retrieve_header( $response, 'etag' ),
            ];
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new \WP_Error( 'dmwp_json_error', __( 'Invalid JSON response from API.', 'digital-menu-wp' ) );
        }

        return [
            'not_modified' => false,
            'data'         => $data,
            'etag'         => wp_remote_retrieve_header( $response, 'etag' ),
            'updated_at'   => wp_remote_retrieve_header( $response, 'x-menu-updated-at' ),
        ];
    }

    /**
     * GET /api/v1/menu/{id} — fetch a single menu.
     *
     * @return array|WP_Error
     */
    public function show( int $menu_id, string $locale = '' ): array|\WP_Error {
        $headers = [];

        if ( $locale !== '' ) {
            $headers['Accept-Language'] = $locale;
        }

        $response = $this->request( 'GET', '/v1/menu/' . $menu_id, $headers );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new \WP_Error( 'dmwp_json_error', __( 'Invalid JSON response from API.', 'digital-menu-wp' ) );
        }

        return $data;
    }

    /**
     * Test the connection — returns store info or WP_Error.
     *
     * @return array|WP_Error
     */
    public function test(): array|\WP_Error {
        $result = $this->index();

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        if ( ! empty( $result['not_modified'] ) ) {
            return [ 'connected' => true, 'message' => __( 'Connection OK (data unchanged).', 'digital-menu-wp' ) ];
        }

        $data = $result['data'] ?? [];

        return [
            'connected'  => true,
            'store_name' => $data['store']['name'] ?? '',
            'menus'      => array_map( fn( $m ) => [
                'id'   => $m['id'],
                'name' => $m['name'],
            ], $data['menus'] ?? [] ),
        ];
    }

    /**
     * Make an HTTP request to the API.
     */
    private function request( string $method, string $endpoint, array $headers = [] ): array|\WP_Error {
        if ( $this->base_url === '' || $this->token === '' ) {
            return new \WP_Error( 'dmwp_not_configured', __( 'API URL or token not configured.', 'digital-menu-wp' ) );
        }

        $url = $this->base_url . $endpoint;

        $args = [
            'method'    => $method,
            'timeout'   => 15,
            'sslverify' => ! $this->is_local_url( $url ),
            'headers'   => array_merge( [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept'        => 'application/json',
            ], $headers ),
        ];

        $response = wp_remote_request( $url, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );

        if ( $code === 401 ) {
            return new \WP_Error( 'dmwp_unauthorized', __( 'Invalid or expired API token.', 'digital-menu-wp' ) );
        }

        if ( $code === 403 ) {
            return new \WP_Error( 'dmwp_forbidden', __( 'API access not available for this store.', 'digital-menu-wp' ) );
        }

        if ( $code >= 500 ) {
            return new \WP_Error( 'dmwp_server_error', __( 'API server error. Please try again later.', 'digital-menu-wp' ) );
        }

        return $response;
    }

    /**
     * Check if the URL points to a local development environment.
     */
    private function is_local_url( string $url ): bool {
        $host = wp_parse_url( $url, PHP_URL_HOST );

        if ( ! $host ) {
            return false;
        }

        return str_ends_with( $host, '.test' )
            || str_ends_with( $host, '.local' )
            || str_ends_with( $host, '.localhost' )
            || $host === 'localhost'
            || $host === '127.0.0.1';
    }
}
