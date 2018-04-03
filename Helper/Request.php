<?php

if (!function_exists('validate')) {
    /**
     * Validate request with set rules
     *
     * @param \Illuminate\Http\Request $request
     * @param array $rules
     * @return \Illuminate\Http\Request
     * @throws \Illuminate\Validation\ValidationException
     */
    function validate(\Illuminate\Http\Request &$request, array $rules)
    {
        $list = $request->all();
        $validator = Illuminate\Support\Facades\Validator::make($list, $rules);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException(
                $validator,
                'Invalid request',
                $validator->errors()
            );
        }
        request()->replace($list);
        return $request;
    }
}

if (!function_exists('arrify')) {
    /**
     * Update Request item into array
     * @param \Illuminate\Http\Request $request
     * @param  string $item
     * @return \Illuminate\Http\Request
     */
    function arrify(\Illuminate\Http\Request &$request, string $item)
    {
        if ($request->has($item)) {
            $list = $request->all();
            $object = $list[$item];
            if ($object !== null) {
                $list[$item] = is_array($object) ? $object : explode(',', $object);
            }
            request()->replace($list);
            return $request;
        }
    }
}


if (!function_exists('boolify')) {
    /**
     * Update Request item into boolean
     *
     * @param \Illuminate\Http\Request $request
     * @param string $item
     * @return \Illuminate\Http\Request
     */
    function boolify(\Illuminate\Http\Request &$request, string $item)
    {
        if ($request->has($item)) {
            $list = $request->all();
            $object = $list[$item];
            if (strtolower($object) === 'true') {
                $object = true;
                $list[$item] = $object;
            } elseif (strtolower($object) === 'false') {
                $object = false;
                $list[$item] = $object;
            }
            request()->replace($list);
            return $request;
        }
    }
}

if (!function_exists('requestify')) {
    /**
     * Replace or insert item in request
     *
     * @param                          $item
     * @param                          $value
     */
    function requestify(\Illuminate\Http\Request &$request, $item, $value)
    {
        $list = request()->all();
        $list[$item] = $value;
        request()->replace($list);
        return $request;
    }
}
