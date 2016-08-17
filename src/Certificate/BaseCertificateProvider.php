<?php

namespace Develpr\AlexaApp\Certificate;

use Develpr\AlexaApp\Exceptions\InvalidCertificateException;

abstract class BaseCertificateProvider {

    const ECHO_SERVICE_DOMAIN = 'echo-api.amazon.com';

    /**
     * @param $certificate
     * @return array | null
     */
    protected function parseCertificate($certificate){

        return openssl_x509_parse($certificate);

    }

    /**
     * returns true if the configured service domain is present/valid, false if invalid/not present
     * @param array $parsedCertificate
     * @return boolean
     */
    protected function verifyCertificateSubjectAltNamePresent(array $parsedCertificate)
    {
        if(strpos(array_get($parsedCertificate, 'extensions.subjectAltName'), self::ECHO_SERVICE_DOMAIN) === false)
            return false;
        else
            return true;
    }

    /**
     * returns true if the date is valid, false if not
     *
     * @param array $parsedCertificate
     * @return boolean
     */
    protected function validateCertificateDate(array $parsedCertificate){

        $validFrom = array_get($parsedCertificate, 'validFrom_time_t');

        $validTo = array_get($parsedCertificate, 'validTo_time_t');

        $time = time();

        return ($validFrom <= $time && $time <= $validTo);

    }

    /**
     * Retrieve the certificate from a url
     *
     * @param $certificateChainUri
     * @return string
     */
    protected function getRemoteCertificateChain($certificateChainUri){

        return file_get_contents($certificateChainUri);

    }

    /**
     * Download/retrieve the certificate chain from a given location
     *
     * @param $certificateChainUri
     * @return null|String
     */
    public function getCertificateFromUri($certificateChainUri)
    {
        $certificateChain = $this->retrieveCertificateFromStore($certificateChainUri);

        if( ! is_null($certificateChain) || $certificateChain === false){

            $parsedCertificate = $this->parseCertificate($certificateChain);

            if(! $this->validateCertificateDate($parsedCertificate) || ! $this->verifyCertificateSubjectAltNamePresent($parsedCertificate))
                $certificateChain = $this->storeRemoteCertificate($certificateChainUri);

        }else{
            $certificateChain = $this->storeRemoteCertificate($certificateChainUri);
        }


        return $certificateChain;
    }

    protected function storeRemoteCertificate($certificateChainUri){

        $certificateContents = $this->getRemoteCertificateChain($certificateChainUri);

        $parsedCertificate = $this->parseCertificate($certificateContents);

        //if the certificate we are getting from the URL doesn't have a valid SAN present, this is not secure!
        if( ! $this->verifyCertificateSubjectAltNamePresent($parsedCertificate) )
            throw new InvalidCertificateException("The remote certificate doesn't contain a valid SANs in the signature");

        $this->persistCertificate($certificateChainUri, $certificateContents);

        return($this->retrieveCertificateFromStore($certificateChainUri));

    }

    /**
     * Persist the certificate contents to data store so it's retrievable using the certificate chain uri
     *
     * @param String $certificateChainUri
     * @param String $certificateContents
     */
    protected abstract function persistCertificate($certificateChainUri, $certificateContents);

    /**
     * Retrieve the certificate give the certificate chain's uri from the data store
     *
     * @param String $certificateChainUri
     * @return String | null
     */
    protected abstract function retrieveCertificateFromStore($certificateChainUri);

}
