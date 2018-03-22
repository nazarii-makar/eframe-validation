<?php

namespace EFrame\Validation;

use ReflectionClass;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator as IlluminateValidator;

class Validator extends IlluminateValidator
{
    /**
     * Add a failed rule and error message to the collection.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return void
     */
    protected function addFailure($attribute, $rule, $parameters)
    {
        $messageBag = new MessageBag();
        $messageBag->add($rule, $this->makeReplacements(
            $this->getMessage($attribute, $rule), $attribute, $rule, $parameters
        ));

        $this->messages->add($attribute, $messageBag);

        $this->failedRules[$attribute][$rule] = $parameters;
    }

    /**
     * Validate an attribute using a custom rule object.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Illuminate\Contracts\Validation\Rule  $rule
     * @return void
     */
    protected function validateUsingCustomRule($attribute, $value, $rule)
    {
        if (! $rule->passes($attribute, $value)) {
            $this->failedRules[$attribute][get_class($rule)] = [];

            $messageBag = new MessageBag();
            $messageBag->add((new ReflectionClass($rule))->getShortName(), $this->makeReplacements(
                $rule->message(), $attribute, get_class($rule), []
            ));

            $this->messages->add($attribute, $messageBag);
        }
    }
}