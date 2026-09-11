<?php

namespace App\Http\Controllers;

use App\Services\NotificationsService;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    protected NotificationsService $service;

    public function __construct(NotificationsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function read()
    {
        if (!$this->service->resolveRecipient()) {
            return redirect()->route('dashboard')->with('error', __('pages.notif_no_access'));
        }

        $page = 'Notifications';
        $notifications = $this->service->getNotifications(10);
        $unreadCount = $this->service->countUnread();

        return view('management.notifications')
            ->with('page', $page)
            ->with('notifications', $notifications)
            ->with('unreadCount', $unreadCount);
    }
    //---------------
    public function open(int $id)
    {
        $link = $this->service->markAsRead($id);
        if (!$link) {
            return redirect()->route('notifications')->with('error', __('pages.notif_not_found'));
        }

        return redirect()->to($link);
    }
    //---------------
    public function markAllRead()
    {
        if (!$this->service->resolveRecipient()) {
            return redirect()->route('dashboard')->with('error', __('pages.notif_no_access'));
        }

        $this->service->markAllAsRead();

        return redirect()->back()->with('success', __('pages.notif_all_read'));
    }
}
