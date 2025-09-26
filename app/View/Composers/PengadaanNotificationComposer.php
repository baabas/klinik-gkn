<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Helpers\PengadaanNotificationHelper;

class PengadaanNotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Hanya tambahkan notifikasi jika user adalah pengadaan
        if (PengadaanNotificationHelper::isPengadaanUser()) {
            $notifications = PengadaanNotificationHelper::getAllNotifications();
            $view->with('pengadaanNotifications', $notifications);
        } else {
            $view->with('pengadaanNotifications', [
                'pending_requests' => 0,
                'new_items_to_add' => 0,
                'approved_for_input' => 0,
                'total' => 0,
            ]);
        }
    }
}