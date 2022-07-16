<?php

namespace Faulancer\View\Helper;

use Faulancer\Model\Role;

/**
 * Class User
 * @package KennstDes\View\Helper
 */
class User extends AbstractViewHelper
{

    /**
     * @return self
     */
    public function __invoke(): self
    {
        return $this;
    }

    /**
     * @return false
     */
    public function isLoggedIn(): bool
    {
        return null !== $this->getUsername();
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->getUser()->getCurrentUser()->name ?? null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasRole(string $name): bool
    {
        return in_array($name, $this->getUserRoles(), true);
    }

    /**
     * @return array
     */
    private function getUserRoles(): array
    {
        return array_map(function (Role $role) {
            return $role->name;
        }, $this->getUser()->getCurrentUser()->roles ?? []);
    }
}
