<?php

namespace Tests\Unit;

use App\Http\Controllers\PaymentController;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $purchase = PaymentController::purchase();
        $this->assertTrue(true);
    }
}
