<?php

namespace EFrame\Validation;

use Illuminate\Support\MessageBag;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Validation\ValidationException as IlluminateValidationException;

class ValidationException extends IlluminateValidationException implements HttpExceptionInterface
{
    /**
     * Get all of the validation error messages.
     *
     * @return array
     */
    public function errors()
    {
        $messages = [];

        /**
         * @var string       $attribute
         * @var MessageBag[] $message_bags
         */
        foreach ($this->validator->errors()->messages() as $attribute => $message_bags) {

            $errors = [];

            foreach ($message_bags as $message_bag) {

                /**
                 * @var string     $rule
                 * @var MessageBag $messages
                 */
                foreach ($message_bag->messages() as $rule => $error_messages) {
                    $errors[] = [
                        'rule'     => $rule,
                        'messages' => $error_messages,
                    ];
                }
            }

            $messages[] = [
                'attribute' => $attribute,
                'errors'    => $errors,
            ];
        }

        return $messages;
    }

    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders()
    {
        return [];
    }
}