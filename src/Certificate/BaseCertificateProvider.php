<?php

namespace Develpr\AlexaApp\Certificate;

use Develpr\AlexaApp\Exceptions\InvalidCertificateException;

abstract class BaseCertificateProvider
{
    const ECHO_SERVICE_DOMAIN = 'echo-api.amazon.com';

    /**
     * @param mixed $certificate
     *
     * @return array
     */
    protected function parseCertificate($certificate)
    {
        return openssl_x509_parse($certificate);
    }

    /**
     * Returns whether the configured service domain is present and validpresent
     *
     * @param array $parsedCertificate
     *
     * @return bool
     */
    protected function verifyCertificateSubjectAltNamePresent(array $parsedCertificate)
    {
        return strpos(array_get($parsedCertificate, 'extensions.subjectAltName'), self::ECHO_SERVICE_DOMAIN) !== false;
    }

    /**
     * Returns whether the date is valid
     *
     * @param array $parsedCertificate
     *
     * @return bool
     */
    protected function validateCertificateDate(array $parsedCertificate)
    {
        $validFrom = array_get($parsedCertificate, 'validFrom_time_t');

        $validTo = array_get($parsedCertificate, 'validTo_time_t');

        $time = time();

        return ($validFrom <= $time && $time <= $validTo);
    }

    /**
     * Retrieve the certificate from a url
     *
     * @param string $certificateChainUri
     *
     * @return string
     */
    protected function getRemoteCertificateChain($certificateChainUri)
    {
        return file_get_contents($certificateChainUri);
    }

    /**
     * Download/retrieve the certificate chain from a given location
     *
     * @param string $certificateChainUri
     *
     * @return string|null
     */
    public function getCertificateFromUri($certificateChainUri)
    {
        $certificateChain = $this->retrieveCertificateFromStore($certificateChainUri);

        if (!is_null($certificateChain) || $certificateChain === false) {
            $parsedCertificate = $this->parseCertificate($certificateChain);
            if (!$this->validateCertificateDate($parsedCertificate) || !$this->verifyCertificateSubjectAltNamePresent($parsedCertificate)) {
                $certificateChain = $this->storeRemoteCertificate($certificateChainUri);
            }
        } else {
            $certificateChain = $this->storeRemoteCertificate($certificateChainUri);
        }

        return $certificateChain;
    }

    /**
     * @param string $certificateChainUri
     *
     * @return string|null
     *
     * @throws InvalidCertificateException
     */
    protected function storeRemoteCertificate($certificateChainUri)
    {
        $certificateContents = $this->getRemoteCertificateChain($certificateChainUri);

        $parsedCertificate = $this->parseCertificate($certificateContents);

        // If the certificate we are getting from the URL doesn't have a valid SAN present, this is not secure!
        if (!$this->verifyCertificateSubjectAltNamePresent($parsedCertificate)) {
            throw new InvalidCertificateException("The remote certificate doesn't contain a valid SANs in the signature");
        }

        $this->persistCertificate($certificateChainUri, $certificateContents);

        return $this->retrieveCertificateFromStore($certificateChainUri);
    }

    /**
     * Persist the certificate contents to data store so it's retrievable using the certificate chain uri
     *
     * @param string $certificateChainUri
     * @param string $certificateContents
     */
    protected abstract function persistCertificate($certificateChainUri, $certificateContents);

    /**
     * Retrieve the certificate give the certificate chain's uri from the data store
     *
     * @param string $certificateChainUri
     *
     * @return string|null
     */
    protected abstract function retrieveCertificateFromStore($certificateChainUri);
}
