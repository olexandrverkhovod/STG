<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard\Scan;

use FernleafSystems\Utilities\Logic\ExecOnce;
use FernleafSystems\Wordpress\Plugin\Shield\Crons\StandardCron;
use FernleafSystems\Wordpress\Plugin\Shield\Databases;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\HackGuard\Options;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\ModConsumer;
use FernleafSystems\Wordpress\Services\Services;

class ScansController {

	use ModConsumer;
	use ExecOnce;
	use StandardCron;

	private $scanCons;

	public function __construct() {
		$this->scanCons = [];
	}

	protected function run() {
		foreach ( $this->getAllScanCons() as $scanCon ) {
			$scanCon->execute();
		}
		$this->setupCron();
		$this->handlePostScanCron();
	}

	/**
	 * @return Controller\Base[]
	 */
	public function getAllScanCons() :array {
		/** @var Options $opts */
		$opts = $this->getOptions();
		foreach ( $opts->getScanSlugs() as $slug ) {
			try {
				$this->getScanCon( $slug );
			}
			catch ( \Exception $e ) {
			}
		}
		return $this->scanCons;
	}

	/**
	 * @return int[] - key is scan slug
	 */
	public function getLastScansAt() :array {
		/** @var Options $opts */
		$opts = $this->getOptions();
		/** @var Databases\Events\Select $oSel */
		$oSel = $this->getCon()
					 ->getModule_Events()
					 ->getDbHandler_Events()
					 ->getQuerySelector();
		$aEvents = $oSel->getLatestForAllEvents();

		$aLatest = [];
		foreach ( $opts->getScanSlugs() as $slug ) {
			$event = $slug.'_scan_run';
			$aLatest[ $slug ] = isset( $aEvents[ $event ] ) ? (int)$aEvents[ $event ]->created_at : 0;
		}
		return $aLatest;
	}

	/**
	 * @param string $slug
	 * @return Controller\Base|mixed
	 * @throws \Exception
	 */
	public function getScanCon( string $slug ) {
		if ( !isset( $this->scanCons[ $slug ] ) ) {
			$class = __NAMESPACE__.'\\Controller\\'.ucwords( $slug );
			if ( @class_exists( $class ) ) {
				/** @var Controller\Base $obj */
				$obj = new $class();
				$this->scanCons[ $slug ] = $obj->setMod( $this->getMod() );
			}
			else {
				throw new \Exception( 'Scan slug does not have a class: '.$slug );
			}
		}
		return $this->scanCons[ $slug ];
	}

	private function handlePostScanCron() {
		add_action( $this->getCon()->prefix( 'post_scan' ), function () {
			$this->runAutoRepair();
		} );
	}

	private function runAutoRepair() {
		/** @var HackGuard\ModCon $mod */
		$mod = $this->getMod();
		/** @var HackGuard\Options $opts */
		$opts = $this->getOptions();
		foreach ( $opts->getScanSlugs() as $sSlug ) {
			$oScanCon = $mod->getScanCon( $sSlug );
			if ( $oScanCon->isCronAutoRepair() ) {
				$oScanCon->runCronAutoRepair();
			}
		}
	}

	/**
	 * Cron callback
	 */
	public function runCron() {
		Services::WpGeneral()->getIfAutoUpdatesInstalled() ? $this->resetCron() : $this->cronScan();
	}

	private function cronScan() {
		/** @var HackGuard\ModCon $mod */
		$mod = $this->getMod();
		/** @var HackGuard\Options $opts */
		$opts = $this->getOptions();

		if ( $this->getCanScansExecute() ) {
			$scans = [];
			foreach ( $opts->getScanSlugs() as $slug ) {
				$scanCon = $mod->getScanCon( $slug );
				if ( $scanCon->isScanningAvailable() && $scanCon->isEnabled() ) {
					$scans[] = $slug;
				}
			}

			$opts->setIsScanCron( true );
			$mod->saveModOptions()
				->getScanQueueController()
				->startScans( $scans );
		}
		else {
			error_log( 'Shield scans cannot execute.' );
		}
	}

	/**
	 * @return string[]
	 */
	public function getReasonsScansCantExecute() :array {
		try {
			$reasons = array_keys( array_filter( [
				'reason_not_call_self' => !$this->getCon()->getModule_Plugin()->canSiteLoopback()
			] ) );
		}
		catch ( \Exception $e ) {
			$reasons = [];
		}
		return $reasons;
	}

	public function getCanScansExecute() :bool {
		return count( $this->getReasonsScansCantExecute() ) === 0;
	}

	protected function getCronFrequency() {
		/** @var HackGuard\Options $opts */
		$opts = $this->getOptions();
		return $opts->getScanFrequency();
	}

	public function getFirstRunTimestamp() :int {

		$startHour = (int)apply_filters( 'shield/scan_cron_start_hour', 3 );
		$startMinute = (int)apply_filters( 'shield/scan_cron_start_minute', (int)rand( 0, 59 ) );
		if ( $startHour < 0 || $startHour > 23 ) {
			$startHour = 3;
		}
		if ( $startMinute < 1 || $startMinute > 59 ) {
			$startMinute = (int)rand( 0, 59 );
		}

		$c = Services::Request()->carbon( true );
		if ( $c->hour > $startHour ) {
			$c->addDays( 1 ); // Start on this hour, tomorrow
		}
		elseif ( $c->hour === $startHour ) {
			if ( $c->minute >= $startMinute ) {
				$c->addDays( 1 ); // Start on this minute, tomorrow
			}
		}

		$c->hour( $startHour )
		  ->minute( $startMinute )
		  ->second( 0 );

		return $c->timestamp;
	}

	protected function getCronName() :string {
		return $this->getCon()->prefix( $this->getOptions()->getDef( 'cron_all_scans' ) );
	}
}