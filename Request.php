<?php

namespace App\Ipaas;

use Illuminate\Support\Facades\Validator;

/**
 * Class Request
 * @package App\Ipaas
 */
class Request
{
    /**
     * @var \Illuminate\Http\Request|Request $this
     */

    /**
     * @var \Illuminate\Http\Request
     */
    protected $instance;

    public function __construct(\Illuminate\Http\Request $instance)
    {
        $this->instance = $instance;
    }

    /**
     * @param string $item
     * @return $this
     */
    public function boolify(string $item)
    {
        if ($this->instance->has($item)) {
            $list = $this->instance->all();
            $object = $list[$item];
            if (strtolower($object) === 'true') {
                $object = true;
                $list[$item] = $object;
            } elseif (strtolower($object) === 'false') {
                $object = false;
                $list[$item] = $object;
            }
            $this->instance->replace($list);
        }

        return $this;
    }

    /**
     * @param string $item
     * @return $this
     */
    public function arrify(string $item)
    {
        if ($this->instance->has($item)) {
            $list = $this->instance->all();
            $object = $list[$item];
            if ($object !== null) {
                $list[$item] = is_array($object) ? $object : explode(',', $object);
            }
            request()->replace($list);
        }

        return $this;
    }

    /**
     * @param string $item
     * @param mixed $value
     * @return $this
     */
    public function requestify(string $item, mixed $value)
    {
        $list = $this->instance->all();
        $list[$item] = $value;
        request()->replace($list);
        return $this;
    }

    /**
     * @param array $rules
     * @return Request
     * @throws ValidationException
     * @throws \Exception
     */
    public function validate(array $rules)
    {
        $list = $this->instance->all();
        $validator = Validator::make($list, $rules);

        if ($validator->fails()) {
            throw new ValidationException(
                $validator,
                'Invalid request',
                422
            );
        }
        return $this;
    }

    /**
     * @param string $property
     * @return bool
     */
    public function has(
        string $property
    ) {
        return $this->instance->has($property);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->instance->all();
    }

    /**
     * @method
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->instance, $method), $args);
    }

    public function __get($key)
    {
        return $this->instance->$key;
    }

    public function __set($key, $val)
    {
        return $this->instance->$key = $val;
    }
}
