<?php

namespace Develpr\Tests\Response\Directives\Dialog;

use Develpr\AlexaApp\Request\AlexaRequest;
use Develpr\AlexaApp\Response\Directives\Dialog\DialogDirective;
use Develpr\Tests\BaseTestCase;
use Mockery\MockInterface;

class DialogDirectiveTest extends BaseTestCase
{
    /** @var DialogDirective | MockInterface */
    protected $directive;

    public function setUp()
    {
        parent::setUp();
        $this->directive = \Mockery::mock(DialogDirective::class)
            ->makePartial()
            ->shouldDeferMissing();
    }

    /** @test */
    public function it_can_return_the_alexa_request()
    {
        $this->assertInstanceOf(AlexaRequest::class, $this->directive->request());
    }

    /** @test */
    public function its_arrayable()
    {
        $this->directive->shouldReceive('getType')->once()->andReturn('Foo');
        $directiveAsArray = $this->directive->toArray();
        $type = array_get($directiveAsArray, 'type', '');

        $this->assertTrue(is_array($directiveAsArray));
        $this->assertArrayHasKey('type', $directiveAsArray);
        $this->assertEquals('Foo', $type);
    }
}