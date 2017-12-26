<?php

namespace Tests\Unit;

use App\Services\Hotels\Orders\NameOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Services\Hotels\Orders\NameOrder<extended>
 */
class OrderByTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_set_order_by_name_ascending_by_default()
    {
        $nameOrder = new NameOrder();
        $this->assertTrue($nameOrder->isAscOrder());
    }

    /**
     * @test
     */
    public function it_can_set_order_ascending_by_method()
    {
        $nameOrder = (new NameOrder())->asc();
        $this->assertTrue($nameOrder->isAscOrder());
    }

    /**
     * @test
     */
    public function it_can_set_order_ascending_from_constructor()
    {
        $nameOrder = new NameOrder('asc');
        $this->assertTrue($nameOrder->isAscOrder());
    }

    /**
     * @test
     */
    public function it_can_set_order_descending_by_method()
    {
        $nameOrder = (new NameOrder())->desc();
        $this->assertTrue($nameOrder->isDescOrder());
    }

    /**
     * @test
     */
    public function it_can_set_order_descending_from_constructor()
    {
        $nameOrder = new NameOrder('desc');
        $this->assertTrue($nameOrder->isDescOrder());
    }
}
