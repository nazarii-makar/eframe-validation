<?php

namespace EFrame\Validation\Providers;

use EFrame\Validation\Validator;
use EFrame\Validation\Rules\Uuid;
use EFrame\Validation\Rules\RRule;
use Illuminate\Support\ServiceProvider;
use EFrame\Validation\Rules\RRuleFrequency;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $rules = [
        'uuid'       => Uuid::class,
        'rrule'      => RRule::class,
        'rrule_freq' => RRuleFrequency::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerValidator();

        $this->registerRules();
    }

    /**
     * Register validator
     */
    public function registerValidator()
    {
        app('validator')->resolver(function ($translator, $data, $rules, $messages) {
            return new Validator($translator, $data, $rules, $messages);
        });
    }

    /**
     * Register rules
     */
    protected function registerRules()
    {
        foreach ($this->rules as $rule => $handler) {
            \Illuminate\Support\Facades\Validator::extend($rule, $handler, call_user_func([new $handler, 'message']));
        }
    }
}