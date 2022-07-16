<?php

namespace Faulancer\View\Helper;

class ShowMessage extends AbstractViewHelper
{
    /**
     * @param string $id
     *
     * @return string|null
     */
    public function __invoke(string $id): ?string
    {
        $message = $this->getSession()->getFlashMessage($id);

        if (null === $message) {
            return null;
        }

        return $this->getTranslator()->translate($message);
    }
}
