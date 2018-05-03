<?php

namespace EFrame\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

class Min implements Rule
{
    /**
     * @var int
     */
    protected $size;

    /**
     * @var int
     */
    protected $min;

    /**
     * Min constructor.
     *
     * @param int $min
     * @param     $value
     */
    public function __construct(int $min, $value)
    {
        $this->min  = $min;
        $this->size = sizeof($value);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->size >= $this->min;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The size of :attribute is too short.';
    }
}