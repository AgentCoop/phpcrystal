<?php
namespace PhpCrystal\Core\Services\Base;

use Doctrine\Common\Annotations\TokenParser;

class PhpParser extends TokenParser
{
    /**
     * @return string
     */
    public static function toPhpArray($elements)
    {
        $phpArray = '[';

        array_map(function($el) use(&$phpArray) {
            $phpArray .= (is_string($el) ? ('\'' . $el . '\'') : $el) . ',';
        }, $elements);

        $phpArray = rtrim($phpArray, ',');
        $phpArray .= ']';

        return $phpArray;
    }

    /**
     * @return static
     */
    public static function loadFromFile($filename)
    {
        $content = file_get_contents($filename);

        return new static($content);
    }

    /**
     * @return static
     */
    public static function loadFromString($phpSource)
    {
        return new static('<?php ' . $phpSource);
    }

    /**
     * Extracts class namespace
     *
     * @return string
     */
    public function extractNamespace()
    {
        $nsDeclStart = false;
        $name = '';

        while (($token = $this->next())) {
            if ($token[0] === T_NAMESPACE) {
                $nsDeclStart = true;
            } else if ($nsDeclStart) {
                if ($token[0] === T_STRING || $token[0] === T_NS_SEPARATOR) {
                    $name .= $token[1];
                } else {
                    break;
                }
            }
        }

        return $name;
    }

    /**
     * Extracts fully-qualified class name
     *
     * @return string
     */
    public function extractClassName()
    {
        $namespace = $this->extractNamespace();

        while (($token = $this->next())) {
            if ($token[0] === T_CLASS) {
                return $namespace . '\\' . $this->next()[1];
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function extractInterface()
    {
        $interface = $this->extractNamespace();

        while (($token = $this->next())) {
            if ($token[0] === T_INTERFACE) {
                return $interface . '\\' . $this->next()[1];
            }
        }

        return null;
    }
}
