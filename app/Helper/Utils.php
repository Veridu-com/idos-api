<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Helper;

/**
 * Utilities Class.
 */
class Utils {
    /**
     * Transforms a text into a slug.
     *
     * @param string $text
     *
     * @link http://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string
     *
     * @return string
     */
    public static function slugify(string $text) : string {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
