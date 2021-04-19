<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\LoginGuard\Lib\TwoFactor\Provider;

use FernleafSystems\Wordpress\Plugin\Shield\Modules;
use FernleafSystems\Wordpress\Services\Services;

abstract class BaseProvider {

	use Modules\ModConsumer;

	const SLUG = '';
	/**
	 * Set to true if this provider can be used in isolation. False if there
	 * must be at least 1 other 2FA provider active.
	 */
	const STANDALONE = true;
	/**
	 * Always a screen, but maybe an json-encoded string, e.g. '[]', like U2F
	 */
	const DEFAULT_SECRET = '';

	public function __construct() {
	}

	public function setupProfile() {
	}

	/**
	 * Assumes this is only called on active profiles
	 * @param \WP_User $user
	 * @return bool
	 */
	public function validateLoginIntent( \WP_User $user ) {
		$bOtpSuccess = false;
		$sReqOtpCode = $this->fetchCodeFromRequest();
		if ( !empty( $sReqOtpCode ) ) {
			$bOtpSuccess = $this->processOtp( $user, $sReqOtpCode );
			$this->postOtpProcessAction( $user, $bOtpSuccess );
		}
		return $bOtpSuccess;
	}

	/**
	 * @param \WP_User $user
	 * @return string|array
	 */
	protected function getSecret( \WP_User $user ) {
		$sSecret = $this->getCon()->getUserMeta( $user )->{static::SLUG.'_secret'};
		return empty( $sSecret ) ? static::DEFAULT_SECRET : $sSecret;
	}

	/**
	 * @param \WP_User $user
	 * @return bool
	 */
	public function hasValidatedProfile( $user ) {
		return $this->getCon()->getUserMeta( $user )->{static::SLUG.'_validated'} === true;
	}

	/**
	 * @param \WP_User $user
	 * @return bool
	 */
	protected function hasValidSecret( \WP_User $user ) {
		return $this->isSecretValid( $this->getSecret( $user ) );
	}

	protected function isEnforced( \WP_User $user ) :bool {
		return false;
	}

	public function isProfileActive( \WP_User $user ) :bool {
		return $this->hasValidSecret( $user );
	}

	public function isProviderAvailableToUser( \WP_User $user ) :bool {
		return $this->isProviderEnabled();
	}

	abstract public function isProviderEnabled() :bool;

	/**
	 * @param string $secret
	 * @return bool
	 */
	protected function isSecretValid( $secret ) {
		return !empty( $secret ) && is_string( $secret );
	}

	/**
	 * @param \WP_User $user
	 * @return $this
	 */
	public function deleteSecret( $user ) {
		$this->getCon()
			 ->getUserMeta( $user )->{static::SLUG.'_secret'} = null;
		return $this;
	}

	/**
	 * @param \WP_User $user
	 * @return string
	 */
	public function resetSecret( \WP_User $user ) {
		$sNewSecret = $this->genNewSecret( $user );
		$this->setSecret( $user, $sNewSecret );
		return $sNewSecret;
	}

	/**
	 * @param \WP_User $user
	 * @param bool     $bValidated set true for validated, false for invalidated
	 * @return $this
	 */
	public function setProfileValidated( $user, $bValidated = true ) {
		$this->getCon()
			 ->getUserMeta( $user )->{static::SLUG.'_validated'} = $bValidated;
		return $this;
	}

	/**
	 * @param \WP_User     $user
	 * @param string|array $sNewSecret
	 * @return $this
	 */
	protected function setSecret( $user, $sNewSecret ) {
		$this->getCon()
			 ->getUserMeta( $user )->{static::SLUG.'_secret'} = $sNewSecret;
		return $this;
	}

	/**
	 * @param \WP_User $user
	 * @return string|mixed
	 */
	protected function genNewSecret( \WP_User $user ) {
		return '';
	}

	abstract protected function processOtp( \WP_User $user, string $otp ) :bool;

	/**
	 * Only to be fired if and when Login has been completely verified.
	 * @param \WP_User $user
	 * @return $this
	 */
	public function postSuccessActions( \WP_User $user ) {
		return $this;
	}

	/**
	 * This MUST only ever be hooked into when the User is looking at their OWN profile, so we can use "current user"
	 * functions.  Otherwise we need to be careful of mixing up users.
	 * @param \WP_User $user
	 * @return string
	 */
	public function renderUserProfileOptions( \WP_User $user ) :string {
		return '';
	}

	/**
	 * ONLY TO BE HOOKED TO USER PROFILE EDIT
	 * @param \WP_User $user
	 * @return string
	 */
	public function renderUserEditProfileOptions( \WP_User $user ) {
		return $this->renderUserProfileOptions( $user );
	}

	/**
	 * @param \WP_User $user
	 */
	public function handleEditOtherUserProfileSubmit( \WP_User $user ) {
	}

	/**
	 * @param \WP_User $user
	 */
	protected function processRemovalFromAccount( \WP_User $user ) {
	}

	/**
	 * This MUST only ever be hooked into when the User is looking at their OWN profile,
	 * so we can use "current user" functions.  Otherwise we need to be careful of mixing up users.
	 * @param \WP_User $user
	 */
	public function handleUserProfileSubmit( \WP_User $user ) {
	}

	public function captureLoginAttempt( \WP_User $user ) {
	}

	/**
	 * @return array
	 */
	public function getFormField() {
		return [];
	}

	abstract protected function auditLogin( \WP_User $user, bool $bIsSuccess );

	/**
	 * @param \WP_User $user
	 * @param bool     $bIsOtpSuccess
	 * @return $this
	 */
	protected function postOtpProcessAction( \WP_User $user, bool $bIsOtpSuccess ) {
		$this->auditLogin( $user, $bIsOtpSuccess );
		return $this;
	}

	/**
	 * @return string
	 */
	protected function getLoginFormParameter() {
		return $this->getCon()->prefixOption( static::SLUG.'_otp' );
	}

	/**
	 * @return string
	 */
	protected function fetchCodeFromRequest() {
		return trim( Services::Request()->request( $this->getLoginFormParameter(), false, '' ) );
	}

	/**
	 * @param \WP_User $user
	 * @return array
	 */
	protected function getCommonData( \WP_User $user ) {
		return [
			'flags'   => [
				'has_validated_profile' => $this->hasValidatedProfile( $user ),
				'is_enforced'           => $this->isEnforced( $user ),
				'is_profile_active'     => $this->isProfileActive( $user ),
				'is_my_user_profile'    => $user->ID == Services::WpUsers()->getCurrentWpUserId(),
				'i_am_valid_admin'      => $this->getCon()->isPluginAdmin(),
				'user_to_edit_is_admin' => Services::WpUsers()->isUserAdmin( $user ),
			],
			'vars'    => [
				'otp_field_name' => $this->getLoginFormParameter(),
			],
			'strings' => [
				'is_enforced' => __( 'This setting is enforced by your security administrator.', 'wp-simple-firewall' ),
			],
		];
	}
}