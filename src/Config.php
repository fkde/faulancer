<?php

namespace Faulancer;

use Assert\Assert;
use Assert\AssertionFailedException;
use Faulancer\Exception\FileNotFoundException;

class Config
{
    private const DOTENV_PATH = './../.env';
    private const DOTENV_LOCAL_PATH = './../.env.local';

    private const CONFIG_PATH = './../config';

    private array $config;

    /**
     * @throws AssertionFailedException
     * @throws FileNotFoundException
     */
    public function __construct()
    {
        $this->loadDotEnv();
        $this->loadConfig();
    }

    /**
     * @param string $namespace
     * @return string|array|object|null
     */
    public function get(string $namespace): string|array|object|null
    {
        $result = $this->config[$namespace] ?? null;

        // Load config from given namespace
        if (str_contains($namespace, ':') !== false) {
            $result = $this->resolveNamespace($namespace);
        }

        // Try to return the different config locations
        return $result ?? null;
    }

    /**
     * @param string $lang
     *
     * @return void
     */
    public function setLanguage(string $lang): void
    {
        $this->config['language'] = $lang;
    }

    /**
     * @param string $namespace
     * @return string|array|object|null
     */
    private function resolveNamespace(string $namespace): string|array|object|null
    {
        $parts   = explode(':', $namespace);
        $pointer = $this->config;

        foreach ($parts as $part) {
            $pointer = $pointer[$part] ?? null;
        }

        return $pointer;
    }

    /**
     * @return void
     * @throws AssertionFailedException
     * @throws FileNotFoundException
     */
    private function loadConfig(): void
    {
        $types     = ['app', 'routes'];
        $configDir = realpath(self::CONFIG_PATH);

        Assert::that($configDir)->directory('Could not read config directory.');

        foreach ($types as $type) {
            $configFile = sprintf('/%s.conf.php', $type);
            $configFilePath = $configDir . $configFile;

            Assert::that($configFilePath)->file();

            $contents = require $configFilePath;

            if (is_int($contents)) {
                continue;
            }

            $this->config[$type] = $contents;
        }

        $this->replaceEnvVariables();
    }

    /**
     * @return void
     */
    private function loadDotEnv(): void
    {
        $mainDotEnvFile  = realpath(self::DOTENV_PATH);
        $localDotEnvFile = realpath(self::DOTENV_LOCAL_PATH);
        $dotEnvContents  = [];
        $envFileExists = false;

        if (file_exists($mainDotEnvFile)) {
            $dotEnvMain = file_get_contents($mainDotEnvFile);
            $dotEnvContents = $this->parseDotEnv($dotEnvMain);
            $envFileExists = true;
        }

        if (file_exists($localDotEnvFile)) {
            $dotEnvLocal = file_get_contents($localDotEnvFile);
            $dotEnvContents = array_merge($dotEnvContents, $this->parseDotEnv($dotEnvLocal));
            $envFileExists = true;
        }

        Assert::that($envFileExists)->true('Could not read .env file.');

        foreach ($dotEnvContents as $key => $value) {
            putenv(sprintf('%s=%s', $key, $value));
        }
    }

    /**
     * @param string $payload
     * @return array
     */
    private function parseDotEnv(string $payload): array
    {
        $result  = [];
        $matches = [];
        preg_match_all('/([A-Z0-9_]+)="?([\w\+\-\.\\:_\/%$§]+)"?/um', $payload, $matches);

        if (empty($matches)) {
            return [];
        }

        foreach ($matches[0] as $match) {

            if (is_array($match) || false === str_contains($match, '=')) {
                continue;
            }

            $parts = explode('=', $match);
            $key = $parts[0]; $value = $parts[1];
            $result[$key] = str_replace(['\"', '"'], ['\"', ''], $value);

        }

        return $result;
    }

    /**
     * @return void
     */
    private function replaceEnvVariables(): void
    {
        array_walk_recursive($this->config, static function (&$item) {
            if (str_starts_with($item, 'ENV:')) {
                $item = getenv(substr($item, 4));
            }
        });
    }
}
