<?php

namespace Faulancer\View;

use Faulancer\Config;
use Faulancer\Exception\ContainerException;
use Faulancer\Exception\FrameworkException;
use Faulancer\Initializer;
use Faulancer\Exception\NotFoundException;
use Faulancer\Exception\TemplateException;
use Faulancer\Exception\ViewHelperException;
use Faulancer\Service\Aware\ConfigAwareTrait;
use Faulancer\Service\Aware\EnvironmentAwareInterface;
use Faulancer\Service\Aware\EnvironmentAwareTrait;
use Faulancer\Service\Aware\LoggerAwareTrait;
use Faulancer\Service\Environment;
use Faulancer\View\Helper\AbstractViewHelper;
use Faulancer\Exception\FileNotFoundException;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Aware\ConfigAwareInterface;

class Renderer implements LoggerAwareInterface, ConfigAwareInterface, EnvironmentAwareInterface
{
    use LoggerAwareTrait;
    use ConfigAwareTrait;
    use EnvironmentAwareTrait;

    private Config $config;

    private string $template = '';

    private array $variables = [];

    private Renderer|null $parentView = null;

    /**
     * Set template for this view
     *
     * @param string $template
     * @return self
     *
     * @throws FileNotFoundException
     */
    public function setTemplate(string $template = ''): self
    {
        $templatePath = realpath($this->config->get('app:template:path'));

        $templateFile = ltrim($template, DIRECTORY_SEPARATOR);
        $template     = rtrim($templatePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $templateFile;

        if (empty($template) || !file_exists($template) || is_dir($template)) {
            throw new FileNotFoundException('Template "' . $template . '" not found');
        }

        $this->logger->debug('Template "' . $template . '" found.');

        $this->template = $template;

        return $this;
    }

    /**
     * Add javascript from outside
     *
     * @param string $file
     * @return self
     */
    public function addScript(string $file): self
    {
        $this->variables['assetsJs'][] = $file;
        return $this;
    }

    /**
     * Add stylesheet from outside
     *
     * @param string $file
     * @return self
     */
    public function addStylesheet(string $file): self
    {
        $this->variables['assetsCss'][] = $file;
        return $this;
    }

    /**
     * Return current template
     *
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * Set a single variable
     *
     * @param string       $key
     * @param string|array|object|null $value
     */
    public function setVariable(string $key = '', string|array|object|null $value = ''): void
    {
        $this->variables[$key] = $value;
    }

    /**
     * Get a single variable
     *
     * @param string $key
     * @return string|array|object
     */
    public function getVariable(string $key): string|array|object
    {
        return $this->variables[$key] ?? '';
    }

    /**
     * Check if variable exists
     *
     * @param string $key
     * @return bool
     */
    public function hasVariable(string $key): bool
    {
        if (isset($this->variables[$key])) {
            return true;
        }

        return false;
    }

    /**
     * Set many variables at once
     *
     * @param array $variables
     * @return self
     */
    public function setVariables(array $variables = []): self
    {
        foreach ($variables as $key => $value) {
            $this->setVariable($key, $value);
        }

        return $this;
    }

    /**
     * Get all variables
     *
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Define parent template
     *
     * @param Renderer $view
     */
    public function setParentView(Renderer $view): void
    {
        $this->logger->debug('Add layout for template "' . $this->getTemplate() . '".');
        $this->parentView = $view;
    }

    /**
     * Get parent template
     *
     * @return Renderer|null
     */
    public function getParentView(): ?Renderer
    {
        return $this->parentView;
    }

    /**
     * Strip spaces and tabs from output
     *
     * @param $output
     * @return string
     */
    private function normalizeOutput($output): string
    {
        $output = str_replace('> <', '><', trim($output));

        if (getenv('APPLICATION_ENV') === 'production') {
            $this->logger->debug('Compressing output for production environment.');
            return preg_replace('/(\s{2,}|\t|\r|\n)/', ' ', $output);
        }

        // Dev environment
        $this->logger->debug('Remove unnecessary spaces and tabs from output.');
        return str_replace(["\t", "\t", "\r", "\n\n"], " ", $output);
    }

    /**
     * Render the current view
     *
     * @return string
     *
     * @throws TemplateException
     * @throws FileNotFoundException
     * @throws ViewHelperException
     */
    public function render(): string
    {
        try {
            $content = '';

            extract($this->variables, EXTR_OVERWRITE);

            $this->logger->debug('Opening output buffering for template "' . $this->template . '"');
            ob_start();

            include $this->getTemplate();
            $content = ob_get_contents();

            ob_end_clean();
            $this->logger->debug('Cleared output buffer for template "' . $this->template . '"');

        } catch (ViewHelperException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            throw $e;
        } catch (\Error $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            throw $e;
        } finally {
            $this->clearOutputBuffer();
        }

        if ($this->getParentView() instanceof Renderer) {
            return $this->normalizeOutput($this->getParentView()->setVariables($this->getVariables())->render());
        }

        return $this->normalizeOutput($content);
    }

    /**
     * @return void
     */
    private function clearOutputBuffer(): void
    {
        while (ob_get_level() > 1) {
            $this->logger->debug('Clean output buffer.');
            ob_end_clean();
       }
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed|void
     * @throws ContainerException
     * @throws FrameworkException
     * @throws NotFoundException
     * @throws ViewHelperException
     */
    public function __call(string $name, array $arguments)
    {
        $viewHelper = sprintf('%s\Helper\%s', __NAMESPACE__, ucfirst($name));

        if (false === class_exists($viewHelper)) {
            throw new ViewHelperException(sprintf('No view helper for "%s" found.', $name));
        }

        try {

            /** @var AbstractViewHelper $class */
            $class = Initializer::load($viewHelper, [$this, $this->config]);

            return call_user_func_array([$class, '__invoke'], $arguments);

        } catch (FrameworkException $t) {
            $this->getLogger()->error($t->getMessage(), ['exception' => $t]);
            if ($this->getEnvironment()->get() === Environment::DEVELOPMENT) {
                throw $t;
            }
        }
    }

    /**
     * @return void
     */
    public function reset(): void
    {
        unset($this->variables, $this->template);
    }

    public function __destruct()
    {
        $this->reset();
    }
}
