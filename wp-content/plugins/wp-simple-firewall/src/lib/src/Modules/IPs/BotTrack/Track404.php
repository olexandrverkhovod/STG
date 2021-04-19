<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\IPs\BotTrack;

class Track404 extends Base {

	const OPT_KEY = 'track_404';

	protected function process() {
		add_action( 'template_redirect', function () {
			if ( is_404() ) {
				$this->doTransgression();
			}
		} );
	}
}
