<?php

namespace app\components;

/**
 * Class Helper
 *
 * @package app\components
 */
class Helper
{
    /**
     * @param     $array
     * @param     $key
     * @param int $counter
     *
     * @return int
     */
    public static function getElementDepth($array, $key, $counter = 0)
    {
        foreach ($array as $k => $value) {
            if ($k == $key) {
                return $counter;
            }
            if ($k != $key) {
                $counter++;
            }
            if (is_array($value)) {
                return self::getElementDepth($value, $key, $counter);
            }
        }

        return 0;
    }

    /**
     * @param      $text
     * @param bool $die
     */
    public static function prettyText($text, $die = true)
    {
        echo '<pre>';
        var_export($text);
        echo '</pre>';
        if ($die) {
            die;
        }
    }

    /**
     * @param $inputString
     *
     * @param $value
     *
     * @return array
     */
    public static function createArrayInDepth($inputString, $value)
    {
        $parts = explode("__", $inputString);
        $parts[] = $value;
        $result = [];

        for ($i = count($parts) - 1; $i > 0; $i--) {
            if (isset($result[$parts[$i]])) {
                unset($result[$parts[$i]]);
            }
            $result[$parts[$i - 1]] = $value;
            $value = $result;
        }

        return $result;
    }

    /**
     * @param $array
     *
     * @return array
     */
    public static function arrayUniqueRecursive($array)
    {
        $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::arrayUniqueRecursive($value);
            }
        }

        return $result;
    }

    /**
     * @param $totalCount
     * @param $currentOffset
     * @param $currentLimit
     *
     * @return array
     */
    public static function getLimitAndOffset($totalCount, $currentOffset, $currentLimit)
    {
        $pagesCount = ceil($totalCount / $currentLimit);
        $currentPage = ($pagesCount > 0 ? (int)($currentOffset / $currentLimit) + 1 : 0);
        $nextPage = ( ($currentOffset + $currentLimit  ) >= $totalCount ? false : $currentPage + 1);
        $previousPage = ( ($currentOffset - $currentLimit ) < 0 ? false : $currentPage - 1);

        return compact('pagesCount', 'currentPage', 'nextPage', 'previousPage');
    }

    /**
     * @param $code
     *
     * @return null|string|string[]
     */
    public static function getEmoji($code)
    {
        return preg_replace_callback(
            '@\\\x([0-9a-fA-F]{2})@x',
            function ($captures) {
                return chr(hexdec($captures[1]));
            },
            $code
        );
    }

    /**
     * @param $decDigit
     *
     * @return string
     */
    public static function getEmojiNumber($decDigit)
    {
        $emojiStartPosition = 30;
        $pattern = '\x%d\xE2\x83\xA3';
        $result = '';

        $digits = array_filter(
            preg_split('//', (string)$decDigit),
            function ($item) {
                return is_numeric($item);
            }
        );
        foreach ($digits as $digit) {
            $result .= sprintf($pattern, $emojiStartPosition + (int)$digit);
        }


        return $result;
    }

    /**
     * @param $message
     * @param $maxSize
     *
     * @return array
     */
    public static function splitMessage($message, $maxSize)
    {
        $parts = explode(PHP_EOL, $message);
        $result = [];

        $message = '';
        foreach ($parts as $part) {
            if (mb_strlen($message . $part) >= $maxSize) {
                $result[] = ($message . PHP_EOL);
                $message = $part . PHP_EOL;
            } else {
                $message .= ($part . PHP_EOL);
            }
        }
        $result[] = $message;

        return $result;
    }

    /**
     * @param       $text
     * @param array $symbols
     *
     * @return null|string|string[]
     */
    public static function clearText($text, $symbols = ['<', '>'])
    {
        foreach ($symbols as $symbol) {
            $text = preg_replace('/\\'.$symbol.'/isu', '', $text);
        }

        return $text;
    }

    /**
     * @param $array
     *
     * @return array
     */
    public static function convertToKeyAssoc($array)
    {
        $result = [];
        $usedKeys = [];

        foreach ($array as $key => $value) {
            if (!in_array($key, $usedKeys)) {
                $usedKeys[] = $key;
            }
            foreach ($value as $number => $item) {
                $result[$number][$key] = $item;
            }
        }

        return array_values($result);
    }
}