<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\IPs\Lib;

use FernleafSystems\Utilities\Logic\ExecOnce;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\IPs;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\ModConsumer;
use FernleafSystems\Wordpress\Services\Services;

class ProcessOffenses {

	use ModConsumer;
	use ExecOnce;

	protected function canRun() :bool {
		return !$this->getMod()->isVerifiedBot();
	}

	protected function run() {
		/** @var IPs\ModCon $mod */
		$mod = $this->getMod();

		$mod->loadOffenseTracker()->setIfCommit( true );

		$con = $this->getCon();
		add_filter( $con->prefix( 'firewall_die_message' ), [ $this, 'augmentFirewallDieMessage' ] );
		add_action( $con->prefix( 'pre_plugin_shutdown' ), function () {
			$this->processOffense();
		} );
		add_action( 'shield_security_offense', [ $this, 'processCustomShieldOffense' ], 10, 3 );
	}

	private function processOffense() {
		/** @var IPs\ModCon $mod */
		$mod = $this->getMod();

		$tracker = $mod->loadOffenseTracker();
		if ( !$this->getCon()->plugin_deleting
			 && $tracker->hasVisitorOffended() && $tracker->isCommit() ) {
			( new IPs\Components\ProcessOffense() )
				->setMod( $mod )
				->setIp( Services::IP()->getRequestIp() )
				->run();
		}
	}

	/**
	 * @param array $aMessages
	 * @return array
	 */
	public function augmentFirewallDieMessage( $aMessages ) {
		if ( !is_array( $aMessages ) ) {
			$aMessages = [];
		}

		$aMessages[] = sprintf( '<p>%s</p>', sprintf(
			$this->getMod()->getTextOpt( 'text_remainingtrans' ),
			max( 0, ( new IPs\Components\QueryRemainingOffenses() )
				->setMod( $this->getMod() )
				->setIP( Services::IP()->getRequestIp() )
				->run() )
		) );

		return $aMessages;
	}

	/**
	 * Allows 3rd parties to trigger Shield offenses
	 * @param string $message
	 * @param int    $offenseCount
	 * @param bool   $bIncludeLoggedIn
	 */
	public function processCustomShieldOffense( $message, $offenseCount = 1, $bIncludeLoggedIn = true ) {
		if ( $this->getCon()->isPremiumActive() ) {
			if ( empty( $message ) ) {
				$message = __( 'No custom message provided.', 'wp-simple-firewall' );
			}

			if ( $bIncludeLoggedIn || !did_action( 'init' ) || !Services::WpUsers()->isUserLoggedIn() ) {
				$this->getCon()
					 ->fireEvent(
						 'custom_offense',
						 [
							 'audit'         => [ 'message' => $message ],
							 'offense_count' => (int)$offenseCount
						 ]
					 );
			}
		}
	}
}