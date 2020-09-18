<?php
/**
 * PHP Lexer
 * Copyright (C) 2020 Christian Neff
 *
 * Permission to use, copy, modify, and/or distribute this software for
 * any purpose with or without fee is hereby granted, provided that the
 * above copyright notice and this permission notice appear in all copies.
 *
 * @package  Secondtruth\Lexer
 * @version  0.1-dev
 * @link     https://www.secondtruth.de
 * @license  https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Secondtruth\Lexer;

use ReflectionClass;
use function implode;
use function preg_split;
use function sprintf;
use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;
use const PREG_SPLIT_OFFSET_CAPTURE;

/**
 * Base class for writing simple lexers, i.e. for creating small DSLs.
 */
abstract class AbstractLexer
{
    /**
     * Composed regex for input parsing.
     *
     * @var string
     */
    private string $regex;

    /**
     * Scans the input string for tokens.
     *
     * @param string $input The input to be tokenized.
     *
     * @return TokenStream
     */
    public function tokenize($input)
    {
        if (!isset($this->regex)) {
            $this->regex = sprintf(
                '/(%s)|%s/%s',
                implode(')|(', $this->getCatchablePatterns()),
                implode('|', $this->getNonCatchablePatterns()),
                $this->getModifiers()
            );
        }

        $flags   = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;
        $matches = preg_split($this->regex, $input, -1, $flags);

        if ($matches === false) {
            // Work around https://bugs.php.net/78122
            $matches = [[$input, 0]];
        }

        $tokens = [];
        foreach ($matches as $match) {
            // Must remain before 'value' assignment since it can change content
            $type = $this->getType($match[0]);

            $tokens[] = new Token($match[0], $type, $match[1]);
        }

        return new TokenStream($tokens, $input);
    }

    /**
     * Gets the literal for a given token.
     *
     * @param int|string $token
     * @param bool $withClass
     *
     * @return int|string
     *
     * @throws \ReflectionException
     */
    public function getLiteral($token, $withClass = false)
    {
        $className = static::class;
        $reflClass = new ReflectionClass($className);
        $constants = $reflClass->getConstants();

        foreach ($constants as $name => $value) {
            if ($value === $token) {
                return ($withClass ? $className . '::' : '') . $name;
            }
        }

        return $token;
    }

    /**
     * Checks if given value is identical to the given token.
     *
     * @param mixed $value
     * @param int|string $token
     *
     * @return bool
     */
    public function isA($value, $token): bool
    {
        return $this->getType($value) === $token;
    }

    /**
     * Get Regex modifiers.
     *
     * @return string
     */
    protected function getModifiers(): string
    {
        return 'iu';
    }

    /**
     * Lexical catchable patterns.
     *
     * @return array
     */
    abstract protected function getCatchablePatterns();

    /**
     * Lexical non-catchable patterns.
     *
     * @return array
     */
    abstract protected function getNonCatchablePatterns();

    /**
     * Retrieve token type. Also processes the token value if necessary.
     *
     * @param string $value
     *
     * @return int|string|null
     */
    abstract protected function getType(&$value);
}
