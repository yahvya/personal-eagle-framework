<?php

namespace SaboCore\Utils\List;

use Closure;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Iterator;
use TypeError;

/**
 * @brief Utilitaire de liste à contenu uniforme
 * @author yahaya bathily https://github.com/yahvya
 * @template ContainedType type d'éléments contenu
 */
class SaboList implements Countable, Iterator,Arrayable {
    /**
     * @var ContainedType[] données
     */
    protected array $datas;

    /**
     * @var int Position actuelle du pointeur
     */
    protected int $currentPos;

    /**
     * @var Closure Fonction de recherche
     */
    protected Closure $finder;

    /**
     * @param ContainedType[] $datas données à traiter
     */
    public function __construct(array $datas) {
        $this->datas = $datas;
        $this->currentPos = 0;
        $this->finder = $this->getDefaultFinder();
    }

    /**
     * @brief Retourne le nombre d'éléments contenu dans la liste
     * @return int Le nombre d'éléments dans la liste
     */
    public function count(): int {
        return count(value: $this->datas);
    }

    /**
     * @brief Retourne l'élément actuel de la liste
     * @return ContainedType Élément actuel
     */
    public function current(): mixed {
        return $this->datas[$this->currentPos];
    }

    /**
     * @brief Retourne la clé de l'élément actuel de la liste
     * @return int Clé actuelle
     */
    public function key(): int {
        return $this->currentPos;
    }

    /**
     * @brief Déplace le pointeur vers l'élément suivant dans la liste
     */
    public function next(): void {
        ++$this->currentPos;
    }

    /**
     * @brief Remet le pointeur à la première position dans la liste
     */
    public function rewind(): void {
        $this->currentPos = 0;
    }

    /**
     * @brief Vérifie si la position actuelle est valide dans la liste
     * @return bool True si la position actuelle est valide sinon false
     */
    public function valid(): bool {
        return isset($this->datas[$this->currentPos]);
    }

    /**
     * @return ContainedType|null première occurrence ou null si non existant
     */
    public function getFirst():mixed{
        return $this->datas[0] ?? null;
    }

    /**
     * @return ContainedType|null dernière occurrence ou null non existant
     */
    public function getLast():mixed{
        return $this->datas[count($this->datas) - 1] ?? null;
    }

    /**
     * @brief Met à jour la fonction de recherche interne
     * @param Closure $finder Nouvelle fonction de recherche
     * @attention Arguments de la fonction (donnée recherchée, liste de données au format [ContainedType])
     * @attention Retour de la fonction [ContainedType] les données correspondantes
     * @return $this
     */
    public function setFinder(Closure $finder):SaboList{
        $this->finder = $finder;

        return $this;
    }

    /**
     * @return bool Si le contenu est vide
     */
    public function isEmpty():bool{
        return $this->count() == 0;
    }

    /**
     * @brief Défini la fonction de recherche par défaut permettant
     * @attention Arguments de la fonction (donnée recherchée, liste de données au format [ContainedType])
     * @attention Retour de la fonction [ContainedType] les données correspondantes
     * @return Closure La fonction de recherche par défaut
     */
    public function getDefaultFinder():Closure{
        return fn(mixed $toFind,mixed $datas):array => array_filter(
            array: $datas,
            callback: fn(mixed $element):bool => $element === $toFind
        );
    }

    /**
     * @brief Recherche les correspondances des données fournies et crée une liste avec
     * @attention L'algorithme de recherche doit être adapté aux données fournies
     * @param mixed ...$toFinds données à trouver et utilisés pour la comparaison
     * @return SaboList<ContainedType> liste résultante
     * @throws TypeError en cas d'erreur de type recherché par l'algorithme
     */
    public function find(mixed ...$toFinds):SaboList{
        $resultList = [];

        foreach($toFinds as $toFind){
            $foundedElements = call_user_func_array($this->finder,[$toFind,$this->datas]);

            if(!empty($foundedElements) )
                $resultList = array_merge($resultList,$foundedElements);
        }

        return new SaboList($resultList);
    }

    /**
     * @return ContainedType[] Fourni la liste réelle
     */
    public function toArray():array{
        return $this->datas;
    }
}
