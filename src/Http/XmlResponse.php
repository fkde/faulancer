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
        $xml = new \SimpleXMLElement('<?xml version="1.0"?><urlset></urlset>');
        $this->createNodes($xml, $data);
        return html_entity_decode($xml->asXML());
    }

    private function createNodes(\SimpleXMLElement $xml, $data)
    {
        foreach ($data as $key => $payload) {

            if (is_string($payload) && !empty($payload)) {
                $xml->addChild(html_entity_decode($key), $payload);
                continue;
            }

            // sequential array
            if (is_array($payload) && count($payload) && is_numeric($key)) {
                $this->createNodes($xml, $payload);
                continue;
            }

            if (is_array($payload) && count($payload) && !is_numeric($key)) {
                $parent = new \SimpleXMLElement(sprintf('<%s></%s>', $key, $key));
                $list = $this->createNodes($parent, $payload);
                $result = preg_replace( '/<\?xml.+?\?>/', '', $list->asXml());
                $result = preg_replace('/<' . $key . '>(.*)<\/' . $key . '>/', '$1', $result);
                $xml->addChild($key, $result);
                continue;
            }

        }

        return $xml;
    }
}