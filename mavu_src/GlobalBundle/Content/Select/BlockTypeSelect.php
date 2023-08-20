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
            ['name' => 'textblock', 'title' => 'textblock', 'defaultClasses' => ''],
            ['name' => 'image', 'title' => 'image', 'defaultClasses' => 'inline-block text-xs opacity-50 mt-3'],
            ['name' => 'imageslider', 'title' => 'imageslider', 'defaultClasses' => ''],
            ['name' => 'video', 'title' => 'video', 'defaultClasses' => 'inline-block text-xs opacity-50 mt-3'],
            ['name' => 'special_element', 'title' => 'special_element', 'defaultClasses' => ''],
            ['name' => 'twocol', 'title' => '2-columns', 'defaultClasses' => 'sm:grid sm:gap-6 xs:space-y-6 sm:grid-flow-col sm:grid-cols-[1fr_1fr]'],
            ['name' => 'threecol', 'title' => '3-columns', 'defaultClasses' => 'sm:grid sm:gap-6 xs:space-y-6 sm:grid-flow-col sm:grid-cols-[1fr_1fr_1fr]'],
            ['name' => 'button', 'title' => 'button', 'defaultClasses' => ''],
            ['name' => 'expander', 'title' => 'expander', 'defaultClasses' => 'c1:pb-8 c1:pt-4 c1:pl-8'],
            ['name' => 'section', 'title' => 'section', 'defaultClasses' => ''],
            ['name' => 'headline', 'title' => 'Headline (standalone)', 'defaultClasses' => ''],
            ['name' => 'boxes', 'title' => 'boxes', 'defaultClasses' => ''],

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
