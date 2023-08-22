<?php

namespace Mavu\GlobalBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Sulu\Bundle\MediaBundle\Api\Media;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;


class BlockPathsExtension extends AbstractExtension
{


    /**
     * @var PropertyAccess
     */
    private $accessor;


    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();
    }


    public function getFilters(): array
    {
        return [
            new TwigFilter('mavu_add_block_paths', [$this, 'addBlockPaths']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('mavu_get_by_path', [$this, 'getByPath']),
        ];
    }



    public function getByPath($object, $path)
    {
        if ($object && $path) {
            return $this->accessor->getValue($object, $path);
        } else {
            return null;
        }
    }


    public function addBlockPaths($content)
    {
        $this->recursiveAddPathsInPlace($content);
        return $content;
    }

    public function recursiveAddPathsInPlace(&$content, string $path = "")
    {
        if (is_array($content)) {
            foreach ($content as $idx => &$item) {
                $item['path'] = $path . "[{$idx}]";
                foreach (["maincol", "leftcol", "middlecol", "rightcol", "items"] as $fieldname) {
                    if (array_key_exists($fieldname, $item)) {
                        $this->recursiveAddPathsInPlace($item[$fieldname], $item['path'] . "[{$fieldname}]");
                    }
                }
            }
        }
    }
}
