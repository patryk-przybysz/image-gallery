<?php

declare(strict_types=1);

namespace App\Utils;

class ValidationSchema
{
    public $rules = [];

    private function addRule($callback, string $message)
    {
        // https://stackoverflow.com/a/41168727
        if (!is_callable($callback)) {
            throw new \Exception("Provided validation callback is not callable");
        }
        array_push($this->rules, [
            'callback' => $callback,
            'message' => $message,
        ]);
        return $this;
    }

    public function string(string $message = 'The %s has to be a string')
    {
        return $this->addRule('is_string', $message);
    }

    public function number(string $message = 'The %s has to be a number')
    {
        return $this->addRule(function ($data) {
            return is_int($data) || is_float($data);
        }, $message);
    }

    public function array(array $schemas, string $message = 'The %s has to be an array')
    {
        foreach ($schemas as $key => $schema) {
            $this->rules[$key] = $schema;
        }

        return $this->addRule('is_array', $message);
    }

    public function required(string $message = 'The %s has to be set')
    {
        return $this->addRule(function ($data) {
            if (is_string($data)) {
                return $data != '';
            }
            return $data != null && $data !== '';
        }, $message);
    }

    public function enum(array $acceptedValues, string $message = 'The %s is not a correct value')
    {
        return $this->addRule(function ($data) use ($acceptedValues) {
            return in_array($data, $acceptedValues);
        }, $message);
    }

    public function minLength(int $minLength, string $message = "The %s is too short")
    {
        return $this->addRule(function ($data) use ($minLength) {
            switch (true) {
                case is_array($data):
                case is_subclass_of($data, 'Countable', true):
                    return count($data) >= $minLength;

                case is_string($data):
                    return strlen($data) >= $minLength;

                default:
                    return false;
            }
        }, $message);
    }

    public function maxLength(int $maxLength, string $message = "The %s is too long")
    {
        return $this->addRule(function ($data) use ($maxLength) {
            switch (true) {
                case is_array($data):
                case is_subclass_of($data, 'Countable', true):
                    return count($data) <= $maxLength;

                case is_string($data):
                    return strlen($data) <= $maxLength;

                default:
                    return false;
            }
        }, $message);
    }

    public function refine(callable $callback, string $message = 'Invalid value for %s')
    {
        return $this->addRule($callback, $message);
    }

    public function safeParse($data, $fieldName = 'field')
    {
        $errors = [];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (isset($this->rules[$key])) {
                    $schema = $this->rules[$key];
                    $errors[$key] = $schema->safeParse($value, $key);
                }
            }
        } else {
            foreach ($this->rules as $rule) {
                $callback = $rule['callback'];
                $message = $rule['message'];

                $result = $callback($data);
                if (!$result) {
                    // pass params
                    $errors[] = sprintf($message, $fieldName);
                }
            }
        }

        return $errors;
    }
}
