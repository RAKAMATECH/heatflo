<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class SetAdminWelcomeModal
{
    public function handle(Login $event): void
    {
        if (! request()->is('admin') && ! request()->is('admin/*')) {
            return;
        }

        session()->flash('admin_welcome_modal', true);
    }
}
