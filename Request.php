<?php

namespace Ipaas;

use Illuminate\Http\Request as BaseRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class Request
 *
 * @package Ipaas
 */
class Request extends BaseRequest
{

    /**
     * Request constructor.
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     */
    public function __construct(
        array $query = array(),
        array $request = array(),
        array $attributes = array(),
        array $cookies = array(),
        array $files = array(),
        array $server = array(),
        $content = null
    ) {
        // temp store data
        $request_date = request()->all();

        // construct new request
        parent::__construct(
            request()->query->all(),
            request()->request->all(),
            request()->attributes->all(),
            request()->cookies->all(),
            request()->files->all(),
            request()->server->all(),
            request()->content
        );

        // reset temp data
        $this->replace($request_date);
    }

    /**
     * @param string $item
     * @return $this
     */
    public function boolify(string $item)
    {
        if ($this->has($item)) {
            $list = $this->all();
            $object = $list[$item];
            if (strtolower($object) === 'true') {
                $object = true;
                $list[$item] = $object;
            } elseif (strtolower($object) === 'false') {
                $object = false;
                $list[$item] = $object;
            }
            $this->replace($list);
        }

        return $this;
    }

    /**
     * @param string $item
     * @return $this
     */
    public function arrify(string $item)
    {
        if ($this->has($item)) {
            $list = $this->all();
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
    public function requestify(string $item, $value)
    {
        $list = $this->all();
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
        $list = $this->all();
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
