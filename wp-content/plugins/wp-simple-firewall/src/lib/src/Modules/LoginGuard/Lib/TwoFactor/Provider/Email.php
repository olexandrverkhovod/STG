<?php

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\LoginGuard\Lib\TwoFactor\Provider;

use FernleafSystems\Wordpress\Plugin\Shield\Modules\LoginGuard;
use FernleafSystems\Wordpress\Plugin\Shield\ShieldNetApi\SureSend\SendEmail;
use FernleafSystems\Wordpress\Services\Services;

class Email extends BaseProvider {

	const SLUG = 'email';

	private $secretToDelete = '';

	public function captureLoginAttempt( \WP_User $user ) {
		$this->sendEmailTwoFactorVerify( $user );
	}

	/**
	 * @param \WP_User $user
	 * @param bool     $bIsSuccess
	 */
	protected function auditLogin( \WP_User $user, bool $bIsSuccess ) {
		$this->getCon()->fireEvent(
			$bIsSuccess ? 'email_verified' : 'email_fail',
			[
				'audit' => [
					'user_login' => $user->user_login,
					'method'     => 'Email',
				]
			]
		);
	}

	/**
	 * @param \WP_User $user
	 * @return $this
	 */
	public function postSuccessActions( \WP_User $user ) {
		if ( !empty( $this->secretToDelete ) ) {
			$secrets = $this->getAllCodes( $user );
			unset( $secrets[ $this->secretToDelete ] );
			$this->storeCodes( $user, $secrets );
		}
		return $this;
	}

	protected function processOtp( \WP_User $user, string $otp ) :bool {
		$valid = false;
		foreach ( $this->getAllCodes( $user ) as $secret => $expiresAt ) {
			if ( wp_check_password( $otp, $secret ) ) {
				$valid = true;
				$this->secretToDelete = $secret;
				break;
			}
		}
		return $valid;
	}

	/**
	 * @return array
	 */
	public function getFormField() {
		return [
			'name'        => $this->getLoginFormParameter(),
			'type'        => 'text',
			'value'       => $this->fetchCodeFromRequest(),
			'placeholder' => __( 'This code was just sent to your registered Email address.', 'wp-simple-firewall' ),
			'text'        => __( 'Email OTP', 'wp-simple-firewall' ),
			'help_link'   => 'https://shsec.io/3t'
		];
	}

	/**
	 * @inheritDoc
	 */
	public function handleUserProfileSubmit( \WP_User $user ) {

		$bWasEnabled = $this->isProfileActive( $user );
		$bToEnable = Services::Request()->post( 'shield_enable_mfaemail' ) === 'Y';

		$sMsg = null;
		$bError = false;
		if ( $bToEnable ) {
			$this->setProfileValidated( $user );
			if ( !$bWasEnabled ) {
				$sMsg = __( 'Email Two-Factor Authentication has been enabled.', 'wp-simple-firewall' );
			}
		}
		elseif ( $this->isEnforced( $user ) ) {
			$sMsg = __( "Email Two-Factor Authentication couldn't be disabled because it is enforced based on your user roles.", 'wp-simple-firewall' );
			$bError = true;
		}
		else {
			$this->setProfileValidated( $user, false );
			$sMsg = __( 'Email Two-Factor Authentication has been disabled.', 'wp-simple-firewall' );
		}

		if ( !empty( $sMsg ) ) {
			$this->getMod()->setFlashAdminNotice( $sMsg, $bError );
		}
	}

	public function isProfileActive( \WP_User $user ) :bool {
		/** @var LoginGuard\Options $opts */
		$opts = $this->getOptions();
		return parent::isProfileActive( $user ) &&
			   ( $this->isEnforced( $user ) ||
				 ( $this->hasValidatedProfile( $user ) && $opts->isEnabledEmailAuthAnyUserSet() ) );
	}

	protected function isEnforced( \WP_User $user ) :bool {
		/** @var LoginGuard\Options $opts */
		$opts = $this->getOptions();
		return count( array_intersect( $opts->getEmail2FaRoles(), $user->roles ) ) > 0;
	}

	/**
	 * @param string $secret
	 * @return bool
	 */
	protected function isSecretValid( $secret ) {
		return true;
	}

