<?php

namespace Develpr\Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    /**
     * @param string $intent
     * @param array $slots
     * @param array $confirmationStatus
     *
     * @return array
     */
    protected function intentRequestMock($intent = 'GetDate', $slots = null, $confirmationStatus = null)
    {
        $requestData = ["version" => "1.0"];

        $requestData['session'] = [
            'sessionId' => 'SessionId.0000000-0000-0000-0000-000000000000',
            'application' => [
                'applicationId' => 'amzn1.ask.skill.0000000-0000-0000-0000-000000000000',
            ],
            'attributes' => ['Bazz' => 'Buzz'],
            'user' => [
                'userId' => 'amzn1.ask.account.000000000000000000',
            ],
            'new' => true,
        ];

        $requestData['context'] = [
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


        $requestData['request'] = [
            'type' => 'IntentRequest',
            'requestId' => 'EdwRequestId.0000000-0000-0000-0000-000000000000',
            'locale' => 'en-US',
            'timestamp' => Carbon::now()->toIso8601String(),
            'intent' => [
                'name' => $intent,
                'slots' => $slots ?: [
                    'Date' => [
                        'name' => 'Date',
                        'type' => 'AMAZON.DATE',
                        'confirmationStatus' => $confirmationStatus ?: 'NONE',
                    ]
                ],
                'confirmationStatus' => 'NONE',
            ],
            'dialogState' => "STARTED",
        ];

        return $requestData;
    }
}

