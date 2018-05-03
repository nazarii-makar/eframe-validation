<?php

namespace EFrame\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

class Max implements Rule
{
    /**
     * @var int
     */
    protected $size;

    /**
     * @var int
     */
    protected $max;

    /**
     * Min constructor.
     *
     * @param int $max
     * @param     $value
     */
    public function __construct(int $max, $value)
    {
        $this->max  = $max;
        $this->size = sizeof($value);
    }

    /**
     * Determaxe if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return  $this->max >= $this->size;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The size of :attribute is too long.';
    }
}