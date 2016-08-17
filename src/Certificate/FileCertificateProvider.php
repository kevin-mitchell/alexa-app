<?php

namespace Develpr\AlexaApp\Certificate;

use Develpr\AlexaApp\Contracts\CertificateProvider;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class FileCertificateProvider extends BaseCertificateProvider implements CertificateProvider
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $filePath;

    /**
     * FileCertificateProvider constructor.
     *
     * @param Filesystem $filesystem
     * @param string     $filePath
     */
    public function __construct(Filesystem $filesystem, $filePath)
    {
        $this->filesystem = $filesystem;
        $this->filePath = $filePath;

        if (!$this->filesystem->isDirectory($this->filePath)) {
            $this->filesystem->makeDirectory($this->filePath);
        }
    }

    /**
     * Persist the certificate contents to data store so it's retrievable using the certificate chain uri
     *
     * @param string $certificateChainUri
     * @param string $certificateContents
     */
    protected function persistCertificate($certificateChainUri, $certificateContents)
    {
        $this->filesystem->put($this->calculateFilePath($certificateChainUri), $certificateContents);
    }

    /**
     * Retrieve the certificate give the certificate chain's uri from the datastore
     *
     * @param string $certificateChainUri
     *
     * @return string|null
     */
    protected function retrieveCertificateFromStore($certificateChainUri)
    {
        try {
            $certificateChain = $this->filesystem->get($this->calculateFilePath($certificateChainUri));
        } catch (FileNotFoundException $e) {
            $certificateChain = null;
        }

        return $certificateChain;
    }

    /**
     * Calculate the path that the certificate should be stored
     *
     * @param string $certificateChainUri
     *
     * @return string
     */
    private function calculateFilePath($certificateChainUri)
    {
        $filename = md5($certificateChainUri);

        $path = $this->filePath.$filename;

        return $path;
    }
}
