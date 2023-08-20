<?php


namespace Mavu\GlobalBundle\Navigation;

use Sulu\Component\Content\Compat\Structure;
use Sulu\Bundle\WebsiteBundle\Navigation\NavigationQueryBuilder;
use Sulu\Component\Content\Compat\Property;

class MavuNavigationQueryBuilder extends NavigationQueryBuilder
{
    

    public function build($webspaceKey, $locales)
    {
        $additionalFields = [];

        $where = '';
        $select = ['page.*'];
        $order = [];

        foreach ($locales as $locale) {
            $this->setLocale($locale);
            $additionalFields[$locale] = [];

            if ($this->excerpt) {
                $this->buildSelectorForExcerpt($locale, $additionalFields);
            }

            $customSelect = $this->buildSelect($webspaceKey, $locale, $additionalFields);
            if ('' !== $customSelect) {
                $select[] = $customSelect;
            }

            
            if ($this->published) {
                $where .= \sprintf(
                    '%s ((page.[%s] = %s OR page.[%s] = %s)',
                    '' !== $where ? 'OR ' : '',
                    $this->getPropertyName('state'),
                    Structure::STATE_PUBLISHED,
                    $this->getPropertyName('shadow-on'),
                    'true'
                );
            }
            
            
            $customWhere = $this->buildWhere($webspaceKey, $locale);
            if (null !== $customWhere && '' !== $customWhere) {
                $where = $where . ('' !== $where ? ' AND ' : '') . $customWhere;
            }
            
            if ($this->published) {
                $where .= ')';
            }

            $customOrder = $this->buildOrder($webspaceKey, $locale);
            if (!empty($customOrder)) {
                $order[] = $customOrder;
            } else {
                $order = ['[jcr:path] ASC'];
            }
        }

        $mixinTypeWhere = \implode(' OR ', \array_map(function($mixinType) {
            return 'page.[jcr:mixinTypes] = "' . $mixinType . '"';
        }, static::$mixinTypes));

        $sql2 = \sprintf(
            'SELECT %s
             FROM [nt:unstructured] AS page
             WHERE (%s)
                AND (%s)
                %s %s',
            \implode(', ', $select),
            $mixinTypeWhere,
            $where,
            \count($order) > 0 ? 'ORDER BY' : '',
            \implode(', ', $order)
        );

        return [$sql2, $additionalFields];
    }


        /**
     * Returns a select statement for excerpt data.
     */
    private function buildSelectorForExcerpt($locale, &$additionalFields)
    {

        $excerptStructure = $this->structureManager->getStructure('mavu_teaserdata');
        $extension = $this->extensionManager->getExtension('', 'mavu_teaserdata');

        foreach ($excerptStructure->getProperties(true) as $property) {
            $additionalFields[$locale][] = [
                'extension' => $extension,
                'target' => 'excerpt',
                'property' => $property->getName(),
                'name' => $property->getName(),
            ];
        }

        $additionalFields[$locale][]=[
            'name'=>'category',
            'property'=>new Property("category",[],null),
        ];
        $additionalFields[$locale][]=[
            'name'=>'day',
            'property'=>new Property("day",[],null),
        ];
        
        // dump($additionalFields);

    }

    // /**
    //  * Returns custom where statement.
    //  */
    // protected function buildSelect($webspaceKey, $locale, &$additionalFields)
    // {


    //     $excerptStructure = $this->structureManager->getStructure('newspost');
    //     $excerptStructure->setLanguageCode($locale);

    //     $additionalFields[$locale][] = [
    //         'property' =>  $excerptStructure->getProperty('day'),
    //         'name' => 'title',
    //     ];

    //     return '';
    // }


    
}
