<?php

namespace Develpr\Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected $requestData = [
        "version" => "1.0",
        "session" => [],
        "request" => [],
        "context" => [],
    ];

    /**
     * @param string $intent
     * @param array $slots
     * @param string $dialogState
     * @param array $confirmationStatus
     *
     * @return array
     */
    protected function intentRequestStub($intent = 'GetDate', $slots = null, $dialogStatus = null, $confirmationStatus = null)
    {
        $this->requestData['request'] = $this->requestStub(
            'EdwRequestId.0000000-0000-0000-0000-000000000000',
            'IntentRequest',
            [
                'name' => $intent,
                'slots' => $slots ?: [],
            ],
            $confirmationStatus,
            $dialogStatus,
            'en-US'
        );

        $this->requestData['session'] = $this->sessionStub();
        $this->requestData['context'] = $this->contextStub();

        return $this->requestData;
    }

    /**
     * @param string $sessionId
     * @param array $userData
     * @param array $appData
     * @param array $attributes
     * @param bool $newSession
     * @return array
     */
    public function sessionStub(
        $sessionId = 'SessionId.0000000-0000-0000-0000-000000000000',
        $userData = ['userId' => 'amzn1.ask.account.000000000000000000'],
        $appData = ['applicationId' => 'amzn1.ask.skill.0000000-0000-0000-0000-000000000000'],
        $attributes = ['Bazz' => 'Buzz'],
        $newSession = true
    )
    {
        return [
            'sessionId' => $sessionId,
            'application' => $appData,
            'attributes' => $attributes,
            'user' => $userData,
            'new' => $newSession,
        ];
    }

    /**
     * @param string $requestId
     * @param string $requestType
     * @param array $intent
     * @param bool $dialogState
     * @param string $dialogStatus
     * @param null $timestamp
     * @param string $locale
     * @return array
     */
    public function requestStub(
        $requestId = 'EdwRequestId.0000000-0000-0000-0000-000000000000',
        $requestType = 'IntentRequest',
        $intent = [
            'name' => 'GetDate',
            'slots' => [],
        ],
        $confirmationStatus = null,
        $dialogStatus = null,
        $timestamp = null,
        $locale = 'en-US'
    )
    {
        $requestData = [
            'type' => $requestType,
            'requestId' => $requestId,
            'locale' => $locale,
            'timestamp' => $timestamp ?: Carbon::now()->toIso8601String(),
            'intent' => $intent,
            'confirmationStatus' => $confirmationStatus ?: 'NONE',
        ];

        if(!empty($dialogStatus)) {
            $requestData['dialogState'] = $dialogStatus;
        }

        return $requestData;
    }

    public function contextStub()
    {
        $context = [
            'System' => [
                'application' => [
                    'applicationId' => 'amzn1.ask.skill.0000000-0000-0000-0000-000000000000'
                ],
                'user' => [
                    'userId' => 'amzn1.ask.account.000000000000000000',
                    'permissions' => [
                        'consentToken' => uniqid()
                    ],
                    'accessToken' => uniqid()
                ],
                'device' => [
                    'deviceId' => uniqid(),
                    'supportedInterfaces' => [
                        'AudioPlayer' => []
                    ]
                ],
                'apiEndpoint' => '/foo'
            ],
            'AudioPlayer' => [
                'token' => uniqid(),
                'offsetInMilliseconds' => 0,
                'playerActivity' => 'PAUSE'
            ]
        ];


        return $context;
    }
}

