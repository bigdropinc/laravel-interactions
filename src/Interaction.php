<?php

namespace bigdropinc\LaravelInteractions;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;

/**
 * Class Interaction
 * @package App\Http\Interactions
 */
abstract class Interaction
{
    /**
     * @var \Illuminate\Contracts\Validation\Validator
     */
    protected $validator;
    protected $exception = true;

    private $_valid = false;
    private $_result;
    private $_attributes = [];

    private static $allowableAttributes = [];

    /**
     * Interaction constructor.
     * @param array $attributes
     * @param bool $exception
     */
    public function __construct(array $attributes = [], $exception = true)
    {
        $this->exception = $exception;
        $this->filterAttributes($attributes);
        $this->prepareForValidation();
        $this->validator = Validator::make($this->_attributes, $this->rules());
    }

    public function __set($key, $value)
    {
        if (\in_array($key, $this->getAllowedAttributes())) {
            $this->_attributes[$key] = $value;
        } else {
            $this->$key = $value;
        }
    }

    public function __get($key)
    {
        if (!empty($this->_attributes) && array_key_exists($key, $this->_attributes)) {
            return $this->_attributes[$key] ?? null;
        }

        return null;
    }

    public function __isset($key)
    {
        if (array_key_exists($key, $this->_attributes)) {
            return isset($this->_attributes[$key]);
        }

        return isset($this->$key);
    }

    /**
     * @return Interaction
     * @throws ValidationException
     */
    public function run()
    {
        if (!$this->validator->fails()) {
            $this->beforeExecute();
            $this->_result = $this->execute();
            $this->afterExecute();
            $this->_valid = $this->validator->errors()->isEmpty();
        } else {
            $this->_valid = false;
        }

        if (!$this->_valid && $this->exception) {
            throw new ValidationException($this->validator);
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function rules()
    {
        return [];
    }

    abstract protected function execute();

    protected function prepareForValidation()
    {
    }

    protected function beforeExecute()
    {
    }

    protected function afterExecute()
    {
    }

    protected function filterAttributes($attributes)
    {
        $this->_attributes = $attributes;

        $rules = $this->rules();
        foreach ($attributes as $key => $value) {
            if (null === data_get($rules, $key)) {
                $this->_attributes = array_except($this->_attributes, $key);
            }
        }
    }

    /**
     * @return array
     */
    protected function getAllowedAttributes()
    {
        if (empty(self::$allowableAttributes)) {
            self::$allowableAttributes = array_keys($this->rules());
        }

        return self::$allowableAttributes;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * @return MessageBag
     */
    public function getErrors()
    {
        return $this->validator->errors();
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->_valid;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }
}
