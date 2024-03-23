<?php

namespace SaboCore\Routing\Response;

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
 * @brief Réponse fichier blade
 * @author yahaya bathily https://github.com/yahvya
 */
class BladeResponse extends HtmlResponse{
    /**
     * @param string $pathFromViews chemin à partir du dossier des vues
     * @param array $datas données de la vue
     */
    public function __construct(string $pathFromViews,array $datas = []){
        try{
            $factory = self::newFactory(viewsPath: [APP_CONFIG->getConfig(name: "ROOT") . "/src/views/"]);

            parent::__construct(content: $factory->make(view: $pathFromViews,data: $datas)->render());
        }
        catch(Throwable){
            parent::__construct(content: "Veuillez rechargez la page");
        }
    }

    /**
     * @param array $viewsPath chemin du dossier des vues
     * @return Factory|null le factory crée
     */
    public static function newFactory(array $viewsPath):Factory|null{
        try{
            $pathToCompiledTemplates = APP_CONFIG->getConfig(name: "ROOT") . "/sabo-core/views/blade/compiled";
            $filesystem = new Filesystem;
            $eventDispatcher = new Dispatcher(container: new Container);
            $viewResolver = new EngineResolver;
            $bladeCompiler = new BladeCompiler(files: $filesystem, cachePath: $pathToCompiledTemplates);

            // enregistrement des directives
            $bladeDirectives = registerBladeDirectives();

            foreach ($bladeDirectives as $directive => $executor)
                $bladeCompiler->directive($directive,$executor);

            $viewResolver->register(engine: "blade",resolver:  function () use ($bladeCompiler) {
                return new CompilerEngine($bladeCompiler);
            });
            $viewResolver->register(engine: "php", resolver: function () use($filesystem) {
                return new PhpEngine($filesystem);
            });

            $viewFinder = new FileViewFinder(files: $filesystem, paths: $viewsPath);

            return new Factory(engines: $viewResolver, finder: $viewFinder, events: $eventDispatcher);
        }
        catch(Throwable){
            return null;
        }
    }
}