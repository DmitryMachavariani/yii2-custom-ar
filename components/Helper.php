<?php

namespace app\components;

use Thunder\Shortcode\HandlerContainer\HandlerContainer;
use Thunder\Shortcode\Parser\RegularParser;
use Thunder\Shortcode\Processor\Processor;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use yii\helpers\ArrayHelper;

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
        foreach ($parts as $i => $part) {
            if (mb_strlen($message . $part) < $maxSize) {
                $message .= $part . PHP_EOL;
            } else {
                $bigPart = $message . $part;
                $partLen = mb_strlen($bigPart);
                $countParts = ceil($partLen / $maxSize);
                for ($i = 0; $i < $countParts - 1; $i++) {
                    $result[] = mb_substr($bigPart, $i * $maxSize, $maxSize);
                }
                if (mb_strlen(mb_substr($bigPart, ($countParts - 1) * $maxSize, $maxSize)) < $maxSize) {
                    $message = mb_substr($bigPart, ($countParts - 1) * $maxSize, $maxSize) . PHP_EOL;
                } else {
                    $message = '';
                }
            }
        }
        $result[] = $message . PHP_EOL;

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

    public static function minimizePhone($phone)
    {
        if (empty($phone)) {
            return null;
        }
        $phone = preg_replace('/[^0-9]+/isu', '', $phone);
        if (strpos($phone, '8') === 0) {
            $phone = preg_replace('/^8/', '7', $phone);
        }
        return '+' . preg_replace('/[^0-9]+/isu', '', $phone);
    }

    public static function maximizePhone($phone)
    {
        $phone = self::minimizePhone($phone);
        return preg_replace(
            '/(\+\d{1})(\d{3})(\d{3})(\d{2})(\d{2})/isu', "$1 ($2) $3-$4-$5",
            $phone
        );
    }

    /**
     * @param $text           - Text with shortcodes
     * @param $processData    [keyword => 'file', 'attributes' => ['id', 'name']
     * @param $stringTemplate "<a href='/task/download?fileId=%s'>%s</a>"
     *
     * @return string
     */
    public static function processShortCode($text, $processData, $stringTemplate)
    {
        $handlers = new HandlerContainer();
        $handlers->add($processData['keyword'], function(ShortcodeInterface $s) use ($processData, $stringTemplate)
        {
            return call_user_func_array('sprintf',
                ArrayHelper::merge(
                    [$stringTemplate],
                    array_map(function ($item) use ($s) {
                        return $s->getParameter($item);
                    }, $processData['attributes'])
                )
            );
        });
        $processor = new Processor(new RegularParser(), $handlers);

        return $processor->process($text);
    }

    public static function processHistoryOfTask(int $type)
    {

    }
}