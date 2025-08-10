<?php

namespace Yahvya\EagleFramework\Utils\List;

use Closure;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Iterator;
use TypeError;

/**
 * @brief Uniform content list
 * @template ContainedType Content type description
 */
class SaboList implements Countable, Iterator, Arrayable
{
    /**
     * @var ContainedType[] Data
     */
    protected array $datas;

    /**
     * @var int Current pointer index
     */
    protected int $currentPos;

    /**
     * @var Closure Search handler
     */
    protected Closure $finder;

    /**
     * @param ContainedType[] $datas Data to treat
     */
    public function __construct(array $datas)
    {
        $this->datas = $datas;
        $this->currentPos = 0;
        $this->finder = $this->getDefaultFinder();
    }

    /**
     * @return int Count of elements in the list
     */
    public function count(): int
    {
        return count(value: $this->datas);
    }

    /**
     * @return ContainedType Element pointed by the current index
     */
    public function current(): mixed
    {
        return $this->datas[$this->currentPos];
    }

    /**
     * @return int Current index
     */
    public function key(): int
    {
        return $this->currentPos;
    }

    /**
     * @brief Move the current to the next index
     * @attention No check is performed
     */
    public function next(): void
    {
        ++$this->currentPos;
    }

    /**
     * @brief Move the cursor to index 0
     */
    public function rewind(): void
    {
        $this->currentPos = 0;
    }

    /**
     * @return bool If the current index is in a valid position in the list
     */
    public function valid(): bool
    {
        return isset($this->datas[$this->currentPos]);
    }

    /**
     * @return ContainedType|null First element or null
     */
    public function getFirst(): mixed
    {
        return $this->datas[0] ?? null;
    }

    /**
     * @return ContainedType|null The last element of null if it doesn't exist
     */
    public function getLast(): mixed
    {
        return $this->datas[count($this->datas) - 1] ?? null;
    }

    /**
     * @brief Update the search handler
     * @param Closure $finder New search handler
     * @attention Handler prototype should be: (searched data,data list with the format -> [ContainedType...])
     * @attention Handler return prototype: [ContainedType...] Corresponding data
     * @return $this
     */
    public function setFinder(Closure $finder): SaboList
    {
        $this->finder = $finder;

        return $this;
    }

    /**
     * @return bool If the content is empty
     */
    public function isEmpty(): bool
    {
        return $this->count() == 0;
    }

    /**
     * @brief Défini la fonction de recherche par défaut permettant
     * @attention Arguments de la fonction (donnée recherchée, liste de données au format [ContainedType])
     * @attention Retour de la fonction [ContainedType] les données correspondantes
     * @return Closure La fonction de recherche par défaut
     */
    public function getDefaultFinder(): Closure
    {
        return fn(mixed $toFind, mixed $datas): array => array_filter(
            array: $datas,
            callback: fn(mixed $element): bool => $element === $toFind
        );
    }

    /**
     * @brief Find the corresponding data and build a new instance from them
     * @param mixed ...$toFinds Data to find (used for the comparison)
     * @return SaboList<ContainedType> Result list based on the founded items
     * @attention The search handler should be adapted to the provided data
     * @throws TypeError On error
     */
    public function find(mixed ...$toFinds): SaboList
    {
        $resultList = [];

        foreach ($toFinds as $toFind)
        {
            $foundedElements = call_user_func_array($this->finder, [$toFind, $this->datas]);

            if (!empty($foundedElements))
                $resultList = array_merge($resultList, $foundedElements);
        }

        return new SaboList($resultList);
    }

    /**
     * @return ContainedType[] Data array
     */
    public function toArray(): array
    {
        return $this->datas;
    }
}
