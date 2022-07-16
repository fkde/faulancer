<?php

namespace Faulancer\Http;

use Spatie\ArrayToXml\ArrayToXml;

class XmlResponse extends Response
{
    /**
     * @param array $body
     */
    public function __construct(array $body)
    {
        parent::__construct(200, ['Content-Type' => 'text/xml'], $this->toXml($body));
    }

    /**
     * @param array $data
     * @return string
     */
    private function toXml(array $data): string
    {
        return ArrayToXml::convert($data, [
            'rootElementName' => 'urlset',
            '_attributes' => [
                'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9'
            ]
        ]);
    }
}