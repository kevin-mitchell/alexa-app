<?php  namespace Develpr\AlexaApp\Certificate;


use Develpr\AlexaApp\Contracts\CertificateProvider;
use Develpr\AlexaApp\Exceptions\InvalidCertificateException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class FileCertificateProvider extends BaseCertificateProvider implements CertificateProvider {

	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var String
	 */
	private $filePath;

	function __construct(Filesystem $filesystem, $filePath)
	{
		$this->filesystem = $filesystem;
		$this->filePath = $filePath;

		if( ! $this->filesystem->isDirectory($this->filePath) ){
			$this->filesystem->makeDirectory($this->filePath);
		}
	}


	public function getCertificateFromUri($certificateChainUri)
	{
		$filename = md5($certificateChainUri);

		$path = $this->filePath . $filename;

		try{
			$certificateChain = $this->filesystem->get($path);

			$parsedCertificate = $this->parseCertificate($certificateChain);

			if(! $this->validateCertificateDate($parsedCertificate) || ! $this->verifyCertificateSubjectAltNamePresent($parsedCertificate))
				$certificateChain = $this->storeRemoteCertificate($certificateChainUri, $path);

		}catch(FileNotFoundException $e){
			$certificateChain = $this->storeRemoteCertificate($certificateChainUri, $path);
		}

		return $certificateChain;
	}

	private function storeRemoteCertificate($certificateChainUri, $path){

		$certificateContents = $this->getRemoteCertificateChain($certificateChainUri);

		$parsedCertificate = $this->parseCertificate($certificateContents);

		//if the certificate we are getting from the URL doesn't have a valid SAN present, this is not secure!
		if( ! $this->verifyCertificateSubjectAltNamePresent($parsedCertificate) )
			throw new InvalidCertificateException("The remote certificate doesn't contain a valid SANs in the signature");

		$this->filesystem->put($path, $certificateContents);

		return($this->filesystem->get($path));

	}



} 