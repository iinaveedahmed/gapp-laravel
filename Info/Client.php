<?php
namespace App\Ipaas\Info;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;

class Client extends Base
{
    /**
     * @param string
     * @return Client
     */
    public function client(string $client): Client
    {
        return $this->prop($client, 'client-id');
    }

    /**
     * @param string
     * @return Client
     */
    public function key(string $value): Client
    {
        return $this->prop($value, 'client-key');
    }

    /**
     * @param string
     * @return Client
     */
    public function type(string $value): Client
    {
        return $this->prop($value, 'type');
    }

    /**
     * @param string $value
     * @param $name
     * @return Client
     */
    public function prop(string $value, $name): Client
    {
        parent::data($value, $name);
        return $this;
    }

    /**
     * @param string|Carbon $value
     * @param string $name
     * @return Client
     * @throws Exception
     */
    public function date($value, string $name): Client
    {
        /**
         * @type Carbon $value
         */
        if (!is_a($value, Carbon::class)) {
            try {
                $value = Carbon::parse($value);
            } catch (Exception $e) {
                throw $e;
            }
        }
        return $this->prop($value->format('c'), $name);
    }

    /**
     * @param string|Carbon $value
     * @return Client
     * @throws Exception
     */
    public function dateFrom($value): Client
    {
        return $this->date($value, 'date-from');
    }

    /**
     * @param string|Carbon $value
     * @return Client
     * @throws Exception
     */
    public function dateTo($value): Client
    {
        return $this->date($value, 'date-to');
    }

    /**
     * @param string
     * @return Client
     */
    public function uuid(string $value = null): Client
    {
        return $this->prop($value ? $value : Str::uuid(), 'uuid');
    }
}