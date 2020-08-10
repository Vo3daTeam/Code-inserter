<?php

function vo3da_table_exists( string $table_name ) {

}

/**
 * Returns the domains of the current site
 *
 * @param int $site_id Current site id.
 *
 * @return array
 */
function vo3da_get_mirrors( int $site_id ): array {
	$mirrors = false;
	if ( false === $mirrors ) {
		global $wpdb;
		$domain_mapping_table = wp_cache_get( 'vo3da_domain_mapping_table' );
		if ( false === $domain_mapping_table ) {
			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
			$domain_mapping_table = $wpdb->get_var(
				$wpdb->prepare(
					'SHOW TABLES LIKE %s',
					$wpdb->dmtable
				)
			);
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery.
			wp_cache_add( 'vo3da_domain_mapping_table', $domain_mapping_table );
		}

		if ( ! empty( $domain_mapping_table ) && 'wp_domain_mapping' === $domain_mapping_table ) {

			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery

			$domains = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT DISTINCT REPLACE( domain, 'www.', '' ) as domain FROM $wpdb->dmtable WHERE blog_id = %d ORDER BY domain ASC",
					$site_id
				)
			);
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery.
			$mirrors = [];
			if ( ! empty( $domains ) ) {
				foreach ( $domains as $domain ) {
					if ( file_exists( NGINX_DIR . $site_id . '/' . $domain->domain . '.conf' ) ) {
						array_push( $mirrors, $domain->domain );
					}
				}
			}

			wp_cache_add( 'vo3da_site_mirrors', $mirrors );
		} else {
			$protocols = [ 'http://', 'https://' ];
			$site_url  = get_site_url( get_current_blog_id() );
			$mirrors[] = str_replace( $protocols, '', $site_url );
			wp_cache_add( 'vo3da_site_mirrors', $mirrors, 'https' );
		}
	}

	return $mirrors;
}