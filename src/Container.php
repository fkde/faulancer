<?php

namespace Faulancer;

use Psr\Container\ContainerInterface;
use Faulancer\Exception\NotFoundException;
use Faulancer\Exception\ContainerException;

class Container implements ContainerInterface
{

    private array $items;

    /**
     * @param string $id
     * @return mixed|void
     *
     * @throws NotFoundException
     */
    public function get(string $id)
    {
        $item = $this->items[$id] ?? null;

        if (null === $item) {
            throw new NotFoundException(
                sprintf(
                    'Requested item "%s" not found in Container. Class: %s',
                    $id,
                    get_called_class()
                )
            );
        }

        return $this->items[$id];
    }

    /**
     * @param string $id
     * @param object $object
     * @param array  $aliases
     *
     * @throws ContainerException
     */
    public function set(string $id, object $object, array $aliases = [])
    {
        $aliases[] = $id;

        foreach ($aliases as $alias) {
            if ($this->has($alias)) {
                throw new ContainerException(sprintf('Item "%s" is already defined.', $id));
            }

            $this->items[$alias] = $object;
        }
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($items[$id]);
    }
}
