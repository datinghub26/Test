<?php

namespace App\Livewire\User;

use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Notifications extends Component
{
    public $notifications = [];
    public $unreadNotificationsCount = 0;


    /**
     * @return array
     */
    public function getListeners(): array
    {
        return [
            "echo:App.Models.User." . auth()->id() . ",NotificationCreated" => 'checkForNewNotifications',
        ];
    }

    public function render()
    {
        return view('livewire.user.notifications');
    }

    public function loadNotifications(): void
    {
        if (!auth()->check())
            return;


        $this->notifications = auth()->user()->alerts()->orderBy('created_at', 'desc')->get();
        $this->unreadNotificationsCount = auth()->user()->alerts()->where('is_read', 0)->count();
    }

    public function markAllAsRead(): void
    {
        if (!auth()->check())
            return;

        auth()->user()->alerts()->update(['is_read' => 1]);
        $this->unreadNotificationsCount = 0;
    }

//    #[On('echo:App.Models.User.{auth()->id()},NotificationCreated')]
    public function checkForNewNotifications(): void
    {
        if (!auth()->check())
            return;

        $unNotifiedNotificationsCount = auth()->user()->alerts()->where('is_notified', 0)->count();
        if ($unNotifiedNotificationsCount > 0) {
            $this->loadNotifications();
            Toaster::success("You have a $unNotifiedNotificationsCount new notifications");
            auth()->user()->alerts()->update(['is_notified' => 1]);
        }
    }


}
