<?php

namespace EFrame\Validation\Rules;

use Recurr;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Validation\Rule;

class RRuleFrequency implements Rule
{
    /**
     * @var Collection
     */
    protected $parameters;

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
        $this->parameters = collect($parameters)->map(function ($freq) {
            return strtoupper($freq);
        });

        return $this->passes($attribute, $value);
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
        try {
            /** @var Recurr\Rule $rule */
            $rule = Recurr\Rule::createFromString($value);
        } catch (Exception $e) {
            return false;
        }

        return false !== $this->parameters->search($rule->getFreqAsText());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must containts valid frequency.';
    }
}