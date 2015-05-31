<?php  namespace Develpr\AlexaApp\Certificate;


use Develpr\AlexaApp\Exceptions\InvalidCertificateException;

abstract class BaseCertificateProvider {

	protected function validateCertificateDate($certificate){

		$parsedCertificate = openssl_x509_parse($certificate);

		$validFrom = $parsedCertificate['validFrom_time_t'];

		$validTo = $parsedCertificate['validTo_time_t'];

		$time = time();

		return ($validFrom <= $time && $time <= $validTo);

	}

	protected function getRemoteCertificateChain($certificateChainUri){

		return file_get_contents($certificateChainUri);

	}
} 