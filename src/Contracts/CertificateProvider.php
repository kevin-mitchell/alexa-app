<?php

namespace Frijj2k\LarAlexa\Contracts;

interface CertificateProvider
{
    /**
     * @param string $certificateChainUri
     *
     * @return string|null
     */
    public function getCertificateFromUri($certificateChainUri);
}
