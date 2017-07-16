<?php

namespace Develpr\Tests\Unit;

use Carbon\Carbon;
use Develpr\AlexaApp\Request\AlexaRequest;
use Develpr\Tests\BaseTestCase;
use Mockery;

class AlexaRequestTest extends BaseTestCase
{
    /** @var AlexaRequest | Mockery\MockInterface $request */
    protected $request;

    public function setUp()
    {
        parent::setUp();
        $this->requestData = $this->intentRequestStub('GetDate', null, 'IN_PROGRESS');

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
        $this->assertNotEmpty($this->request->getUserId());
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
        $this->assertNotEmpty($this->request->getAppId());
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
        $this->assertNotEmpty($this->request->getSession());
        $this->assertEquals($expectedSession, $this->request->getSession());
    }

    /** @test */
    public function it_can_get_the_request_context()
    {
        $expectedSession = array_get($this->requestData, 'context');
        $this->assertNotEmpty($this->request->getContext());
        $this->assertEquals($expectedSession, $this->request->getContext());
    }

    /** @test */
    public function it_can_get_the_dialog_state()
    {
        $expectedState = array_get($this->requestData, 'request.dialogState');
        $this->assertNotEmpty($this->request->dialogState());
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
        $this->assertNotEmpty($this->request->getIntent());
        $this->assertEquals($expectedIntent, $this->request->getIntent());
    }

    /** @test */
    public function it_can_get_a_slot_value()
    {
        $expectedSlotValue = Carbon::now()->toIso8601String();
        array_set(
            $this->requestData,
            'request.intent.slots',
            ['Date' => ['value' => $expectedSlotValue]]
        );

        $this->request->shouldReceive('getContent')
            ->once()
            ->andReturn(json_encode($this->requestData));

        $this->assertEquals($expectedSlotValue, $this->request->slot('Date'));
    }

    /** @test */
    public function it_can_get_a_set_a_default_slot_value()
    {
        $expectedSlotValue = 'Bar';
        $this->assertNull($this->request->slot('Foo'));
        $this->assertEquals($expectedSlotValue, $this->request->slot('Foo', $expectedSlotValue));
    }

    /** @test */
    public function it_can_get_all_slots()
    {
        $slots = [
            'Date' => [
                'name' => 'Date',
                'type' => 'AMAZON.DATE',
                'confirmationStatus' => 'NONE',
            ]
        ];
        $requestData = $this->intentRequestStub('GetDate', $slots);
        $this->request->shouldReceive('getContent')
            ->once()
            ->andReturn(json_encode($requestData))
            ->byDefault();

        $this->assertNotEmpty($this->request->slots());
        $this->assertEquals($slots, $this->request->slots());
    }

    /** @test */
    public function it_can_update_a_slot_with_confirmation()
    {
        $expectedSlotValue = Carbon::now()->toIso8601String();
        $slots = [
            'Date' => [
                'name' => 'Date',
                'type' => 'AMAZON.DATE',
                'confirmationStatus' => 'NONE',
            ]
        ];
        $requestData = $this->intentRequestStub('GetDate', $slots);
        $this->request->shouldReceive('getContent')
            ->once()
            ->andReturn(json_encode($requestData))
            ->byDefault();

        $previousValue = $this->request->slot('Date');
        $this->assertNull($previousValue);

        $this->request->updateSlot('Date', $expectedSlotValue);

        $this->assertEquals($expectedSlotValue, $this->request->slot('Date'));
    }

    /** @test */
    public function test_it_can_get_the_request_timestamp()
    {
        $expectedTimestamp = strtotime(array_get($this->requestData, 'request.timestamp'));
        $this->assertEquals($expectedTimestamp, $this->request->getTimestamp());
    }

    /** @test */
    public function it_knows_if_it_has_processed_the_request()
    {
        $this->assertFalse($this->request->isProcessed());

        // do something to trigger the private method we can't directly access
        $this->request->slots();

        $this->assertTrue($this->request->isProcessed());
    }

    /** @test */
    public function it_can_set_the_prompt_response_to_true()
    {
        $this->assertFalse($this->request->isPromptResponse());
        $this->request->setPromptResponse(true);
        $this->assertTrue($this->request->isPromptResponse());
    }

    /** @test */
    public function it_can_get_confirmation_status_of_the_request()
    {
        $expectedConfirmationStatus = array_get($this->requestData, 'request.intent.confirmationStatus');
        $this->assertEquals($expectedConfirmationStatus, $this->request->getConfirmationStatus());
    }
}