<?php

if (!function_exists('normalizedName')) {
    /**
     * Normalize name
     *
     * @param $name
     * @return mixed
     */
    function normalizedName($name)
    {
        return preg_replace('/(?:\s\s+|\n|\t)/', ' ', $name);
    }
}

if (!function_exists('boolifyList')) {
    /**
     * Update Request item into boolean
     *
     * @param array $list
     * @param $item
     */
    function boolifyList(&$list, $item)
    {
        $object = $list[$item];
        if (strtolower($object) === 'true') {
            $object = true;
            $list[$item] = $object;
        } elseif (strtolower($object) === 'false') {
            $object = false;
            $list[$item] = $object;
        }
    }
}
