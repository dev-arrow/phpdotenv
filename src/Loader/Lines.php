<?php

declare(strict_types=1);

namespace Dotenv\Loader;

final class Lines
{
    /**
     * This class is a singleton.
     *
     * @return void
     */
    private function __construct()
    {
        //
    }

    /**
     * Process the array of lines of environment variables.
     *
     * This will produce an array of entries, one per variable.
     *
     * @param string[] $lines
     *
     * @return string[]
     */
    public static function process(array $lines)
    {
        $output = [];
        $multiline = false;
        $multilineBuffer = [];

        foreach ($lines as $line) {
            [$multiline, $line, $multilineBuffer] = self::multilineProcess($multiline, $line, $multilineBuffer);

            if (!$multiline && !self::isComment($line) && self::isSetter($line)) {
                $output[] = $line;
            }
        }

        return $output;
    }

    /**
     * Used to make all multiline variable process.
     *
     * @param bool     $multiline
     * @param string   $line
     * @param string[] $buffer
     *
     * @return array{bool,string,string[]}
     */
    private static function multilineProcess(bool $multiline, string $line, array $buffer)
    {
        // check if $line can be multiline variable
        if ($started = self::looksLikeMultilineStart($line)) {
            $multiline = true;
        }

        if ($multiline) {
            array_push($buffer, $line);

            if (self::looksLikeMultilineStop($line, $started)) {
                $multiline = false;
                $line = implode("\n", $buffer);
                $buffer = [];
            }
        }

        return [$multiline, $line, $buffer];
    }

    /**
     * Determine if the given line can be the start of a multiline variable.
     *
     * @param string $line
     *
     * @return bool
     */
    private static function looksLikeMultilineStart(string $line)
    {
        if (strpos($line, '="') === false) {
            return false;
        }

        return self::looksLikeMultilineStop($line, true) === false;
    }

    /**
     * Determine if the given line can be the start of a multiline variable.
     *
     * @param string $line
     * @param bool   $started
     *
     * @return bool
     */
    private static function looksLikeMultilineStop(string $line, bool $started)
    {
        if ($line === '"') {
            return true;
        }

        $seen = $started ? 0 : 1;

        foreach (self::getCharPairs(str_replace('\\\\', '', $line)) as $pair) {
            if ($pair[0] !== '\\' && $pair[1] === '"') {
                $seen++;
            }
        }

        return $seen > 1;
    }

    /**
     * Get all pairs of adjacent characters within the line.
     *
     * @param string $line
     *
     * @return array{array{string,string|null}}
     */
    private static function getCharPairs(string $line)
    {
        $chars = str_split($line);

        /** @var array{array{string,string|null}} */
        return array_map(null, $chars, array_slice($chars, 1));
    }

    /**
     * Determine if the line in the file is a comment, e.g. begins with a #.
     *
     * @param string $line
     *
     * @return bool
     */
    private static function isComment(string $line)
    {
        $line = ltrim($line);

        return isset($line[0]) && $line[0] === '#';
    }

    /**
     * Determine if the given line looks like it's setting a variable.
     *
     * @param string $line
     *
     * @return bool
     */
    private static function isSetter(string $line)
    {
        return strpos($line, '=') !== false;
    }
}
