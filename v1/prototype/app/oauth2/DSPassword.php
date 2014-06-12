<?php

use Carbon\Carbon;
use League\OAuth2\Server\Util\SecureKey;

/**
 * Custom password grant class for OAuth2 implementation.
 */
class DSPassword extends League\OAuth2\Server\Grant\Password {

  /**
   * URL to send user validation requests to.
   * @var string
   */
  protected $userValidationUrl = 'http://wwww.dosomething.org/user/access';

  /**
   * Overrides the parent implementation. Validates the request based on the
   * credentials given.
   *
   * @param null|array $inputParams
   * @return array
   */
  public function completeFlow($inputParams = null) {

    $authParams = $this->authServer->getParam(
      array(
        'client_id',
        'client_secret',
        'email',
        'phone',
        'drupal_uid',
        'username',
        'password'),
      'post',
      $inputParams
    );

    // Validate the client credentials.
    $this->validateClient($authParams['client_id'], $authParams['client_secret']);

    // Validate the user credentials.
    if (is_null($authParams['password'])) {
      throw new Exception\ClientException(sprintf($this->authServer->getExceptionMessage('invalid_request'), 'password'), 0);
    }

    $userId = 0;
    if (!is_null($authParams['email'])) {
      $userId = $this->validateUser('email', $authParams['email'], $authParams['password']);
    }
    elseif (!is_null($authParams['phone'])) {
      $userId = $this->validateUser('phone', $authParams['phone'], $authParams['password']);
    }
    elseif (!is_null($authParams['drupal_uid'])) {
      $userId = $this->validateUser('drupal_uid', $authParams['drupal_uid'], $authParams['password']);
    }
    elseif (!is_null($authParams['username'])) {
      $userId = $this->validateUser('username', $authParams['username'], $authParams['password']);
    }
    else {
      throw new Exception\ClientException('The request is missing one of the required user identifying parameters: email, phone, drupal_uid, or username.', 0);
    }

    // @todo Validate any scopes in the request.

    // Generate or retrieve the access token.
    $this->assignAccessToken($userId, $accessToken, $accessTokenExpires, $accessTokenExpiresIn);

    $response = array(
      'access_token'  =>  $accessToken,
      'token_type'    =>  'bearer',
      'expires'       =>  $accessTokenExpires,
      'expires_in'    =>  $accessTokenExpiresIn
    );

    return $response;
  }

  /**
   * Validate the client credentials.
   *
   * @param string $clientId
   * @param string $clientSecret
   */
  private function validateClient($clientId, $clientSecret) {
    $clientDetails = $this->authServer->getStorage('client')->getClient($clientId, $clientSecret, null, $this->identifier);

    if ($clientDetails === false) {
        throw new Exception\ClientException($this->authServer->getExceptionMessage('invalid_client'), 8);
    }
  }

  /**
   * Validate the user credentials.
   *
   * @param string $param
   * @param string $user
   * @param string $password
   * @return string
   */
  private function validateUser($param, $user, $password) {
    // User data payload.
    $data = array(
      $param => $user,
      'password' => $password,
    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->userValidationUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);

    $response = curl_exec($ch);

    // Extract header and body from the response.
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    // Alternatively could do: list($header, $body) = explode("\r\n\r\n", $response, 2);

    curl_close($ch);

    // @todo Determine access based on response. 200 OK = good. 401 Unauthorized = no good.

    $authorized = false;
    if ($unauthorized) {
      throw new Exception\ClientException($this->authServer->getExceptionMessage('invalid_credentials'), 0);
    }

    // @todo Get user id from response body and return.
    return $userId;
  }

  /**
   * Assigns the access token for the user's request and updates or creates
   * the session entries in the database.
   *
   * @param string $userId
   * @param string &$accessToken
   * @param string &$accessTokenExpires
   * @param string &$accessTokenExpiresIn
   * @return string
   */
  private function assignAccessToken($userId, &$accessToken, &$accessTokenExpires, &$accessTokenExpiresIn) {
    // @todo - everywhere DB::table is used here, subclass LucaDegasperi\OAuth2Server\Repositories\FluentSession
    // and add wrappers to these database queries.

    // Check if this user already has a session.
    $sessions = DB::table('oauth_sessions')
                          ->where('owner_id', $userId)
                          ->where('owner_type', 'user')
                          ->take(1)
                          ->get();

    // Existing session found for this user.
    if (count($sessions) > 0) {
      // Get the access token info for that session.
      $tokens = DB::table('oauth_session_access_tokens')
                          ->where('session_id', $sessions[0]->id)
                          ->get();

      if (count($tokens) > 0) {
        // Update and pass back by reference the access token info.
        $accessToken = $tokens[0]->access_token;
        $accessTokenExpiresIn = ($this->accessTokenTTL !== null) ? $this->accessTokenTTL : $this->authServer->getAccessTokenTTL();
        $accessTokenExpires = time() + $accessTokenExpiresIn;

        DB::table('oauth_session_access_tokens')
                  ->where('session_id', $sessions[0]->id)
                  ->update(array(
                      'access_token_expires' => $accessTokenExpires,
                      'updated_at' => Carbon::now(),
                    ));
      }
    }
    else {
      // If we're here, then session and token don't exist for this user.
      // Generate a new access token.
      $accessToken = SecureKey::make();
      $accessTokenExpiresIn = ($this->accessTokenTTL !== null) ? $this->accessTokenTTL : $this->authServer->getAccessTokenTTL();
      $accessTokenExpires = time() + $accessTokenExpiresIn;

      // Create a new session
      $sessionId = $this->authServer->getStorage('session')->createSession($authParams['client_id'], 'user', $userId);

      // Associate an access token with the session
      $accessTokenId = $this->authServer->getStorage('session')->associateAccessToken($sessionId, $accessToken, $accessTokenExpires);

      // Associate scopes with the access token
      foreach ($authParams['scopes'] as $scope) {
          $this->authServer->getStorage('session')->associateScope($accessTokenId, $scope['id']);
      }
    }
  }

}
