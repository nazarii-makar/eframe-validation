<?php

namespace EFrame\Validation\Providers;

use EFrame\Validation\Validator;
use EFrame\Validation\Rules\Uuid;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $rules = [
        'uuid' => Uuid::class,
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