<?php

if (!function_exists('t_cat')) {
    /**
     * Translate a post category, falling back to the raw value if no translation exists.
     */
    function t_cat(string $category): string
    {
        $map = trans('categories');
        if (is_array($map) && array_key_exists($category, $map)) {
            return $map[$category];
        }
        return $category;
    }
}

if (!function_exists('t_cat_name')) {
    /**
     * Translate a category name and return only the text part (without emoji prefix).
     * Used in category cards where the emoji is shown separately.
     */
    function t_cat_name(string $category): string
    {
        $translated = t_cat($category);
        // Split on first space to separate emoji from name
        $parts = array_pad(explode(' ', $translated, 2), 2, '');
        return $parts[1] ?: $parts[0];
    }
}

if (!function_exists('t_tag')) {
    /**
     * Translate a post tag, falling back to the raw value if no translation exists.
     */
    function t_tag(string $tag): string
    {
        $map = trans('tags');
        if (is_array($map) && array_key_exists($tag, $map)) {
            return $map[$tag];
        }
        return $tag;
    }
}
