<?php

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

    if (!is_null($authParams['email'])) {
      $this->validateUser('email', $authParams['email'], $authParams['password']);
    }
    elseif (!is_null($authParams['phone'])) {
      $this->validateUser('phone', $authParams['phone'], $authParams['password']);
    }
    elseif (!is_null($authParams['drupal_uid'])) {
      $this->validateUser('drupal_uid', $authParams['drupal_uid'], $authParams['password']);
    }
    elseif (!is_null($authParams['username'])) {
      $this->validateUser('username', $authParams['username'], $authParams['password']);
    }
    else {
      throw new Exception\ClientException('The request is missing one of the required user identifying parameters: email, phone, drupal_uid, or username.', 0);
    }

    // @todo Validate any scopes in the request.

    // @todo Generate or retrieve the access token.

    // @todo If new token is generated, create a new session.

    // @todo Associate the new access token with the new session.

    // @todo Associates scopes with the access token.

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
  }
}
