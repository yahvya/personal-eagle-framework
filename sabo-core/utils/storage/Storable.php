<?php

namespace SaboCore\Utils\Storage;

/**
 * @brief Représente un élement pouvant être stocké
 * @author yahaya bathily
 */
interface Storable{
    /**
     * @brief Stocke l'élément
     * @param string $path chemin de stockage attendu
     * @return bool si le stockage a réussi
     */
    public function storeIn(string $path):bool;

    /**
     * @return mixed le contenu de l'élément stocké
     */
    public function getFromStorage():mixed;
}