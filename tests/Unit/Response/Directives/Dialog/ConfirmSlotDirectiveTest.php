<?php

namespace Develpr\Tests\Response\Directives\Dialog;

use Develpr\AlexaApp\Response\Directives\Dialog\ConfirmSlot;
use Develpr\Tests\BaseTestCase;

class ConfirmSlotDirectiveTest extends BaseTestCase
{
    /** @var  ConfirmSlot */
    protected $directive;

    public function setUp()
    {
        parent::setUp();
        $this->directive = new ConfirmSlot('FooSlot');
    }

    /** @test */
    public function it_is_an_instance_of_dialog_directive()
    {
        $this->assertInstanceOf(ConfirmSlot::class, $this->directive);
    }

    /** @test */
    public function it_has_a_confirm_intent_type()
    {
        $this->assertEquals('Dialog.ConfirmSlot', $this->directive->getType());
    }

    /** @test */
    public function it_has_the_correct_slot_to_confirm()
    {
        $slot = array_get($this->directive->toArray(), 'slotToConfirm');
        $this->assertEquals('FooSlot', $slot);
    }
}