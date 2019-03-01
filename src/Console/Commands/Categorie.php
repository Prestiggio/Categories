<?php

namespace Ry\Categories\Console\Commands;

use Illuminate\Console\Command;
use Ry\Categories\Models\Categorygroup;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:addgroup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the category module';

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
        Categorygroup::create([
        		"name" => $this->ask("Nom:")
        ]);
        
        
    }
}
