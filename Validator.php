<?php

namespace EFrame\Validation;
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
}