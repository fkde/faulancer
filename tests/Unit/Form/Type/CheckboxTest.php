<?php

namespace Faulancer\Tests\Unit\Form\Type;

use Prophecy\Argument;
use PHPUnit\Framework\TestCase;
use Faulancer\Form\Type\Checkbox;
use Faulancer\Service\Translator;
use Prophecy\PhpUnit\ProphecyTrait;
use Faulancer\Exception\FrameworkException;

class CheckboxTest extends TestCase
{

    use ProphecyTrait;

    /**
     * @test
     * @return void
     * @throws FrameworkException
     */
    public function itShouldRenderCheckboxSuccessfully(): void
    {
        $translatorMock = $this->prophesize(Translator::class);
        $translatorMock->translate(Argument::type('string'))->willReturnArgument();

        $button = new Checkbox([
            'name' => 'test_checkbox',
        ]);

        $button->setTranslator($translatorMock->reveal());

        $this->assertMatchesRegularExpression('/<input id="[a-z0-9]+" name="test_checkbox" type="checkbox" \/>/', $button->render());
    }

}