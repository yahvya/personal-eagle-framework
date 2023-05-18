<?php

namespace Sabo\Controller\TwigExtension;

use Twig\TwigFunction;

/**
 * extension de debug
 * fonctions [dump]
 */
class SaboDebugExtension extends SaboExtension{
    public function getFunctions():array{
        return [
            new TwigFunction("dump",[$this,"dump"])
        ];
    }

    /**
     * var_dump classique
     */
    public function dump(mixed ...$datas):void{
        echo "<pre>";
        var_dump(...$datas);
        echo "</pre>";
    }

    public static function initExtension():void{}
}