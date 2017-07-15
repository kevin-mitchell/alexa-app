<?php

namespace Develpr\Tests\Response\Directives\Dialog;

use Develpr\AlexaApp\Response\Directives\Dialog\ElicitSlot;
use Develpr\Tests\BaseTestCase;

class ElicitSlotDirectiveTest extends BaseTestCase
{
    /** @var  ElicitSlot */
    protected $directive;

    public function setUp()
    {
        parent::setUp();
        $this->directive = new ElicitSlot('FooSlot');
    }

    /** @test */
    public function it_is_an_instance_of_dialog_directive()
    {
        $this->assertInstanceOf(ElicitSlot::class, $this->directive);
    }

    /** @test */
    public function it_has_a_delegate_directive_type()
    {
        $this->assertEquals('Dialog.ElicitSlot', $this->directive->getType());
    }

    /** @test */
    public function it_has_the_correct_slot_to_confirm()
    {
        $slot = array_get($this->directive->toArray(), 'slotToElicit');
        $this->assertEquals('FooSlot', $slot);
    }
}