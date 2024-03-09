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
            $factory = self::newFactory([APP_CONFIG->getConfig("ROOT") . "/src/views/"]);

            parent::__construct($factory->make($pathFromViews,$datas)->render());
        }
        catch(Throwable){
            parent::__construct("Veuillez rechargez la page");
        }
    }

    /**
     * @param array $viewsPath chemin du dossier des vues
     * @return Factory|null le factory crée
     */
    public static function newFactory(array $viewsPath):Factory|null{
        try{
            $pathToCompiledTemplates = APP_CONFIG->getConfig("ROOT") . "/sabo-core/views/blade/compiled";
            $filesystem = new Filesystem;
            $eventDispatcher = new Dispatcher(new Container);
            $viewResolver = new EngineResolver;
            $bladeCompiler = new BladeCompiler($filesystem, $pathToCompiledTemplates);

            // enregistrement des directives
            $bladeDirectives = registerBladeDirectives();

            foreach ($bladeDirectives as $directive => $executor)
                $bladeCompiler->directive($directive,$executor);

            $viewResolver->register("blade", function () use ($bladeCompiler) {
                return new CompilerEngine($bladeCompiler);
            });
            $viewResolver->register("php", function () use($filesystem) {
                return new PhpEngine($filesystem);
            });

            $viewFinder = new FileViewFinder($filesystem, $viewsPath);

            return new Factory($viewResolver, $viewFinder, $eventDispatcher);
        }
        catch(Throwable){
            return null;
        }
    }
}