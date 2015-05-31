<?php  namespace Develpr\AlexaApp\Certificate;


use Develpr\AlexaApp\Contracts\CertificateProvider;
use Develpr\AlexaApp\Request\BaseAlexaRequest;
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

			if(! $this->validateCertificateDate($certificateChain))
				$certificateChain = $this->storeRemoteCertificate($certificateChainUri, $path);

		}catch(FileNotFoundException $e){
			$certificateChain = $this->storeRemoteCertificate($certificateChainUri, $path);
		}

		return $certificateChain;
	}

	private function storeRemoteCertificate($certificateChainUri, $path){

		//todo: download file or get the contents!

		$contents = $this->getRemoteCertificateChain($certificateChainUri);

//		$contents = '-----BEGIN CERTIFICATE-----
//MIIFGzCCBAOgAwIBAgIQe8yiyiM0iKNWtiGrwbKQGzANBgkqhkiG9w0BAQUFADCB
//tTELMAkGA1UEBhMCVVMxFzAVBgNVBAoTDlZlcmlTaWduLCBJbmMuMR8wHQYDVQQL
//ExZWZXJpU2lnbiBUcnVzdCBOZXR3b3JrMTswOQYDVQQLEzJUZXJtcyBvZiB1c2Ug
//YXQgaHR0cHM6Ly93d3cudmVyaXNpZ24uY29tL3JwYSAoYykxMDEvMC0GA1UEAxMm
//VmVyaVNpZ24gQ2xhc3MgMyBTZWN1cmUgU2VydmVyIENBIC0gRzMwHhcNMTUwMTMx
//MDAwMDAwWhcNMTUxMDMxMjM1OTU5WjBtMQswCQYDVQQGEwJVUzETMBEGA1UECBMK
//V2FzaGluZ3RvbjEQMA4GA1UEBxQHU2VhdHRsZTEZMBcGA1UEChQQQW1hem9uLmNv
//bSwgSW5jLjEcMBoGA1UEAxQTZWNoby1hcGkuYW1hem9uLmNvbTCCASIwDQYJKoZI
//hvcNAQEBBQADggEPADCCAQoCggEBAJ7SGV+5dY+3DRrB7nRHNUaSXtDgQ3/DywsH
//BJAfxhOJbzgMYpo9LqUQ2ZR09ajX5pPPjVBg4qPoeHGNWUMVUSNO3UQKqjIThUji
//+wYBi+GJXT1ZlT0C9I9e8W13Hby/ESxErAI0rpDTAB+Iuq2CawZdRISrlOOfvwMS
//vFCLA54CCf9yFnq/wxCZZ567zp+PfvAhNqbFZk/jLJEO3ZuV67bvF5o7DgWiR9oV
//FuJ79iIgxGgFZJeUlrKyIAI634aX32KOGLzZ+ipsVLjOg/b6rzRZm08iw+U2kFSB
//QSQbn7YA6bpbw/PW5xRoB1J1miHUceiCqLsL4MnQt0JcoribXp8CAwEAAaOCAWww
//ggFoMB4GA1UdEQQXMBWCE2VjaG8tYXBpLmFtYXpvbi5jb20wCQYDVR0TBAIwADAO
//BgNVHQ8BAf8EBAMCBaAwHQYDVR0lBBYwFAYIKwYBBQUHAwEGCCsGAQUFBwMCMGUG
//A1UdIAReMFwwWgYKYIZIAYb4RQEHNjBMMCMGCCsGAQUFBwIBFhdodHRwczovL2Qu
//c3ltY2IuY29tL2NwczAlBggrBgEFBQcCAjAZGhdodHRwczovL2Quc3ltY2IuY29t
//L3JwYTAfBgNVHSMEGDAWgBQNRFwWU0TBgn4dIKsl9AFj2L55pTArBgNVHR8EJDAi
//MCCgHqAchhpodHRwOi8vc2Quc3ltY2IuY29tL3NkLmNybDBXBggrBgEFBQcBAQRL
//MEkwHwYIKwYBBQUHMAGGE2h0dHA6Ly9zZC5zeW1jZC5jb20wJgYIKwYBBQUHMAKG
//Gmh0dHA6Ly9zZC5zeW1jYi5jb20vc2QuY3J0MA0GCSqGSIb3DQEBBQUAA4IBAQA7
//aLx3H1brQcil1MpNSkyV4RwUdx8VvbysPc4toAg402dBJQLjDqD4eYLEg7hdtTfj
//AEyvuPpWmUA79UIZwyKhHcnRUU+kd68w53ibjBPlVS7irE0d3e1RheLhyIhUClMK
//lQA/uEfnXgNjbwjI4kxP6564IwL5LE1FksZhNqNQrCIt0D3c1cXvJn2+CZvyZQoO
//n3plmaSul1OnbvBoO6r8OI5nLCecebD8ansKJTTpCj5qtHj7rJgp4BkS3nvNd3JV
//h7oNUM9hXgk3Dypd8nkmZoYG5DQo+qAbvWribFPL96bLb34q94oH9f9SCnIQZstc
//VQEvq/IRMyDXwoTIEBaD
//-----END CERTIFICATE-----
//-----BEGIN CERTIFICATE-----
//MIIF7DCCBNSgAwIBAgIQbsx6pacDIAm4zrz06VLUkTANBgkqhkiG9w0BAQUFADCB
//yjELMAkGA1UEBhMCVVMxFzAVBgNVBAoTDlZlcmlTaWduLCBJbmMuMR8wHQYDVQQL
//ExZWZXJpU2lnbiBUcnVzdCBOZXR3b3JrMTowOAYDVQQLEzEoYykgMjAwNiBWZXJp
//U2lnbiwgSW5jLiAtIEZvciBhdXRob3JpemVkIHVzZSBvbmx5MUUwQwYDVQQDEzxW
//ZXJpU2lnbiBDbGFzcyAzIFB1YmxpYyBQcmltYXJ5IENlcnRpZmljYXRpb24gQXV0
//aG9yaXR5IC0gRzUwHhcNMTAwMjA4MDAwMDAwWhcNMjAwMjA3MjM1OTU5WjCBtTEL
//MAkGA1UEBhMCVVMxFzAVBgNVBAoTDlZlcmlTaWduLCBJbmMuMR8wHQYDVQQLExZW
//ZXJpU2lnbiBUcnVzdCBOZXR3b3JrMTswOQYDVQQLEzJUZXJtcyBvZiB1c2UgYXQg
//aHR0cHM6Ly93d3cudmVyaXNpZ24uY29tL3JwYSAoYykxMDEvMC0GA1UEAxMmVmVy
//aVNpZ24gQ2xhc3MgMyBTZWN1cmUgU2VydmVyIENBIC0gRzMwggEiMA0GCSqGSIb3
//DQEBAQUAA4IBDwAwggEKAoIBAQCxh4QfwgxF9byrJZenraI+nLr2wTm4i8rCrFbG
//5btljkRPTc5v7QlK1K9OEJxoiy6Ve4mbE8riNDTB81vzSXtig0iBdNGIeGwCU/m8
//f0MmV1gzgzszChew0E6RJK2GfWQS3HRKNKEdCuqWHQsV/KNLO85jiND4LQyUhhDK
//tpo9yus3nABINYYpUHjoRWPNGUFP9ZXse5jUxHGzUL4os4+guVOc9cosI6n9FAbo
//GLSa6Dxugf3kzTU2s1HTaewSulZub5tXxYsU5w7HnO1KVGrJTcW/EbGuHGeBy0RV
//M5l/JJs/U0V/hhrzPPptf4H1uErT9YU3HLWm0AnkGHs4TvoPAgMBAAGjggHfMIIB
//2zA0BggrBgEFBQcBAQQoMCYwJAYIKwYBBQUHMAGGGGh0dHA6Ly9vY3NwLnZlcmlz
//aWduLmNvbTASBgNVHRMBAf8ECDAGAQH/AgEAMHAGA1UdIARpMGcwZQYLYIZIAYb4
//RQEHFwMwVjAoBggrBgEFBQcCARYcaHR0cHM6Ly93d3cudmVyaXNpZ24uY29tL2Nw
//czAqBggrBgEFBQcCAjAeGhxodHRwczovL3d3dy52ZXJpc2lnbi5jb20vcnBhMDQG
//A1UdHwQtMCswKaAnoCWGI2h0dHA6Ly9jcmwudmVyaXNpZ24uY29tL3BjYTMtZzUu
//Y3JsMA4GA1UdDwEB/wQEAwIBBjBtBggrBgEFBQcBDARhMF+hXaBbMFkwVzBVFglp
//bWFnZS9naWYwITAfMAcGBSsOAwIaBBSP5dMahqyNjmvDz4Bq1EgYLHsZLjAlFiNo
//dHRwOi8vbG9nby52ZXJpc2lnbi5jb20vdnNsb2dvLmdpZjAoBgNVHREEITAfpB0w
//GzEZMBcGA1UEAxMQVmVyaVNpZ25NUEtJLTItNjAdBgNVHQ4EFgQUDURcFlNEwYJ+
//HSCrJfQBY9i+eaUwHwYDVR0jBBgwFoAUf9Nlp8Ld7LvwMAnzQzn6Aq8zMTMwDQYJ
//KoZIhvcNAQEFBQADggEBAAyDJO/dwwzZWJz+NrbrioBL0aP3nfPMU++CnqOh5pfB
//WJ11bOAdG0z60cEtBcDqbrIicFXZIDNAMwfCZYP6j0M3m+oOmmxw7vacgDvZN/R6
//bezQGH1JSsqZxxkoor7YdyT3hSaGbYcFQEFn0Sc67dxIHSLNCwuLvPSxe/20majp
//dirhGi2HbnTTiN0eIsbfFrYrghQKlFzyUOyvzv9iNw2tZdMGQVPtAhTItVgooazg
//W+yzf5VK+wPIrSbb5mZ4EkrZn0L74ZjmQoObj49nJOhhGbXdzbULJgWOw27EyHW4
//Rs/iGAZeqa6ogZpHFt4MKGwlJ7net4RYxh84HqTEy2Y=
//-----END CERTIFICATE-----
//-----BEGIN CERTIFICATE-----
//MIIExjCCBC+gAwIBAgIQNZcxh/OHOgcyfs5YDJt+2jANBgkqhkiG9w0BAQUFADBf
//MQswCQYDVQQGEwJVUzEXMBUGA1UEChMOVmVyaVNpZ24sIEluYy4xNzA1BgNVBAsT
//LkNsYXNzIDMgUHVibGljIFByaW1hcnkgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkw
//HhcNMDYxMTA4MDAwMDAwWhcNMjExMTA3MjM1OTU5WjCByjELMAkGA1UEBhMCVVMx
//FzAVBgNVBAoTDlZlcmlTaWduLCBJbmMuMR8wHQYDVQQLExZWZXJpU2lnbiBUcnVz
//dCBOZXR3b3JrMTowOAYDVQQLEzEoYykgMjAwNiBWZXJpU2lnbiwgSW5jLiAtIEZv
//ciBhdXRob3JpemVkIHVzZSBvbmx5MUUwQwYDVQQDEzxWZXJpU2lnbiBDbGFzcyAz
//IFB1YmxpYyBQcmltYXJ5IENlcnRpZmljYXRpb24gQXV0aG9yaXR5IC0gRzUwggEi
//MA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQCvJAgIKXo1nmAMqudLO07cfLw8
//RRy7K+D+KQL5VwijZIUVJ/XxrcgxiV0i6CqqpkKzj/i5Vbext0uz/o9+B1fs70Pb
//ZmIVYc9gDaTY3vjgw2IIPVQT60nKWVSFJuUrjxuf6/WhkcIzSdhDY2pSS9KP6HBR
//TdGJaXvHcPaz3BJ023tdS1bTlr8Vd6Gw9KIl8q8ckmcY5fQGBO+QueQA5N06tRn/
//Arr0PO7gi+s3i+z016zy9vA9r911kTMZHRxAy3QkGSGT2RT+rCpSx4/VBEnkjWNH
//iDxpg8v+R70rfk/Fla4OndTRQ8Bnc+MUCH7lP59zuDMKz10/NIeWiu5T6CUVAgMB
//AAGjggGRMIIBjTAPBgNVHRMBAf8EBTADAQH/MDEGA1UdHwQqMCgwJqAkoCKGIGh0
//dHA6Ly9jcmwudmVyaXNpZ24uY29tL3BjYTMuY3JsMA4GA1UdDwEB/wQEAwIBBjA9
//BgNVHSAENjA0MDIGBFUdIAAwKjAoBggrBgEFBQcCARYcaHR0cHM6Ly93d3cudmVy
//aXNpZ24uY29tL2NwczAdBgNVHQ4EFgQUf9Nlp8Ld7LvwMAnzQzn6Aq8zMTMwNAYD
//VR0lBC0wKwYJYIZIAYb4QgQBBgpghkgBhvhFAQgBBggrBgEFBQcDAQYIKwYBBQUH
//AwIwbQYIKwYBBQUHAQwEYTBfoV2gWzBZMFcwVRYJaW1hZ2UvZ2lmMCEwHzAHBgUr
//DgMCGgQUj+XTGoasjY5rw8+AatRIGCx7GS4wJRYjaHR0cDovL2xvZ28udmVyaXNp
//Z24uY29tL3ZzbG9nby5naWYwNAYIKwYBBQUHAQEEKDAmMCQGCCsGAQUFBzABhhho
//dHRwOi8vb2NzcC52ZXJpc2lnbi5jb20wDQYJKoZIhvcNAQEFBQADgYEADyWuSO0b
//M4VMDLXC1/5N1oMoTEFlYAALd0hxgv5/21oOIMzS6ke8ZEJhRDR0MIGBJopK90Rd
//fjSAqLiD4gnXbSPdie0oCL1jWhFXCMSe2uJoKK/dUDzsgiHYAMJVRFBwQa2DF3m6
//CPMr3u00HUSe0gST9MsFFy0JLS1j7/YmC3s=
//-----END CERTIFICATE-----
//';

		$this->filesystem->put($path, $contents);

		return($this->filesystem->get($path));

	}



} 