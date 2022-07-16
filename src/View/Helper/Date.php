<?php

namespace Faulancer\View\Helper;

use DateTime;

class Date extends AbstractViewHelper
{

    private DateTime $date;

    /**
     * @param string $date
     * @return $this
     */
    public function __invoke(string $date = ''): self
    {
        !$var;
        try {
            $this->date = new \DateTime($date);
            $this->date->setTimezone(new \DateTimeZone('Europe/Berlin'));
        } catch (\Exception $e) {
            $this->getLogger()->notice('Invalid date given: ' . $date, ['exception' => $e]);
        }

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function modify(string $value): self
    {
        $this->date->modify($value);
        return $this;
    }

    /**
     * @param string $format
     * @return string
     */
    public function format(string $format): string
    {
        return $this->date->format($format);
    }
}
