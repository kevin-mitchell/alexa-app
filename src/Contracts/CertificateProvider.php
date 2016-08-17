<?php
/**
 * Created by PhpStorm.
 * User: shoelessone
 * Date: 5/30/15
 * Time: 2:36 PM
 */

namespace Develpr\AlexaApp\Contracts;


interface CertificateProvider {

    public function getCertificateFromUri($certificateChainUri);

}
