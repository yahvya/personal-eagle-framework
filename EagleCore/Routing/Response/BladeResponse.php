<?php

namespace Yahvya\EagleFramework\Routing\Response;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Throwable;

/**
 * @brief Blade file response
 */
class BladeResponse extends HtmlResponse
{
    /**
     * @param string $pathFromViews Path from the views directory
     * @param array $datas Data to pass to the view
     */
    public function __construct(string $pathFromViews, array $datas = [])
    {
        try
        {
            $factory = self::newFactory(viewsPath: [APP_CONFIG->getConfig(name: "ROOT") . "/Src/views/"]);

            parent::__construct(content: $factory->make(view: $pathFromViews, data: $datas)->render());
        }
        catch (Throwable)
        {
            parent::__construct(content: "Please reload the page");
        }
    }

    /**
     * @param array $viewsPath Path to the views directory
     * @return Factory|null The created factory
     */
    public static function newFactory(array $viewsPath): Factory|null
    {
        try
        {
            $pathToCompiledTemplates = APP_CONFIG->getConfig(name: "ROOT") . "/EagleCore/views/blade/compiled";
            $filesystem = new Filesystem;
            $eventDispatcher = new Dispatcher(container: new Container);
            $viewResolver = new EngineResolver;
            $bladeCompiler = new BladeCompiler(files: $filesystem, cachePath: $pathToCompiledTemplates);

            // Register directives
            $bladeDirectives = registerBladeDirectives();

            foreach ($bladeDirectives as $directive => $executor)
                $bladeCompiler->directive($directive, $executor);

            $viewResolver->register(engine: "blade", resolver: function () use ($bladeCompiler) {
                return new CompilerEngine($bladeCompiler);
            });
            $viewResolver->register(engine: "php", resolver: function () use ($filesystem) {
                return new PhpEngine($filesystem);
            });

            $viewFinder = new FileViewFinder(files: $filesystem, paths: $viewsPath);

            return new Factory(engines: $viewResolver, finder: $viewFinder, events: $eventDispatcher);
        }
        catch (Throwable)
        {
            return null;
        }
    }
}
