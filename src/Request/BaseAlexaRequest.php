<?php  namespace Develpr\AlexaApp\Request; 

use Illuminate\Http\Request;

abstract class BaseAlexaRequest implements AlexaRequest
{
    private $data = [];

    function __construct(Request $request)
    {
        $this->data = $this->extractRequestData($request);

        $this->setupRequest($this->data);

    }

    /**
     * Return an array of
     *
     * @param array $data
     * @return mixed
     */
    protected abstract function setupRequest(array $data);

    /**
     * @param $request
     */
    private function extractRequestData($request)
    {
        //todo: remove this after testing
		$data = json_decode($request->getContent(), true);

        return $data;
    }

    /**
     * returns the request type, i.e. IntentRequest
     *
     * @return string
     */
    public function getRequestType()
    {
        return array_get($this->data, 'request.type');
    }

	/**
	 * Is this request formatted as an Amazon Echo/Alexa request?
	 *
	 * @return bool
	 */
	public function isAlexaRequest()
	{
		return !(is_null($this->getRequestType()));
	}

	/**
	 * Return the user id provided in in the request
	 *
	 * @return mixed
	 */
	public function getUserId()
	{
		return array_get($this->data, 'session.user.userId');
	}

} 
