<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\Integrations;

use FernleafSystems\Wordpress\Plugin\Shield\Modules\Base;

class Strings extends Base\Strings {

	/**
	 * @inheritDoc
	 */
	protected function getAuditMessages() :array {
		return [
			'spam_form_pass' => [
				__( '"%s" submission passed SPAM check.', 'wp-simple-firewall' ),
			],
			'spam_form_fail' => [
				__( '"%s" submission failed SPAM check.', 'wp-simple-firewall' )
			],
		];
	}

	/**
	 * @param string $section
	 * @return array
	 * @throws \Exception
	 */
	public function getSectionStrings( string $section ) :array {

		switch ( $section ) {

			case 'section_integrations':
				$titleShort = __( 'Integrations', 'wp-simple-firewall' );
				$title = __( 'Built-In Shield Integrations', 'wp-simple-firewall' );
				$summary = [
					sprintf( '%s - %s', __( 'Summary', 'wp-simple-firewall' ),
						__( "Shield can automatically integrate with 3rd party plugins.", 'wp-simple-firewall' ) ),
					sprintf( '%s - %s', __( 'Recommendation', 'wp-simple-firewall' ),
						__( "Only enable the integrations you require.", 'wp-simple-firewall' ) ),
				];
				break;

			default:
				return parent::getSectionStrings( $section );
		}

		return [
			'title'       => $title,
			'title_short' => $titleShort,
			'summary'     => is_array( $summary ) ? $summary : [],
		];
	}

	/**
	 * @param string $key
	 * @return array
	 * @throws \Exception
	 */
	public function getOptionStrings( string $key ) :array {

		switch ( $key ) {

			case 'enable_mainwp' :
				$name = __( 'MainWP Integration', 'wp-simple-firewall' );
				$summary = __( "Turn-On Shield's Built-In Extension For MainWP Server And Client Installations", 'wp-simple-firewall' );
				$desc = [
					__( 'This is a ShieldPRO-only feature.', 'wp-simple-firewall' ),
					__( 'Easily integrate Shield Security to help you manage your site security from within MainWP.', 'wp-simple-firewall' ),
					__( "You don't need to install a separate extension for MainWP.", 'wp-simple-firewall' ),
					sprintf( '%s: %s', __( 'Important', 'wp-simple-firewall' ),
						__( "If this is a MainWP client site, you should add your MainWP Admin Server's IP address to your IP bypass list.", 'wp-simple-firewall' ) )
				];
				break;

			default:
				return parent::getOptionStrings( $key );
		}

		return [
			'name'        => $name,
			'summary'     => $summary,
			'description' => $desc,
		];
	}
}