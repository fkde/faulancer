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
        $xml = new \SimpleXMLElement('<?xml version="1.0"?><root></root>');
        $this->createNodes($xml, $data);
        return $xml->asXML();
    }

    private function createNodes($xml, $data)
    {
        foreach ($data as $key => $payload) {

            if (empty($key)) {
                //continue;
            }

            if (!is_array($payload)) {
                $xml->addChild($key, $payload);
                continue;
            }

            $childKey = key($payload);
            $parent = new \SimpleXMLElement(sprintf('<%s></%s>', $childKey, $childKey));
            $children = $this->createNodes($parent, $payload);
            $result = preg_replace( "/<\?xml.+?\?>/", "", $parent->asXml());
            $xml->addChild($childKey, $result);
        }

        return $xml;
    }
}