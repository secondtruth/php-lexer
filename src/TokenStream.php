<?php
/*
 * PHP Lexer
 * Copyright (C) 2020 Christian Neff
 *
 * Permission to use, copy, modify, and/or distribute this software for
 * any purpose with or without fee is hereby granted, provided that the
 * above copyright notice and this permission notice appear in all copies.
 */

declare(strict_types=1);

namespace Secondtruth\Lexer;

/**
 * Class TokenStream.
 *
 * @author Christian Neff <christian.neff@gmail.com>
 */
class TokenStream
{
    /**
     * Array of scanned tokens.
     *
     * @var Token[]
     */
    private array $tokens = [];

    /**
     * Current token stream position.
     *
     * @var int
     */
    private int $position = 0;

    /**
     * Current peek of current token stream position.
     *
     * @var int
     */
    private int $peek = 0;

    /**
     * @var Context
     */
    private Context $context;

    /**
     * TokenStream constructor.
     *
     * @param Token[] $tokens
     * @param string $context
     */
    public function __construct(array $tokens, string $context)
    {
        $this->tokens = $tokens;
        $this->context = new Context($context);
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * Tells the token stream to skip input tokens until it sees a token with the given value.
     *
     * @param string $type The token type to skip until.
     */
    public function skipUntil($type)
    {
        while (($lookahead = $this->lookAhead()) !== null && $lookahead->type !== $type) {
            $this->next();
        }
    }

    /**
     * Checks whether a given token matches the current lookahead.
     *
     * @param int|string $token
     *
     * @return bool
     */
    public function isNextToken($token)
    {
        return ($lookahead = $this->lookAhead()) !== null && $lookahead->type === $token;
    }

    /**
     * Checks whether any of the given tokens matches the current lookahead.
     *
     * @param array $tokens
     *
     * @return bool
     */
    public function isNextTokenAny(array $tokens)
    {
        return ($lookahead = $this->lookAhead()) !== null && in_array($lookahead->type, $tokens, true);
    }

    /**
     * @return bool
     */
    public function atEnd()
    {
        return $this->lookAhead() === null;
    }

    /**
     * Moves to the next token in the token stream.
     *
     * @return bool
     */
    public function next()
    {
        $this->peek = 0;

        if (!isset($this->tokens[$this->position + 1])) {
            return false;
        }

        $this->position++;

        return true;
    }

    /**
     * Gets the current token in the token stream.
     *
     * @return Token|null
     */
    public function read()
    {
        $token = $this->tokens[$this->position] ?? null;
        $lookahead = $this->tokens[$this->position + 1] ?? null;

        if ($token && $lookahead) {
            $token->next = $lookahead;
        }

        return $token;
    }

    /**
     * @return Token|null
     */
    public function lookAhead()
    {
        return $this->tokens[$this->position + 1] ?? null;
    }

    /**
     * @return Token|null
     */
    public function lookBehind()
    {
        return $this->tokens[$this->position - 1] ?? null;
    }

    /**
     * Moves the lookahead pointer.
     *
     * @param int $distance The lookahead distance. Defaults to 1.
     *
     * @return Token|null The next token or NULL if there are no more tokens ahead.
     */
    public function peek(int $distance = 1)
    {
        $peek = $this->peek + $distance;

        if (isset($this->tokens[$this->position + $peek])) {
            $this->peek = $peek;

            return $this->tokens[$this->position + $peek];
        }

        return null;
    }

    /**
     * Peeks at a distant token, returns it and immediately resets the peek.
     *
     * @param int $distance The lookahead distance. Defaults to 1.
     *
     * @return Token|null The next token or NULL if there are no more tokens ahead.
     */
    public function glimpse(int $distance = 1)
    {
        $peek = $this->peek($distance);
        $this->peek = 0;

        return $peek;
    }

    /**
     * Resets the token stream.
     */
    public function reset()
    {
        $this->peek = 0;
        $this->position = 0;
    }

    /**
     * Resets the token stream to the given position.
     *
     * @param int $position Position to place the lexical scanner.
     */
    public function resetPosition($position = 0)
    {
        $this->position = $position;
    }

    /**
     * Resets the peek pointer.
     */
    public function resetPeek()
    {
        $this->peek = 0;
    }
}
