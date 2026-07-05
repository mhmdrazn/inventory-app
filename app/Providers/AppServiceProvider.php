<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // l5-swagger v11 registers AttributeAnnotationFactory only by default,
        // which silently ignores our PHPDoc @OA\... annotations. Inject an
        // analyser that reads both attribute and doc-block annotations.
        //
        // Kept out of config/l5-swagger.php because ReflectionAnalyser has no
        // __set_state() and config:cache serializes via var_export(). Skip
        // during config:cache itself so the deploy build succeeds; every
        // subsequent boot re-runs register() and sees the analyser.
        if (! $this->runningConfigCache()) {
            Config::set('l5-swagger.defaults.scanOptions.analyser', new ReflectionAnalyser([
                new AttributeAnnotationFactory(),
                new DocBlockAnnotationFactory(),
            ]));
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    private function runningConfigCache(): bool
    {
        return $this->app->runningInConsole()
            && in_array('config:cache', $_SERVER['argv'] ?? [], true);
    }
}
