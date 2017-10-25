<?php

namespace Develpr\AlexaApp\Http\Middleware;

use Closure;
use Develpr\AlexaApp\Contracts\AlexaRequest;
use Develpr\AlexaApp\Contracts\CertificateProvider;
use Develpr\AlexaApp\Exceptions\InvalidAppIdException;
use Develpr\AlexaApp\Exceptions\InvalidCertificateException;
use Develpr\AlexaApp\Exceptions\InvalidRequestTimestamp;
use Develpr\AlexaApp\Exceptions\InvalidSignatureChainException;
use Illuminate\Http\Request as IlluminateRequest;

class Certificate
{
    const CERTIFICATE_URL_HEADER = "Signaturecertchainurl";
    const SIGNATURE_HEADER       = "Signature";
    const ENCRYPT_METHOD         = "sha1WithRSAEncryption";

    /**
     * @var \Develpr\AlexaApp\Contracts\AlexaRequest
     */
    private $alexaRequest;

    /**
     * @var \Develpr\AlexaApp\Contracts\CertificateProvider
     */
    private $certificateProvider;

    /**
     * @var array
     */
    private $config;

    /**
     * Certificate constructor.
     *
     * @param AlexaRequest        $alexaRequest
     * @param CertificateProvider $certificateProvider
     * @param array               $config
     */
    public function __construct(AlexaRequest $alexaRequest, CertificateProvider $certificateProvider, array $config)
    {
        $this->alexaRequest = $alexaRequest;
        $this->certificateProvider = $certificateProvider;
        $this->config = $config;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     *
     * @throws InvalidSignatureChainException
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (!$this->alexaRequest->isAlexaRequest()) {
            return $next($request);
        }

        $this->verifyApplicationId();
        $this->checkTimestampTolerance();
        $certificateResult = $this->verifyCertificate($request);

        if ($certificateResult === 1) {
            return $next($request);
        } elseif ($certificateResult === 0) {
            throw new InvalidSignatureChainException("The request did not validate against the certificate chain.");
        } else {
            throw new \Exception("Something went wrong when validating the request and certificate.");
        }
    }

    /**
     * Get the certificate from the certificate provider
     *
     * @param IlluminateRequest $request
     *
     * @return mixed
     */
    private function getCertificate(IlluminateRequest $request)
    {
        $signatureChainUri = $request->header(self::CERTIFICATE_URL_HEADER);

        $this->validateKeychainUri($signatureChainUri);

        $certificate = $this->certificateProvider->getCertificateFromUri($signatureChainUri);

        return $certificate;
    }


    /**
     * @param string $keychainUri
     *
     * @throws InvalidCertificateException
     */
    private function validateKeychainUri($keychainUri)
    {
        $uriParts = parse_url($keychainUri);

        if (strcasecmp($uriParts['host'], array_get($this->config, 'origin.host')) !== 0) {
            throw new InvalidCertificateException("The host for the Certificate provided in the header is invalid");
        }

        if (strpos($uriParts['path'], array_get($this->config, 'origin.path')) !== 0) {
            throw new InvalidCertificateException("The URL path for the Certificate provided in the header is invalid");
        }

        if (strcasecmp($uriParts['scheme'], array_get($this->config, 'origin.scheme')) !== 0) {
            throw new InvalidCertificateException("The URL is using an unsupported scheme. Should be https");
        }

        if (array_key_exists('port', $uriParts) && $uriParts['port'] != array_get($this->config,'origin.port')) {
            throw new InvalidCertificateException("The URL is using an unsupported https port");
        }
    }

    /**
     * @param IlluminateRequest $request
     *
     * @return string
     */
    private function getDecodedSignature(IlluminateRequest $request)
    {
        $signature = $request->header(self::SIGNATURE_HEADER);
        $base64DecodedSignature = base64_decode($signature);

        return $base64DecodedSignature;
    }

    /**
     * @throws InvalidAppIdException
     */
    private function verifyApplicationId()
    {
        if (!boolval(array_get($this->config, 'verifyAppId'))) {
            return;
        }

        $possible = array_get($this->config, 'applicationIds');

        //Somebody might use the .env files and set the applicationIds as a string instead of an array so we'll be sure
        if (!is_array($possible)) {
            $possible = array($possible);
        }

        $appId = $this->alexaRequest->getAppId();

        if (!in_array($appId, $possible)) {
            throw new InvalidAppIdException("The request's app id does not match the configured app id(s)");
        }
    }

    /**
     * @throws InvalidRequestTimestamp
     */
    private function checkTimestampTolerance()
    {
        //If the timestamp tolerance is set to 0 we'll skip the check (see config)
        if (intval(array_get($this->config, 'timestampTolerance')) === 0) {
            return;
        }

        $timestampTolerance = array_get($this->config, 'timestampTolerance');
        $timestamp = $this->alexaRequest->getTimestamp();

        if (time() - $timestamp > $timestampTolerance) {
            throw new InvalidRequestTimestamp("The request timestamp is older then configured timestamp tolerances allow");
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return int
     */
    private function verifyCertificate(IlluminateRequest $request)
    {
        $signature = $this->getDecodedSignature($request);
        $certificate = $this->getCertificate($request);

        //Get the request body that will be validated
        $data = $request->getContent();

        $certKey = openssl_pkey_get_public($certificate);

        // ok, let's do this thing! we're saving the world from hackers here!
        $valid = openssl_verify($data, $signature, $certKey, self::ENCRYPT_METHOD);

        return $valid;
    }
}