	/**
	 * @param \WP_User $user
	 * @return $this
	 */
	private function sendEmailTwoFactorVerify( \WP_User $user ) {
		$sureCon = $this->getCon()->getModule_Comms()->getSureSendController();
		$useSureSend = $sureCon->isEnabled2Fa() && $sureCon->canUserSend( $user );

		try {
			$code = $this->genNewCode( $user );

			$sendSuccess = ( $useSureSend && $this->send2faEmailSureSend( $user, $code ) )
						   || $this->getMod()
								   ->getEmailProcessor()
								   ->sendEmailWithTemplate(
									   '/email/lp_2fa_email_code',
									   $user->user_email,
									   __( 'Two-Factor Login Verification', 'wp-simple-firewall' ),
									   [
										   'flags'   => [
											   'show_login_link' => !$this->getCon()->isRelabelled()
										   ],
										   'vars'    => [
											   'code' => $code
										   ],
										   'hrefs'   => [
											   'login_link' => 'https://shsec.io/96',
											   'verify_2fa' => $this->genLoginLink( $user, $code )
										   ],
										   'strings' => [
											   'someone'          => __( 'Someone attempted to login into this WordPress site using your account.', 'wp-simple-firewall' ),
											   'requires'         => __( 'Login requires verification with the following code.', 'wp-simple-firewall' ),
											   'verification'     => __( 'Verification Code', 'wp-simple-firewall' ),
											   'login_link'       => __( 'Why no login link?', 'wp-simple-firewall' ),
											   'details_heading'  => __( 'Login Details', 'wp-simple-firewall' ),
											   'details_url'      => sprintf( '%s: %s', __( 'URL', 'wp-simple-firewall' ),
												   Services::WpGeneral()->getHomeUrl() ),
											   'details_username' => sprintf( '%s: %s', __( 'Username', 'wp-simple-firewall' ), $user->user_login ),
											   'details_ip'       => sprintf( '%s: %s', __( 'IP Address', 'wp-simple-firewall' ),
												   Services::IP()->getRequestIp() ),
										   ]
									   ]
								   );
		}
		catch ( \Exception $e ) {
			$sendSuccess = false;
		}

		$this->getCon()->fireEvent(
			$sendSuccess ? '2fa_email_send_success' : '2fa_email_send_fail',
			[
				'audit' => [
					'user_login' => $user->user_login,
				]
			]
		);

		return $this;
	}

	private function send2faEmailSureSend( \WP_User $user, string $code ) :bool {
		return ( new SendEmail() )
			->setMod( $this->getMod() )
			->send2FA(
				$user,
				$code
			);
	}

	public function renderUserProfileOptions( \WP_User $user ) :string {
		$aData = [
			'strings' => [
				'label_email_authentication'                => __( 'Email Authentication', 'wp-simple-firewall' ),
				'title'                                     => __( 'Email Authentication', 'wp-simple-firewall' ),
				'description_email_authentication_checkbox' => __( 'Check the box to enable email-based login authentication.', 'wp-simple-firewall' ),
				'provided_by'                               => sprintf( __( 'Provided by %s', 'wp-simple-firewall' ), $this->getCon()
																														   ->getHumanName() )
			]
		];

		return $this->getMod()
					->renderTemplate(
						'/snippets/user/profile/mfa/mfa_email.twig',
						Services::DataManipulation()->mergeArraysRecursive( $this->getCommonData( $user ), $aData ),
						true
					);
	}

	public function isProviderEnabled() :bool {
		/** @var LoginGuard\Options $opts */
		$opts = $this->getOptions();
		return $opts->isEmailAuthenticationActive();
	}

	public function isProviderAvailableToUser( \WP_User $user ) :bool {
		/** @var LoginGuard\Options $opts */
		$opts = $this->getOptions();
		return parent::isProviderAvailableToUser( $user )
			   && ( $this->isEnforced( $user ) || $opts->isEnabledEmailAuthAnyUserSet() );
	}

	private function genLoginLink( \WP_User $user, string $otp ) :string {
		/** @var LoginGuard\Options $opts */
		$opts = $this->getOptions();
		$action = uniqid( '2fa_verify' );
		return add_query_arg(
			[
				'user'                         => $user->user_login,
				$this->getLoginFormParameter() => $otp,
				'shield_nonce_action'          => $action,
				'shield_nonce'                 => $this->getCon()
													   ->nonce_handler->create( $action, $opts->getLoginIntentMinutes()*60 ),
			],
			Services::WpGeneral()->getHomeUrl()
		);
	}

	/**
	 * @param \WP_User $user
	 * @return string
	 */
	private function genNewCode( \WP_User $user ) {
		/** @var LoginGuard\Options $opts */
		$opts = $this->getOptions();

		$secrets = $this->getAllCodes( $user );
		$new = substr( strtoupper( preg_replace( '#io#i', '', wp_generate_password( 30, false ) ) ), 0, 6 );
		$secrets[ wp_hash_password( $new ) ] = Services::Request()
													   ->carbon()
													   ->addMinutes( $opts->getLoginIntentMinutes() )->timestamp;

		$this->storeCodes( $user, array_slice( $secrets, -10 ) );
		return $new;
	}

	/**
	 * @param \WP_User $user
	 * @return array
	 */
	private function getAllCodes( \WP_User $user ) {
		$secrets = $this->getSecret( $user );
		return array_filter(
			is_array( $secrets ) ? $secrets : [],
			function ( $ts ) {
				return $ts >= Services::Request()->ts();
			}
		);
	}

	/**
	 * @param \WP_User $user
	 * @param array    $codes
	 * @return $this
	 */
	private function storeCodes( \WP_User $user, array $codes ) {
		return $this->setSecret( $user, $codes );
	}
}