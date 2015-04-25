<?php  namespace Develpr\AlexaApp\Request; 

class IntentRequest extends BaseAlexaRequest
{
    private $intent = '';

    protected function setupRequest(array $data)
    {
        $this->intent = array_get($data, 'request.intent.name');
    }

    /**
     * @return string
     */
    public function getIntent()
    {
        return $this->intent;
    }



} 
