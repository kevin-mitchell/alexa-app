<?php  namespace Develpr\AlexaApp\Http\Middleware;


use Closure;
use Develpr\AlexaApp\Contracts\CertificateProvider;
use Develpr\AlexaApp\Exceptions\InvalidSignatureChainException;
use Develpr\AlexaApp\Request\AlexaRequest;
use Illuminate\Contracts\Routing\Middleware;
use \Illuminate\Http\Request;
use Develpr\AlexaApp\Exceptions\InvalidCertificateException;
use Develpr\AlexaApp\Exceptions\InvalidAppIdException;

class VerifyAlexaRequestSecurity implements Middleware{

	const CERTIFICATE_URL_HEADER 	= "Signaturecertchainurl";
	const SIGNATURE_HEADER 			= "Signature";
	const ENCRYPT_METHOD			= "sha1WithRSAEncryption";

	/**
	 * @var \Develpr\AlexaApp\Request\AlexaRequest
	 */
	private $alexaRequest;
	/**
	 * @var \Develpr\AlexaApp\Contracts\CertificateProvider
	 */
	private $certificateProvider;

	function __construct(AlexaRequest $alexaRequest, CertificateProvider $certificateProvider)
	{
		$this->alexaRequest = $alexaRequest;
		$this->certificateProvider = $certificateProvider;
	}


	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		//If this is not an Alexa Request at all, we'll do nothing
		//todo: possibly remove this and force users to specify middleware on Alexa specific routes?
		if( ! $this->alexaRequest->isAlexaRequest() )
			return $next($request);

		$this->verifyApplicationId();


		$signature = $this->getDecodedSignature($request);

		$certificate = $this->getCertificate($request);

		//Get the request body that will be validated
		$data = $request->getContent();

		$certKey = openssl_pkey_get_public($certificate);

		// ok, let's do this thing! we're saving the world from hackers here!
		$valid = openssl_verify($data, $signature, $certKey, self::ENCRYPT_METHOD);

		if ($valid == 1) {
			return $next($request);
		} elseif ($valid == 0) {
			throw new InvalidSignatureChainException("The request did not validate against the certificate chain.");
		} else {
			throw new \Exception("Something went wrong when validating the request and certificate.");
		}

	}

	private function getCertificate(Request $request){

		$signatureChainUri = $request->header(self::CERTIFICATE_URL_HEADER);

		$this->validateKeychainUri($signatureChainUri);

		$certificate = $this->certificateProvider->getCertificateFromUri($signatureChainUri);

		return $certificate;

	}


	/**
	 * @param $keychainUri
	 * @throws \Develpr\AlexaApp\Exceptions\InvalidCertificateException
	 */
	private function validateKeychainUri($keychainUri){

		$uriParts = parse_url($keychainUri);

		if( strcasecmp($uriParts['host'], 's3.amazonaws.com') != 0)
			throw new InvalidCertificateException("The host for the Certificate provided in the header is invalid");

		if( strpos($uriParts['path'], '/echo.api/') !== 0 )
			throw new InvalidCertificateException("The URL path for the Certificate provided in the header is invalid");

		if( strcasecmp($uriParts['scheme'], 'https') != 0)
			throw new InvalidCertificateException("The URL is using an unsupported scheme. Should be https");

		if( array_key_exists('port', $uriParts) && $uriParts['port'] != 443)
			throw new InvalidCertificateException("The URL is using an unsupported https port");

	}

	/**
	 * @param Request $request
	 * @return string
	 */
	private function getDecodedSignature(Request $request)
	{
		$signature = $request->header(self::SIGNATURE_HEADER);

		$base64DecodedSignature = base64_decode($signature);

		return $base64DecodedSignature;

	}

	private function verifyApplicationId()
	{
		if( ! boolval(config('alexa.verifyAppId')) )
			return;

		$possible = config('alexa.appIds');

		//Somebody might use the .env files and set the appIds as a string instead of an array so we'll be sure
		if( ! is_array($possible) )
			$possible = array($possible);

		$appId = $this->alexaRequest->getAppId();

		if( ! in_array($appId, $possible))
			throw new InvalidAppIdException("The request's app id does not match the configured app id(s)");
	}


}