<?php

namespace Faulancer\Form;

use Assert\Assert;
use JetBrains\PhpStorm\Pure;
use Faulancer\Service\Aware\SessionAwareTrait;
use Faulancer\Service\Aware\TranslatorAwareTrait;
use Faulancer\Service\Aware\SessionAwareInterface;
use Faulancer\Service\Aware\TranslatorAwareInterface;

abstract class AbstractType implements FormTypeInterface, TranslatorAwareInterface, SessionAwareInterface
{
    use TranslatorAwareTrait;
    use SessionAwareTrait;

    private const PATTERN_ERROR_MESSAGE = '<div class="error-message" data-type="%s" data-name="%s">%s</div>';

    protected FormBuilderInterface $form;

    protected string $name;

    protected ?string $label;

    protected ?string $value;

    protected array $definition;

    protected array $validators;

    protected array $errorMessages;

    private string $uniqid;

    /**
     * AbstractType constructor.
     *
     * @param array $definition
     * @param array $validators
     */
    public function __construct(array $definition, array $validators = [])
    {
        Assert::that($definition)->notEmptyKey('name');

        $this->name       = $definition['name'];
        $this->definition = $definition;
        $this->validators = $validators;
        $this->label      = $definition['label'] ?? null;
        $this->value      = $definition['value'] ?? null;
        $this->uniqid     = uniqid();
    }

    /**
     * @param FormBuilderInterface $form
     */
    public function setForm(FormBuilderInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return FormBuilderInterface
     */
    public function getForm(): FormBuilderInterface
    {
        return $this->form;
    }

    public function getLabel(): ?string
    {
        return '<label for="' . $this->uniqid . '">' . $this->getTranslator()->translate($this->label) . '</label>';
    }

    /**
     * @return string
     */
    protected function getUniqueId(): string
    {
        return $this->uniqid;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $translator = $this->getTranslator();

        foreach ($this->validators as $validator) {
            if (false === class_exists($validator)) {
                continue;
            }

            /** @var FormValidatorInterface $validatorObj */
            $validatorObj = new $validator($this, $this->session);

            if (false === $validatorObj->exec($this->getValue())) {
                $this->definition['class'] = $this->definition['class'] ?? null . ' error';
                $this->definition['data-error'] = $translator->translate($validatorObj->getErrorMessage());
                $this->errorMessages[] = $translator->translate($validatorObj->getErrorMessage());
            }

        }

        return empty($this->errorMessages);
    }

    /**
     * @return string
     */
    #[Pure] public function getErrorMessages(): string
    {
        $output = '';

        if (empty($this->errorMessages)) {
            return $output;
        }

        foreach ($this->errorMessages as $message) {
            $output .= sprintf(
                self::PATTERN_ERROR_MESSAGE,
                $this->definition['type'] ?? 'unknown',
                $this->getName(),
                $message
            );
        }

        return $output;
    }

    /**
     * @return string
     */
    abstract public function render(): string;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}