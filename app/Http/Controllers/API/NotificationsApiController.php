<?php

namespace App\Http\Controllers\API;

use App\Services\NotificationsService;
use App\Http\Controllers\Controller;

class NotificationsApiController extends Controller
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
            return json_encode(['error' => __('pages.notif_no_access')]);
        }

        $notifications = $this->service->getNotifications(10);
        $unreadCount = $this->service->countUnread();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
    //---------------
    public function open(int $id)
    {
        $link = $this->service->markAsRead($id);
        if (!$link) {
            return json_encode(['error' => __('pages.notif_not_found')]);
        }

        return response()->json(['link' => $link]);
    }
    //---------------
    public function markAllRead()
    {
        if (!$this->service->resolveRecipient()) {
            return json_encode(['error' => __('pages.notif_no_access')]);
        }

        $this->service->markAllAsRead();

        return json_encode(['success' => __('pages.notif_all_read')]);
    }
}
