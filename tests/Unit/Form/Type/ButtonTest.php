<?php

namespace Faulancer\Tests\Unit\Form\Type;

use Prophecy\Argument;
use Faulancer\Form\Type\Button;
use PHPUnit\Framework\TestCase;
use Faulancer\Service\Translator;
use Prophecy\PhpUnit\ProphecyTrait;
use Faulancer\Exception\FrameworkException;

class ButtonTest extends TestCase
{

    use ProphecyTrait;

    /**
     * @test
     * @return void
     * @throws FrameworkException
     */
    public function itShouldRenderButtonSuccessfully(): void
    {
        $translatorMock = $this->prophesize(Translator::class);
        $translatorMock->translate(Argument::type('string'))->willReturnArgument();

        $button = new Button([
            'name' => 'testbutton',
            'text' => 'Test'
        ]);

        $button->setTranslator($translatorMock->reveal());

        $this->assertSame('<button name="testbutton">Test</button>', $button->render());
    }

    /**
     * @test
     * @return void
     * @throws FrameworkException
     */
    public function itShouldRenderButtonWithAdditionalAttributesSuccessfully(): void
    {
        $translatorMock = $this->prophesize(Translator::class);
        $translatorMock->translate(Argument::type('string'))->willReturnArgument();

        $button = new Button([
            'name' => 'testbutton',
            'text' => 'Test',
            'data-custom' => 'test'
        ]);

        $button->setTranslator($translatorMock->reveal());

        $this->assertSame('<button name="testbutton" data-custom="test">Test</button>', $button->render());
    }

}