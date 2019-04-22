<?php

namespace bigdropinc\LaravelInteractions;

use bigdropinc\LaravelInteractions\Console\InteractionMakeCommand;
use Illuminate\Support\ServiceProvider;

/**
 * Class InteractionServiceProvider
 * @package bigdropinc\LaravelInteractions
 */
class InteractionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            InteractionMakeCommand::class,
        ]);
    }
}
