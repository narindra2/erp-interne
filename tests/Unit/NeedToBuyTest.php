<?php

namespace Tests\Unit;

use App\Models\User;
use Exception;
use Tests\TestCase;

class NeedToBuyTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        try {
            $this->assertTrue(true);
        }
        catch (Exception $e) {
            $this->assertTrue(false);
        }
    }

    public function test_delete_user() {
        $user = User::find(1);
        if ($user) {
            $user->deleted = 1;
            $user->save();
        }
        $this->assertTrue(true);
    }
}
