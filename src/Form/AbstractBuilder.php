<?php

namespace Faulancer\Form;

use Assert\Assert;
use Faulancer\Initializer;
use Faulancer\Form\Type\Csrf;
use Psr\Http\Message\RequestInterface;
use Faulancer\Service\Aware\LoggerAwareTrait;
use Faulancer\Service\Aware\SessionAwareTrait;
use Faulancer\Exception\NotFoundException;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Aware\SessionAwareInterface;
use Faulancer\Form\Validator\Csrf as CsrfValidator;

abstract class AbstractBuilder implements FormBuilderInterface, LoggerAwareInterface, SessionAwareInterface
{
    use LoggerAwareTrait;
    use SessionAwareTrait;

    private array $formAttributes = [
        'action'  => '',
        'method'  => '',
        'enctype' => ''
    ];

    /** @var AbstractType[] */
    private array $fields = [];

    private RequestInterface $request;

    private ?array $data;

    private string $formId;

    /**
     * AbstractBuilder constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
        $this->formId = md5(get_called_class());
    }

    /**
     * @return $this
     * @throws NotFoundException
     */
    public function build(): self
    {
        $this->create($this->request);

        if ($this->request->getMethod() === 'GET') {
            $this->getSession()->delete('csrf_' . $this->getId());
            $this->appendCsrfToken();
        }

        $this->setFormAttributes($this->request->getUri()->getPath());

        if ($this->request->getMethod() === 'POST') {
            parse_str($this->request->getBody()->getContents(), $this->data);

            foreach ($this->data as $name => $value) {
                if ('csrf' === $name) {
                    // Needs to be filled separately on post request
                    $this->fields['csrf'] = Initializer::load(
                        Csrf::class,
                        [
                            ['name' => 'csrf', 'value' => $value],
                            [CsrfValidator::class]
                        ]
                    );
                }

                if (!$this->fields[$name]) {
                    continue;
                }

                $this->fields[$name]->setValue($value);
            }
        }

        return $this;
    }

    /**
     * @param string $type
     * @param array  $definition
     * @param array  $validators
     *
     * @throws NotFoundException
     */
    protected function add(string $type, array $definition, array $validators = [])
    {
        Assert::that($definition)->notEmptyKey('name', 'Attribute `name` is missing.');
        $this->fields[$definition['name']] = Initializer::load($type, [$definition, $validators]);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->formId;
    }

    /**
     * @return string
     */
    public function open(): string
    {
        return sprintf(
            '<form method="%s" action="%s" enctype="%s">',
            $this->formAttributes['method'],
            $this->formAttributes['action'] ?? null,
            $this->formAttributes['enctype']
        );
    }

    /**
     * @return string
     */
    public function close(): string
    {
        return $this->getField('csrf') . PHP_EOL . '</form>';
    }

    /**
     * @return bool
     * @throws NotFoundException
     */
    protected function appendCsrfToken(): bool
    {
        if ($this->session->get('csrf_' . $this->formId)) {
            return false;
        }

        $token = hash('sha256', uniqid());

        $this->add(Csrf::class, [
            'value' => $token,
            'name'  => 'csrf'
        ]);

        $this->session->set('csrf_' . $this->formId, $token);

        return true;
    }

    /**
     * @param string      $action
     * @param string|null $method
     * @param string|null $enctype
     */
    protected function setFormAttributes(
        string $action,
        ?string $method = 'post',
        ?string $enctype = 'application/x-www-form-urlencoded'
    ) {
        $this->formAttributes['action']  = $action;
        $this->formAttributes['method']  = $method;
        $this->formAttributes['enctype'] = $enctype;
    }

    /**
     * @param string $name
     * @return AbstractType|null
     */
    public function getField(string $name): ?AbstractType
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * @return bool
     */
    public function isSubmitted(): bool
    {
        return 'POST' === $this->request->getMethod();
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $result = array_map(function (AbstractType $field) {
            $field->setForm($this);
            return $field->isValid();
        }, $this->fields);

        return !in_array(false, $result, true);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param RequestInterface $request
     *
     * @return void
     */
    abstract public function create(RequestInterface $request);

}