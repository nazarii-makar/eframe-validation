<?php

namespace EFrame\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

class Step implements Rule
{
    /**
     * @var int
     */
    protected $step;

    /**
     * Min constructor.
     *
     * @param int $max
     * @param     $value
     */
    public function __construct(int $step = 0)
    {
        $this->step  = $step;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function validate($attribute, $value, $parameters)
    {
        $this->step = intval(collect($parameters)->first());

        return $this->passes($attribute, $value);
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
        return 0 === ($value % $this->step);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The step of :attribute is invalid.';
    }
}