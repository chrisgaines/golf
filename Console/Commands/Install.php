<?php

namespace Artisan\Console\Commands;

use Illuminate\Console\Command;
use Services\Schema;
use Services\Importer;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the schema and import past data from the PGA\'s website.';

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
    public function handle()
    {
        // Install the schema.
        $schema = new Schema;
        $schema->install();

        // Next begin importing data from the PGA.
        Importer::install();
    }
}
