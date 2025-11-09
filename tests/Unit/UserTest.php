<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $user = User::factory()->create(['role' => UserRole::USER->value]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function it_can_check_if_user_is_manager()
    {
        $manager = User::factory()->create(['role' => UserRole::MANAGER->value]);
        $user = User::factory()->create(['role' => UserRole::USER->value]);

        $this->assertTrue($manager->isManager());
        $this->assertFalse($user->isManager());
    }

    /** @test */
    public function it_can_check_if_user_is_regular_user()
    {
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);

        $this->assertTrue($user->isRegularUser());
        $this->assertFalse($admin->isRegularUser());
    }

    /** @test */
    public function it_can_check_if_user_is_active()
    {
        $activeUser = User::factory()->create(['active' => true]);
        $inactiveUser = User::factory()->create(['active' => false]);

        $this->assertTrue($activeUser->isActive());
        $this->assertFalse($inactiveUser->isActive());
    }

    /** @test */
    public function it_sets_password_as_hashed()
    {
        $password = 'password123';
        $user = User::factory()->create(['password' => $password]);

        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    /** @test */
    public function it_has_orders_relationship()
    {
        $user = User::factory()->create();
        \App\Models\Order::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $user->orders());
        $this->assertCount(3, $user->orders);
    }

    /** @test */
    public function it_can_scope_active_users()
    {
        User::factory()->count(3)->create(['active' => true]);
        User::factory()->count(2)->create(['active' => false]);

        $this->assertEquals(3, User::active()->count());
    }

    /** @test */
    public function it_can_scope_by_role()
    {
        User::factory()->count(2)->create(['role' => UserRole::ADMIN->value]);
        User::factory()->count(3)->create(['role' => UserRole::MANAGER->value]);
        User::factory()->count(4)->create(['role' => UserRole::USER->value]);

        $this->assertEquals(2, User::role(UserRole::ADMIN)->count());
        $this->assertEquals(3, User::role(UserRole::MANAGER)->count());
        $this->assertEquals(4, User::role(UserRole::USER)->count());
    }
}
