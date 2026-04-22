<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_renders(): void
    {
        $this->get('/admin')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Login'));
    }

    public function test_unauthorized_admin_requests_redirect_to_login(): void
    {
        $this->get('/admin/dashboard')
            ->assertRedirect('/admin');
    }

    public function test_admin_can_login_with_valid_password(): void
    {
        config(['admin.password_hash' => Hash::make('secret-passphrase')]);

        $this->post('/admin/login', [
            'password' => 'secret-passphrase',
        ])->assertRedirect('/admin/dashboard')
            ->assertSessionHas(config('admin.session_key'), true);
    }

    public function test_admin_can_login_with_database_password_override(): void
    {
        config(['admin.password_hash' => Hash::make('fallback-secret-passphrase')]);
        Setting::setValue(config('admin.password_setting_key'), Hash::make('database-secret-passphrase'));

        $this->post('/admin/login', [
            'password' => 'database-secret-passphrase',
        ])->assertRedirect('/admin/dashboard')
            ->assertSessionHas(config('admin.session_key'), true);
    }
}
