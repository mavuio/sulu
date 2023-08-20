<?php

namespace Mavu\GlobalBundle\Twig;

use Twig\TwigFilter;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Symfony\Component\PropertyAccess\PropertyAccess;


class AccessorExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('mavu_recursive_merge', [$this, 'recursiveMergeFilter']),
            new TwigFilter('mavu_set', [$this, 'setFilter']),
            new TwigFilter('mavu_add', [$this, 'addFilter']),
        ];
    }


    /**
     * Recursively merges an array into the element, replacing existing values.
     *
     * @code
     * {{ form|recursive_merge( {'element': {'attributes': {'placeholder': 'Label'}}} ) }}
     * @endcode
     *
     * @param array|iterable|\Traversable $element
     *   The parent renderable array to merge into.
     * @param iterable|array $array
     *   The array to merge.
     *
     * @return array
     *   The merged renderable array.
     *
     * @throws \Twig\Error\RuntimeError
     *   When $element is not an array or "Traversable".
     */
    public static function recursiveMergeFilter($element, $array): array
    {
        if (!twig_test_iterable($element)) {
            throw new RuntimeError(sprintf('The recursive_merge filter only works on arrays or "Traversable" objects, got "%s".', gettype($element)));
        }

        return array_replace_recursive($element, $array);
    }

    /**
     * Sets a deeply-nested property on an array.
     *
     * If the deeply-nested property exists, the existing data will be replaced
     * with the new value.
     *
     * @code
     * {{ form|set( 'element.#attributes.placeholder', 'Label' ) }}
     * @endcode
     *
     * Or using named arguments:
     * @code
     * {{ form|set( at='element.#attributes.placeholder', value='Label' ) }}
     * @endcode
     *
     * @param array|iterable|\Traversable $element
     *   The parent renderable array to set into.
     * @param string $at
     *   The dotted-path to the deeply nested element to set.
     * @param mixed $value
     *   The value to set.
     *
     * @return array
     *   The merged renderable array.
     *
     * @throws \Twig\Error\RuntimeError
     *   When $element is not an array or "Traversable".
     */
    public static function setFilter($element, string $at, $value)
    {
        if (!twig_test_iterable($element)) {
            throw new RuntimeError(sprintf('The "set" filter only works on arrays or "Traversable" objects, got "%s".', gettype($element)));
        }

        return self::addOrSetFilter($element, $at, $value);
    }

    /**
     * Adds a deeply-nested property on an array.
     *
     * If the deeply-nested property exists, the existing data will be replaced
     * with the new value, unless the existing data is an array. In which case,
     * the new value will be merged into the existing array.
     *
     * @code
     * {{ form|add( 'element.#attributes.class', 'new-class' ) }}
     * @endcode
     *
     * Or using named arguments:
     * @code
     * {{ form|add( at='element.#attributes.class', value='new-class' ) }}
     * {# We accept the plural form of "values" as a grammatical convenience. #}
     * {{ form|add( at='element.#attributes.class', values=['new-class', 'new-class-2'] ) }}
     * @endcode
     *
     * @param array|iterable|\Traversable $element
     *   The parent renderable array to merge into.
     * @param string $at
     *   The dotted-path to the deeply nested element to modify.
     * @param mixed $value
     *   The value to add.
     * @param mixed $values
     *   The values to add. If this named argument is used, the "value" argument
     *   is ignored.
     *
     * @return array
     *   The merged renderable array.
     *
     * @throws \Twig\Error\RuntimeError
     *   When $element is not an array or "Traversable".
     */
    public static function addFilter($element, string $at, $value = NULL, $values = NULL)
    {
        if (!twig_test_iterable($element)) {
            throw new RuntimeError(sprintf('The "add" filter only works on arrays or "Traversable" objects, got "%s".', gettype($element)));
        }

        return self::addOrSetFilter($element, $at, !is_null($values) ? $values : $value, TRUE);
    }

    /**
     * Helper function for the set/add filters.
     *
     * @param array|iterable|\Traversable $element
     *   The parent renderable array to merge into.
     * @param string $at
     *   The dotted-path to the deeply nested element to replace.
     * @param mixed $value
     *   The value to set.
     * @param bool $isAddFilter
     *   Which filter is being called.
     *
     * @return array
     *   The merged renderable array.
     */
    protected static function addOrSetFilter($element, string $at, $value, $isAddFilter = FALSE)
    {
        if ($element instanceof \ArrayAccess) {
            $filteredElement = clone $element;
        } else {
            $filteredElement = $element;
        }

        // Convert the dotted path into an array of keys.
        $path = explode('.', $at);
        $lastPath = array_pop($path);

        // Traverse the element down the path, creating arrays as needed.
        $childElement = &$filteredElement;
        foreach ($path as $childPath) {
            if (!isset($childElement[$childPath])) {
                $childElement[$childPath] = [];
            }
            $childElement = &$childElement[$childPath];
        }

        // If this is the add() filter and if the targeted child element is an
        // array, add the value to it.
        if ($isAddFilter && isset($childElement[$lastPath]) && is_array($childElement[$lastPath])) {
            if (is_array($value)) {
                $childElement[$lastPath] = array_merge($childElement[$lastPath], $value);
            } else {
                $childElement[$lastPath][] = $value;
            }
        } else {
            // Otherwise, replace the target element with the given value.
            $childElement[$lastPath] = $value;
        }

        return $filteredElement;
    }
}
