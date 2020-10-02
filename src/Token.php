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
 * Class Token.
 *
 * @author Christian Neff <christian.neff@gmail.com>
 */
class Token
{
    /**
     * @var mixed
     */
    public $value;

    /**
     * @var int|string
     */
    public $type;

    /**
     * @var int
     */
    public int $position;

    /**
     * @var Token|null
     */
    public ?Token $next = null;

    /**
     * Token constructor.
     *
     * @param mixed $value the string value of the token in the input string
     * @param int|string $type the type of the token (identifier, numeric, string, input
     *                         parameter, none)
     * @param int $position the position of the token in the input string
     */
    public function __construct($value, $type, int $position)
    {
        $this->value = $value;
        $this->type = $type;
        $this->position = $position;
    }
}
