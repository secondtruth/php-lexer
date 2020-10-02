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
 * Class Context.
 *
 * @author Christian Neff <christian.neff@gmail.com>
 */
class Context
{
    /**
     * Lexer original input string.
     *
     * @var string
     */
    private $string;

    /**
     * Context constructor.
     *
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * Retrieve the original lexer's input until a given position.
     *
     * @param int $position
     *
     * @return string
     */
    public function getUntilPosition($position)
    {
        return substr($this->string, 0, $position);
    }

    /**
     * @param int|null $position
     *
     * @return array
     */
    public function where(?int $position): array
    {
        $linesBefore = preg_split('#\r?\n#', $position !== null ? $this->getUntilPosition($position) : $this->string);
        $line = count($linesBefore);
        $column = strlen(end($linesBefore)) + 1;

        return [$line, $column];
    }

    /**
     * @param Token $token
     *
     * @return array
     */
    public function whereToken(Token $token): array
    {
        return $this->where($token->position);
    }
}
