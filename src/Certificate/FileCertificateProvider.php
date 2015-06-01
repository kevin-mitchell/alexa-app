<?php  namespace Develpr\AlexaApp\Certificate;


use Develpr\AlexaApp\Contracts\CertificateProvider;
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

	/**
	 * Persist the certificate contents to data store so it's retrievable using the certificate chain uri
	 *
	 * @param String $certificateChainUri
	 * @param String $certificateContents
	 */
	protected function persistCertificate($certificateChainUri, $certificateContents)
	{
		$this->filesystem->put($this->calculateFilePath($certificateChainUri), $certificateContents);
	}

	/**
	 * Retrieve the certificate give the certificate chain's uri from the datastore
	 *
	 * @param String $certificateChainUri
	 * @return String | null
	 */
	protected function retrieveCertificateFromStore($certificateChainUri)
	{
		try{
			$certificateChain = $this->filesystem->get($this->calculateFilePath($certificateChainUri));
		}catch(FileNotFoundException $e){
			$certificateChain = null;
		}

		return $certificateChain;
	}

	/**
	 * Calculate the path that the certificate should be stored
	 *
	 * @param $certificateChainUri
	 * @return string
	 */
	private function calculateFilePath($certificateChainUri){

		$filename = md5($certificateChainUri);

		$path = $this->filePath . $filename;

		return $path;

	}

} 