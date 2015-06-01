<?php namespace Develpr\AlexaApp\Certificate;

trait CertificateTools {

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
	protected function verifyCertificateSubjectAltNamePresent(array $parsedCertificate, $amazonServiceDomain)
	{
		if(strpos(array_get($parsedCertificate, 'extensions.subjectAltName'), $amazonServiceDomain) === false)
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

	protected function getRemoteCertificateChain($certificateChainUri){

		return file_get_contents($certificateChainUri);

	}
} 