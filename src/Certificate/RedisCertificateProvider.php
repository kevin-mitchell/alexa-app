<?php  namespace Develpr\AlexaApp\Certificate;


use Develpr\AlexaApp\Contracts\CertificateProvider;
use Develpr\AlexaApp\Exceptions\InvalidCertificateException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Redis\Database as RedisDatabase;

class RedisCertificateProvider extends BaseCertificateProvider implements CertificateProvider {

    /**
     * @var \Predis\Client
     */
    private $redis;

    function __construct(RedisDatabase $redis)
    {
        $this->redis = $redis->connection();
    }

    /**
     * Persist the certificate contents to data store so it's retrievable using the certificate chain uri
     *
     * @param String $certificateChainUri
     * @param String $certificateContents
     */
    protected function persistCertificate($certificateChainUri, $certificateContents)
    {
        $this->redis->set($certificateChainUri, $certificateContents);
    }

    /**
     * Retrieve the certificate give the certificate chain's uri from the data store
     *
     * @param String $certificateChainUri
     * @return String | null
     */
    protected function retrieveCertificateFromStore($certificateChainUri)
    {
        return $this->redis->get($certificateChainUri);
    }


}
