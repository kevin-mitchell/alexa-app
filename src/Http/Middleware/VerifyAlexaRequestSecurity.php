<?php  namespace Develpr\AlexaApp\Http\Middleware;


use Closure;
use Develpr\AlexaApp\Contracts\CertificateProvider;
use Develpr\AlexaApp\Request\AlexaRequest;
use Illuminate\Contracts\Routing\Middleware;
use \Illuminate\Http\Request;
use Develpr\AlexaApp\Exceptions\InvalidCertificateException;

class VerifyAlexaRequestSecurity implements Middleware{

	const CERTIFICATE_URL_HEADER 	= "Signaturecertchainurl";
	const SIGNATURE_HEADER 			= "Signature";

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

		$signature = $this->getDecodedSignature($request);

		$imageString = file_get_contents($signatureChainUrl);
//		$save = file_put_contents($path,$imageString);

//		$data = fopen('php://input', 'rb');
//		$data = file_get_contents('php://input');
		$data = $request->getContent();

		$test = openssl_x509_parse($imageString);

		$time = time();

		$pubkeyid = openssl_pkey_get_public($imageString);

// state whether signature is okay or not
		$ok = openssl_verify($data, $signature, $pubkeyid, "sha1WithRSAEncryption");

		if ($ok == 1) {
			echo "good";
		} elseif ($ok == 0) {
			echo "bad";
		} else {
			echo "ugly, error checking signature";
		}



		return $next($request);
	}

	private function getCertificate(Request $request){

		$signatureChainUri = $request->header(self::CERTIFICATE_URL_HEADER);

		$this->validateKeychainUri($signatureChainUri);

		$certificate = $this->certificateProvider->getCertificateFromUri($signatureChainUri);



	}

	private function getSignatureChainUrl($request){



	}

	private function verifyChainUrl($url){

	}


	/**
	 * @param $keychainUri
	 * @throws \Develpr\AlexaApp\Exceptions\InvalidCertificateException
	 */
	protected function validateKeychainUri($keychainUri){

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


	protected function validateCertificateDates($fromDate, $toDate){

		$time = time();

		return ($fromDate <= $time && $time <= $toDate);
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


}