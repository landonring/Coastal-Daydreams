<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_settings_page(): void
    {
        $this->withSession([config('admin.session_key') => true])
            ->get('/admin/settings')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Settings'));
    }

    public function test_admin_can_update_password_from_settings(): void
    {
        config(['admin.password_hash' => Hash::make('current-password-value')]);

        $this->withSession([config('admin.session_key') => true])
            ->put('/admin/settings/password', [
                'current_password' => 'current-password-value',
                'password' => 'a-brand-new-password',
                'password_confirmation' => 'a-brand-new-password',
            ])
            ->assertRedirect('/admin/settings');

        $storedHash = Setting::getValue(config('admin.password_setting_key'));

        $this->assertNotNull($storedHash);
        $this->assertTrue(Hash::check('a-brand-new-password', $storedHash));
    }
}
