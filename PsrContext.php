<?php
namespace App\Ipaas;

use Monolog\Handler\PsrHandler;

class PsrContext extends PsrHandler
{
    public function handle(array $record)
    {
        $record['context'] += ilog()->toArray();
        return parent::handle($record);
    }
}
