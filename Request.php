<?php

namespace Ipaas;

use Illuminate\Http\Request as BaseRequest;
use Illuminate\Support\Facades\Validator;
use Ipaas\Exception\ValidationException;

/**
 * Class Request
 * @package Ipaas
 */
class Request extends BaseRequest
{

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
}
