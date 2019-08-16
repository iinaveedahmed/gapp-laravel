<?php

namespace Ipaas\Gapp\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Ipaas\Gapp\Model\PartnerApp;

class CreatePartnerApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-partner-app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new row on partner_apps table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        PartnerApp::insert([
            'provider' => 'Amaka',
            'api_key' => Str::uuid(),
            'expire_date' => now()->addYears(10),
        ]);

        $this->info('Partner app created successful.');
    }
}
