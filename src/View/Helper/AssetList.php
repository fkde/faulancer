<?php

namespace Faulancer\View\Helper;

class AssetList extends AbstractViewHelper
{
    /**
     * @param string $type
     * @param bool   $optimize
     * @return string
     */
    public function __invoke(string $type, bool $optimize = false): string
    {
        $result  = '';

        $pattern = match ($type) {
            'js'  => '<script src="%s"></script>',
            'css' => '<link rel="stylesheet" type="text/css" href="%s">',
            default => '',
        };

        /** @var array $files */

        $files = $this->getRenderer()->getVariable('assets' . ucfirst($type));

        if (empty($files)) {
            return '';
        }

        if ($type === 'css' && $optimize) {
            $result  = '<style type="text/css">';
            $result .= $this->collectAssetsContent($files, $type);
            $result .= '</style>';
            return $result;
        }

        if ($type === 'js' && $optimize) {
            $result  = '<script>';
            $result .= $this->collectAssetsContent($files, $type);
            $result .= '</script>';
            return $result;
        }

        foreach ($files as $file) {
            $result .= sprintf($pattern, $file) . "\n";
        }


        return $result;
    }
    /**
     * Collect all assets content for concatenation
     *
     * @param array  $files The asset files
     * @param string $type  The assets type
     *
     * @return string
     */
    private function collectAssetsContent(array $files, string $type): string
    {
        $docRoot = dirname(__DIR__, 3) . '/public';
        $content  = '';
        $contents = [];
        foreach ($files as $file) {
            if (file_exists($docRoot . $file)) {
                $contents[] = file_get_contents($docRoot . $file);
            }
        }
        if ($type === 'css') {
            $content = str_replace(
                ["\n", "\t", "  ", ": ", " {", "{ ", " }", ";}"],
                ["", "", "", ":", "{", "{", "}", "}"],
                implode('', $contents)
            );
        }
        return $content;
    }
}
