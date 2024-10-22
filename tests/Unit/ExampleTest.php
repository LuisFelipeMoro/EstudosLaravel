<?php

namespace Tests\Unit;

use Tests\TestCase; 
use App\Models\User;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Cache; 
use Illuminate\Database\QueryException;

class ExampleTest extends TestCase
{
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    public function test_caching()
    {
        Cache::flush();

        Cache::put('key', 'value', 10);

        $this->assertEquals('value', Cache::get('key'));

        Cache::flush();
    }
    
    public function test_model_creation_and_validation()
    {
        $user = User::create([
            'name' => 'Test', 
            'email' => 'test@example.com',
            'password' => bcrypt('securepassword'),
        ]);
        
        $this->assertNotNull($user->id);
        $this->assertEquals('Test', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertTrue($user->save());
    }

    public function test_email_uniqueness()
    {
        User::create([
            'name' => 'First',
            'email' => 'test@example.com',
            'password' => bcrypt('securepassword'),
        ]);

        try {
            User::create([
                'name' => 'Second',
                'email' => 'test@example.com',
                'password' => bcrypt('anotherpassword'),
            ]);
            $this->fail('Expected QueryException was not thrown.');
        } catch (QueryException $e) {
            $this->assertStringContainsString('duplicate key value violates unique constraint', $e->getMessage());
        }

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'First',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
            'name' => 'Second',
        ]);
    }
}
