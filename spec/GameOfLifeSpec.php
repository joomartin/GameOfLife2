<?php

namespace spec;

use GameOfLife;
use PhpSpec\ObjectBehavior;

class GameOfLifeSpec extends ObjectBehavior
{
    protected static $blinkerVertical = [
        ['x' => 1, 'y' => 0],
        ['x' => 1, 'y' => 1],
        ['x' => 1, 'y' => 2]
    ];

    protected static $blinkerHorizontal = [
        ['x' => 0, 'y' => 1],
        ['x' => 1, 'y' => 1],
        ['x' => 2, 'y' => 1]
    ];

    protected static $block = [
        ['x' => 0, 'y' => 0],
        ['x' => 1, 'y' => 0],
        ['x' => 0, 'y' => 1],
        ['x' => 1, 'y' => 1]
    ];

    // ---------- willAlive ---------- 

    function it_will_die_if_live_with_fewer_than_two_neighbours()
    {
        $this->willAlive(true, 1)->shouldBe(false);
        $this->willAlive(true, 0)->shouldBe(false);
    }

    function it_will_alive_if_live_with_2_or_3_neighbours()
    {
        $this->willAlive(true, 2)->shouldBe(true);
        $this->willAlive(true, 3)->shouldBe(true);
    }

    function it_will_die_if_live_with_more_than_3_neighbours()
    {
        $this->willAlive(true, 4)->shouldBe(false);
        $this->willAlive(true, 5)->shouldBe(false);
    }
    
    function it_will_alive_if_dead_with_3_neighbours()
    {
        $this->willAlive(false, 3)->shouldBe(true);
    }

    function it_will_remains_dead_if_dead_with_less_than_3_neighbours()
    {
        $this->willAlive(false, 0)->shouldBe(false);
        $this->willAlive(false, 1)->shouldBe(false);
        $this->willAlive(false, 2)->shouldBe(false);
    }

    function it_will_remains_dead_if_dead_with_more_than_3_neighbours()
    {
        $this->willAlive(false, 4)->shouldBe(false);
        $this->willAlive(false, 5)->shouldBe(false);
        $this->willAlive(false, 6)->shouldBe(false);
    }

    // ---------- isAlive ---------- 
    
    function it_determines_if_a_cell_is_alive()
    {
        $this->isAlive(['x' => 1, 'y' => 0], self::$blinkerVertical)->shouldBe(true);
        $this->isAlive(['x' => 1, 'y' => 1], self::$blinkerVertical)->shouldBe(true);
        $this->isAlive(['x' => 1, 'y' => 2], self::$blinkerVertical)->shouldBe(true);
    }

    function it_determines_if_a_cell_is_dead()
    {
        $this->isAlive(['x' => 0, 'y' => 0], self::$blinkerVertical)->shouldBe(false);
        $this->isAlive(['x' => 1, 'y' => 3], self::$blinkerVertical)->shouldBe(false);
        $this->isAlive(['x' => 2, 'y' => 1], self::$blinkerVertical)->shouldBe(false);
    }

    // ---------- neighbours ----------
    
    function it_determines_neighbours_of_a_cell()
    {
        $this->neighbours(['x' => 0, 'y' => 0])->shouldHaveCount(8);
        $this->neighbours(['x' => 0, 'y' => 0])->shouldBe(GameOfLife::$neighbours);
    }

    // ---------- neighboursCount ----------
    
    function it_determines_count_of_neighbours_of_a_cell() 
    {
        $this->neighboursCount(['x' => 1, 'y' => 1], self::$blinkerVertical)->shouldEqual(2);

        $this->neighboursCount(['x' => 1, 'y' => 0], self::$blinkerVertical)->shouldEqual(1);
        $this->neighboursCount(['x' => 1, 'y' => 2], self::$blinkerVertical)->shouldEqual(1);

        $this->neighboursCount(['x' => 0, 'y' => 1], self::$blinkerVertical)->shouldEqual(3);
        $this->neighboursCount(['x' => 2, 'y' => 1], self::$blinkerVertical)->shouldEqual(3);

        $this->neighboursCount(['x' => 0, 'y' => 2], self::$blinkerVertical)->shouldEqual(2);
        $this->neighboursCount(['x' => 2, 'y' => 2], self::$blinkerVertical)->shouldEqual(2);
        $this->neighboursCount(['x' => 0, 'y' => 0], self::$blinkerVertical)->shouldEqual(2);
        $this->neighboursCount(['x' => 2, 'y' => 0], self::$blinkerVertical)->shouldEqual(2);
    }

    // ---------- candidates ----------
    
    function it_determines_all_candidates_for_a_generation() 
    {
        // Every live cell, and all of its neighbours
        $this->candidates(self::$blinkerVertical)->shouldHaveCount(3 * 9);
        $this->candidates(self::$blinkerHorizontal)->shouldHaveCount(3 * 9);
        $this->candidates(self::$block)->shouldHaveCount(4 * 9);
    }

    // ---------- nextGeneration ----------

    function it_calculates_next_generation_for_blinker()
    {
        $this->nextGeneration(self::$blinkerVertical)->shouldHaveTheSame(self::$blinkerHorizontal);
        $this->nextGeneration(self::$blinkerHorizontal)->shouldHaveTheSame(self::$blinkerVertical);
        $this->nextGeneration($this->nextGeneration(self::$blinkerVertical))->shouldHaveTheSame(self::$blinkerVertical);
    }

    function it_calculates_next_generation_for_block()
    {
        $this->nextGeneration(self::$block)->shouldHaveTheSame(self::$block);
    }

    public function getMatchers()
    {
        return [
            'haveTheSame' => function($array1, $array2) {
                // array_diff throws array to string conversion, because of multidimensional arrays
                foreach ($array1 as $item) {
                    if (!in_array($item, $array2)) {
                        return false;
                    }
                }

                return true;
            }
        ];
    }
}
