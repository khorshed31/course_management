<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ModuleController extends Command
{


    protected $signature = 'module:controller {name} {path?}';




    public function handle()
    {

        $name = $this->argument('name');
        $path = $this->argument('path');


        if ($path == '') {
            $path = $this->ask('Module Name: ');
        }


        $controller = file_get_contents(base_path('app/Console/stubs/controller.stub'));

        $controller = str_replace('DummyClass', $name, $controller);
        $controller = str_replace('ModuleName', $path, $controller);

        file_put_contents("module/$path/Controllers/" . $name . '.php', $controller);

        $this->info('Controller created success !');
    }
}
