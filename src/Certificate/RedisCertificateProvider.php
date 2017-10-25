<?php

namespace Develpr\AlexaApp\Certificate;

use Develpr\AlexaApp\Contracts\CertificateProvider;
use Illuminate\Redis\Database as RedisDatabase;

class RedisCertificateProvider extends BaseCertificateProvider implements CertificateProvider
{
    /**
     * @var \Predis\Client
     */
    private $redis;

    /**
     * RedisCertificateProvider constructor.
     *
     * @param \Illuminate\Redis\Database $redis
     */
    public function __construct(RedisDatabase $redis)
    {
        $this->redis = $redis->connection();
    }

    /**
     * Persist the certificate contents to data store so it's retrievable using the certificate chain uri
     *
     * @param string $certificateChainUri
     * @param string $certificateContents
     */
    protected function persistCertificate($certificateChainUri, $certificateContents)
    {
        $this->redis->set($certificateChainUri, $certificateContents);
    }

    /**
     * Retrieve the certificate give the certificate chain's uri from the data store
     *
     * @param string $certificateChainUri
     *
     * @return string|null
     */
    protected function retrieveCertificateFromStore($certificateChainUri)
    {
        return $this->redis->get($certificateChainUri);
    }
}
