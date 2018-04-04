<?php
namespace Ipaas\Logger;

use Monolog\Handler\PsrHandler;

class PsrContext extends PsrHandler
{
    /**
     * @param array $record
     * @return bool
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function handle(array $record)
    {
        $record['context'] += ilog()->toArray();
        return parent::handle($record);
    }
}
