<?php

if (!function_exists('normalizedName')) {
    /**
     * Normalize name
     *
     * e.g. te  sting = te sting
     * @param $name
     * @return mixed
     */
    function normalizedName($name)
    {
        return preg_replace('/(?:\s{2,}|\n|\t)/', ' ', $name);
    }
}

if (!function_exists('boolifyList')) {
    /**
     * Update Request item into boolean
     *
     * e.g. ['true', 'false', 'TRUE', 'FALSE', true, false, TRUE, FALSE, 0, 1, '0', '1', '', ' test']
     *    = [true, false, true, false, true, false, true, false, false, true, false, true, false, true]
     * @param array $list
     * @param $item
     */
    function boolifyList(&$list, $item)
    {
        if (strtolower($list[$item]) == 'false') {
            $bool = false;
        } else {
            $bool = (bool)$list[$item];
        }

        $list[$item] = $bool;
    }
}
