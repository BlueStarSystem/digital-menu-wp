<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Cache manager using wp_options for persistent menu data storage.
 */
class DMWP_Cache {

    private const OPTION_DATA       = 'dmwp_menu_data';
    private const OPTION_ETAG       = 'dmwp_menu_etag';
    private const OPTION_UPDATED_AT = 'dmwp_menu_updated_at';
    private const OPTION_LAST_CHECK = 'dmwp_last_check';

    private DMWP_Api_Client $api;

    public function __construct() {
        $this->api = new DMWP_Api_Client();
    }

    /**
     * Get menu data, refreshing from API if needed.
     *
     * @return array|null Menu data or null if unavailable.
     */
    public function get_menu_data( string $locale = '' ): ?array {
        $local_data = get_option( self::OPTION_DATA );

        // No local data at all — must fetch
        if ( empty( $local_data ) ) {
            return $this->fetch_and_store( $locale );
        }

        // Check if TTL has expired
        if ( ! $this->is_ttl_expired() ) {
            return $local_data;
        }

        // TTL expired — check status via HEAD
        $status = $this->api->status();

        if ( is_wp_error( $status ) ) {
            // API unreachable — use local data
            $this->touch_last_check();
            return $local_data;
        }

        $local_updated_at = get_option( self::OPTION_UPDATED_AT, '' );

        // Compare updated_at timestamps
        if ( $status['updated_at'] === $local_updated_at && $local_updated_at !== '' ) {
            $this->touch_last_check();
            return $local_data;
        }

        // Data changed — fetch with ETag
        $local_etag = get_option( self::OPTION_ETAG, '' );
        $result     = $this->api->index( $locale, $local_etag );

        if ( is_wp_error( $result ) ) {
            $this->touch_last_check();
            return $local_data;
        }

        if ( ! empty( $result['not_modified'] ) ) {
            $this->touch_last_check();
            return $local_data;
        }

        // New data received
        $this->store( $result );

        return $result['data'];
    }

    /**
     * Force a full refresh from the API.
     */
    public function refresh( string $locale = '' ): ?array {
        return $this->fetch_and_store( $locale );
    }

    /**
     * Clear all cached data.
     */
    public function clear(): void {
        delete_option( self::OPTION_DATA );
        delete_option( self::OPTION_ETAG );
        delete_option( self::OPTION_UPDATED_AT );
        delete_option( self::OPTION_LAST_CHECK );
    }

    /**
     * Check if we have any cached data.
     */
    public function has_data(): bool {
        return ! empty( get_option( self::OPTION_DATA ) );
    }

    /**
     * Fetch menu data from API and store it.
     */
    private function fetch_and_store( string $locale ): ?array {
        $result = $this->api->index( $locale );

        if ( is_wp_error( $result ) ) {
            return null;
        }

        if ( ! empty( $result['not_modified'] ) ) {
            $this->touch_last_check();
            return get_option( self::OPTION_DATA );
        }

        $this->store( $result );

        return $result['data'];
    }

    /**
     * Store API response in wp_options.
     */
    private function store( array $result ): void {
        update_option( self::OPTION_DATA, $result['data'], false );
        update_option( self::OPTION_ETAG, trim( $result['etag'] ?? '', '"' ), false );
        update_option( self::OPTION_UPDATED_AT, $result['updated_at'] ?? '', false );
        $this->touch_last_check();
    }

    /**
     * Update the last check timestamp.
     */
    private function touch_last_check(): void {
        update_option( self::OPTION_LAST_CHECK, time(), false );
    }

    /**
     * Check if the cache TTL has expired.
     */
    private function is_ttl_expired(): bool {
        $last_check = (int) get_option( self::OPTION_LAST_CHECK, 0 );

        if ( $last_check === 0 ) {
            return true;
        }

        $settings = get_option( 'dmwp_settings', [] );
        $ttl      = ( (int) ( $settings['cache_ttl'] ?? 15 ) ) * 60; // minutes to seconds

        return ( time() - $last_check ) > $ttl;
    }
}
