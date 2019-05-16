<?php

namespace bigdropinc\LaravelInteractions;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class Interaction
 * @package App\Http\Interactions
 */
abstract class Interaction
{
    public $valid;
    public $result;
    /**
     * @var \Illuminate\Support\MessageBag
     */
    public $errors;

    protected $validator;
    protected $params = [];

    public function __construct(array $params = [])
    {
        $this->filterParams($params);
        $this->prepareForValidation();
        $this->validator = Validator::make($this->params, $this->rules());
    }

    public function __set($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function __get($key)
    {
        if (!empty($this->params) && array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return NULL;
    }

    public function __isset($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * @return array
     */
    protected function rules()
    {
        return [];
    }

    abstract protected function execute();

    /**
     * @param array $params
     * @param bool $exception
     * @return Interaction
     * @throws ValidationException
     */
    public static function run(array $params = [], $exception = true)
    {
        $interaction = new static($params);
        if (!$interaction->validator->fails()) {
            $interaction->result = $interaction->execute();
            $interaction->valid = $interaction->validator->errors()->isEmpty();
        } else {
            $interaction->valid = false;
        }

        if ($exception && !$interaction->valid) {
            throw new ValidationException($interaction->validator);
        }

        $interaction->errors = $interaction->validator->errors();

        return $interaction;
    }

    protected function prepareForValidation(){}

    protected function filterParams($params)
    {
        $this->params = $params;
        foreach (array_keys($params) as $key => $value) {
            if (null === data_get($this->rules(), $value)) {
                $params = array_except($params, $value);
            }
        }

        $this->params = $params;
    }
}
