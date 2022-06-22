<?php

namespace Mavu\GlobalBundle\Content\Select;


class BlockTypeSelect
{
    /**
     * @return array<int, array{name: string, title: string}>
     */
    public function getValues(string $locale): array
    {
        return [
            ['name' => 'textblock', 'title' => 'textblock'],
            ['name' => 'image', 'title' => 'image'],
            ['name' => 'imageslider', 'title' => 'imageslider'],
            ['name' => 'video', 'title' => 'video'],
            ['name' => 'special_element', 'title' => 'special_element'],
            ['name' => 'twocol', 'title' => '2-columns'],
            ['name' => 'threecol', 'title' => '3-columns'],
            ['name' => 'button', 'title' => 'button'],
            ['name' => 'expander', 'title' => 'expander'],
            ['name' => 'section', 'title' => 'section'],
            ['name' => 'headline', 'title' => 'Headline (standalone)'],
            // [ 'name'=>'common', 'title'=>'common'],
        ];
    }


    /**
     * Optional default value for a single select.
     */
    public function getSingleSelectDefaultValue(): string
    {
        return 'textblock';
    }

    /**
     * Optional default value for a multi select.
     *
     * @return array<int, array{name: string}>
     */
    public function getMultiSelectDefaultValue(): array
    {
        return [
            ['name' => 'textblock'],
        ];
    }
}
