<?php

namespace Develpr\Tests\Unit;

use Develpr\AlexaApp\Request\AlexaRequest;
use Develpr\Tests\BaseTestCase;
use Mockery;

class AlexaRequestTest extends BaseTestCase
{
    /** @var AlexaRequest | Mockery\MockInterface $request */
    protected $request;

    /** @var array $requestData */
    protected $requestData;

    public function setUp()
    {
        parent::setUp();
        $this->requestData = $this->intentRequestMock();

        // Partial mock the request object so we can fake the request data
        $this->request = Mockery::mock('Develpr\AlexaApp\Request\AlexaRequest[getContent]');

        $this->request->shouldReceive('getContent')
            ->once()
            ->andReturn(json_encode($this->requestData))
            ->byDefault();
    }

    /** @test */
    public function it_can_get_the_request_type()
    {
        $this->assertEquals('IntentRequest', $this->request->getRequestType());
    }

    /** @test */
    public function it_can_determine_if_the_request_is_an_alexa_request()
    {
        $this->assertTrue($this->request->isAlexaRequest());
    }

    /** @test */
    public function it_can_determine_if_the_request_is_a_new_session()
    {
        $this->assertTrue($this->request->isNewSession());
    }

    /** @test */
    public function it_can_get_the_intent_from_the_prompt_response()
    {
        array_set($this->requestData, 'session.attributes.original_prompt_intent', 'FooIntent');

        $this->request->shouldReceive('getContent')
            ->once()
            ->andReturn(json_encode($this->requestData));

        $this->assertEquals('FooIntent', $this->request->getPromptResponseIntent());
    }

    /** @test */
    public function it_can_get_the_user_id_from_the_request()
    {
        $expectedId = array_get($this->requestData, 'session.user.userId');
        $this->assertEquals($expectedId, $this->request->getUserId());
    }

    /** @test */
    public function it_can_get_the_access_token_from_the_request()
    {
        $token = uniqid();
        array_set($this->requestData, 'session.user.accessToken', $token);

        $this->request->shouldReceive('getContent')
            ->once()
            ->andReturn(json_encode($this->requestData));

        $this->assertEquals($token, $this->request->getAccessToken());
    }

    /** @test */
    public function it_can_get_the_app_id()
    {
        $expectedId = array_get($this->requestData, 'session.application.applicationId');
        $this->assertEquals($expectedId, $this->request->getAppId());
    }

    /** @test */
    public function it_can_determine_if_session_is_present()
    {
        $this->assertTrue($this->request->hasSession());
    }

    /** @test */
    public function it_can_get_the_session_from_the_request()
    {
        $expectedSession = array_get($this->requestData, 'session.attributes');
        $this->assertEquals($expectedSession, $this->request->getSession());
    }

    /** @test */
    public function it_can_get_the_request_context()
    {
        $expectedSession = array_get($this->requestData, 'context');
        $this->assertEquals($expectedSession, $this->request->getContext());
    }

    /** @test */
    public function it_can_get_the_dialog_state()
    {
        $expectedState = array_get($this->requestData, 'request.dialogState');
        $this->assertEquals($expectedState, $this->request->dialogState());
    }

    /** @test */
    public function it_can_get_a_session_value()
    {
        array_set($this->requestData, 'session.attributes.foo', 'bar');
        $this->request->shouldReceive('getContent')
            ->once()
            ->andReturn(json_encode($this->requestData));

        $this->assertEquals('bar', $this->request->getSessionValue('foo'));
    }

    /** @test */
    public function it_can_get_the_intent_name()
    {
        $expectedIntent = array_get($this->requestData, 'request.intent.name');
        $this->assertEquals($expectedIntent, $this->request->getIntent());
    }
}