<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AdminWelcomeModalWidget extends Widget
{
    protected static string $view = 'filament.widgets.admin-welcome-modal-widget';

    public bool $open = false;

    public string $greeting = '';

    public function mount(): void
    {
        $this->open = (bool) session()->pull('admin_welcome_modal', false);
        $this->greeting = $this->buildGreeting();
    }

    public function openModal(): void
    {
        $this->dispatch('open-modal', id: 'admin-welcome-modal');
    }

    public function closeModal(): void
    {
        $this->open = false;
    }

    private function buildGreeting(): string
    {
        $hour = (int) now()->format('G');

        if ($hour >= 17) {
            $timeOfDay = 'evening';
        } elseif ($hour >= 12) {
            $timeOfDay = 'afternoon';
        } else {
            $timeOfDay = 'morning';
        }

        return "Hello Brighton — good {$timeOfDay}.";
    }
}
