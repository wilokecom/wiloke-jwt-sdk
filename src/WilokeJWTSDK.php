<?php


namespace WilokeJWTSDK;


class WilokeJWTSDK {
	use TraitDefine;

	/**
	 * @param array{redirect_ur?: string, app_id: string, app_secret: string, client_session: string  } $aAPI
	 * @param string $version
	 *
	 * @return WilokeJWTSDK|null
	 */
	public static function setup( array $aAPI, string $version = 'v1' ): ?WilokeJWTSDK {
		if ( ! self::$oSelf ) {
			self::$oSelf = new self();
		}

		self::$oSelf->performSetup( $aAPI, $version );

		return self::$oSelf;
	}

	private function performSetup( array $aAPI, string $version ): WilokeJWTSDK {
		$this->aAPI    = wp_parse_args( $this->aDefaultAPI, $aAPI );
		$this->version = $version;

		return $this;
	}

	private function generateEndpoint( string $namespace ): string {
		return $this->rootEndpoint . $this->ds . $this->version . $this->ds . $namespace;
	}

	public function requestAuthCode(): array {
		$response = wp_remote_post(
			$this->generateEndpoint( 'auth-code' ),
			[
				'body' => $this->aAPI
			]
		);

		if ( empty( $response ) || is_wp_error( $response ) ) {
			return [
				'status' => 'error',
				'msg'    => 'Something went error'
			];
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * @param string $code Tra lai tu api requestAuthCode
	 * @param string $email
	 *
	 * @return mixed|string[]
	 */
	public function signUp( string $code, string $email ) {
		$response = wp_remote_post(
			$this->generateEndpoint( 'sign-up' ),
			[
				'body' => array_merge( $this->aAPI, [ 'email' => $email, 'code' => $code ] )
			]
		);

		if ( empty( $response ) || is_wp_error( $response ) ) {
			return [
				'status' => 'error',
				'msg'    => 'Something went error'
			];
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	public function renewToken( string $accessToken ) {
		$response = wp_remote_post(
			$this->generateEndpoint( 'renew-token' ),
			[
				'body' => array_merge( $this->aAPI, [ 'accessToken' => $accessToken ] )
			]
		);

		if ( empty( $response ) || is_wp_error( $response ) ) {
			return [
				'status' => 'error',
				'msg'    => 'Something went error'
			];
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}
