<?php

class GameOfLife
{
    public static $rules = [
        ['isAlive' => true, 'neighbours' => 2],
        ['isAlive' => true, 'neighbours' => 3],
        ['isAlive' => false, 'neighbours' => 3]
    ];

    public static $neighbours = [
        ['x' => -1, 'y' => -1], ['x' => 0, 'y' => -1], ['x' => 1, 'y' => -1],
        ['x' => -1, 'y' => 0],                         ['x' => 1, 'y' => 0],
        ['x' => -1, 'y' => 1],  ['x' => 0, 'y' => 1],  ['x' => 1, 'y' => 1],
    ];

    /**
     * @param bool $isAlive
     * @param int $neighbours
     * @return bool
     */
    public function willAlive($isAlive, $neighbours)
    {
        $filter = array_filter(static::$rules, function ($rule) use ($isAlive, $neighbours) {
            return $rule['isAlive'] == $isAlive && $rule['neighbours'] == $neighbours;
        });

        return !empty($filter);
    }

    /**
     * @param array $cell
     * @param array $generation
     * @return bool
     */
    public function isAlive(array $cell, array $generation)
    {
        return in_array($cell, $generation);
    }

    /**
     * @param array $cell
     * @return array
     */
    public function neighbours(array $cell)
    {
        return array_map(function ($n) use ($cell) {
            return ['x' => $cell['x'] + $n['x'], 'y' => $cell['y'] + $n['y']];
        }, static::$neighbours);
    }

    /**
     * @param array $cell
     * @param array $generation
     * @return int
     */
    public function neighboursCount(array $cell, array $generation)
    {
        return count(array_filter($this->neighbours($cell), function ($neighbour) use ($generation) {
             return $this->isAlive($neighbour, $generation);
        }));
    }

    /**
     * @param array $generation
     * @return int
     */
    public function candidates(array $generation)
    {
        return array_reduce($generation, function ($candidates, $cell) {
            return array_merge($candidates, $this->neighbours($cell), [$cell]);
        }, []);
    }

    /**
     * @param array $generation
     * @return array
     */
    public function nextGeneration(array $generation)
    {
        $nextGeneration = [];
        foreach ($this->candidates($generation) as $cell) {
            if (!in_array($cell, $nextGeneration)
                && $this->willAlive($this->isAlive($cell, $generation), $this->neighboursCount($cell, $generation))) {

                $nextGeneration[] = $cell;
            }
        }

        return $nextGeneration;
    }
}
