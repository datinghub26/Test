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

        // Auto-mark any older notifications as notified so they never trigger audio on login
        auth()->user()->alerts()
            ->where('is_notified', 0)
            ->where('created_at', '<', now()->subMinutes(5))
            ->update(['is_notified' => 1]);

        $unNotified = auth()->user()->alerts()
            ->where('is_notified', 0)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->get();

        if ($unNotified->isNotEmpty()) {
            $this->loadNotifications();
            $shouldUpdateBalance = false;

            // Immediately mark as notified in database to prevent re-triggering
            $notificationIds = $unNotified->pluck('id')->toArray();
            auth()->user()->alerts()->whereIn('id', $notificationIds)->update(['is_notified' => 1]);

            foreach ($unNotified as $notification) {
                $type = strtolower($notification->type ?? '');
                $title = strtolower($notification->title ?? '');

                // 1. Offer complete
                $isOfferComplete = in_array($type, ['offer_completed', 'offer_approved'])
                    || str_contains($title, 'offer completed')
                    || str_contains($title, 'offer approved');

                // 2. Make withdrawal request
                $isWithdrawRequest = in_array($type, ['cashout_submitted', 'withdraw_request'])
                    || str_contains($title, 'withdraw request')
                    || str_contains($title, 'withdrawal request');

                // 3. After making the payment (Withdrawal Approved / Processed)
                $isPaymentMade = in_array($type, ['cashout_approved', 'withdrawal_approved', 'payment_completed'])
                    || str_contains($title, 'withdrawal request approved')
                    || str_contains($title, 'cashout approved')
                    || str_contains($title, 'cashout processed');

                // 4. After chargeback
                $isChargeback = in_array($type, ['chargeback', 'offer_chargeback'])
                    || str_contains($title, 'chargeback')
                    || str_contains($title, 'charge back');

                if ($isOfferComplete) {
                    $shouldUpdateBalance = true;
                    Toaster::success($notification->message);
                    $this->dispatch('play-notification-sound', id: 'notif_' . $notification->id);
                } elseif ($isWithdrawRequest) {
                    $shouldUpdateBalance = true;
                    Toaster::info($notification->message);
                    $this->dispatch('play-notification-sound', id: 'notif_' . $notification->id);
                } elseif ($isPaymentMade) {
                    $shouldUpdateBalance = true;
                    Toaster::success($notification->message);
                    $this->dispatch('play-notification-sound', id: 'notif_' . $notification->id);
                } elseif ($isChargeback) {
                    $shouldUpdateBalance = true;
                    Toaster::error($notification->message);
                    $this->dispatch('play-notification-sound', id: 'notif_' . $notification->id);
                } else {
                    Toaster::info($notification->message);
                }
            }

            if ($shouldUpdateBalance) {
                $this->dispatch('update-balance', balance: (float) auth()->user()->fresh()->points);
            }
        }
    }


}
