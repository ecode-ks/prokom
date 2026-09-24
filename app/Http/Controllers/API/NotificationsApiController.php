<?php

namespace App\Http\Controllers\API;

use App\Services\Api\NotificationsApiService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationsApiController extends Controller
{
    protected NotificationsApiService $service;

    public function __construct(NotificationsApiService $service)
    {
        $this->service = $service;
    }

    private function bindFromRequest(Request $request): ?array
    {
        return $this->service->bindRecipient($request->user());
    }

    public function read(Request $request)
    {
        if (!$this->bindFromRequest($request)) {
            return json_encode(['error' => __('pages.notif_no_access')]);
        }

        $notifications = $this->service->getNotifications(10);
        $unreadCount = $this->service->countUnread();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function open(Request $request, int $id)
    {
        if (!$this->bindFromRequest($request)) {
            return json_encode(['error' => __('pages.notif_no_access')]);
        }

        $link = $this->service->markAsRead($id);
        if (!$link) {
            return json_encode(['error' => __('pages.notif_not_found')]);
        }

        return response()->json(['link' => $link]);
    }

    public function markAllRead(Request $request)
    {
        if (!$this->bindFromRequest($request)) {
            return json_encode(['error' => __('pages.notif_no_access')]);
        }

        $this->service->markAllAsRead();

        return json_encode(['success' => __('pages.notif_all_read')]);
    }

    public function registerPushToken(Request $request)
    {
        if (!$this->bindFromRequest($request)) {
            return response()->json(['error' => __('pages.notif_no_access')], 403);
        }

        $saved = $this->service->registerPushToken(
            (string) $request->input('token'),
            $request->input('platform')
        );

        if (!$saved) {
            return response()->json(['error' => __('pages.push_token_save_failed')], 422);
        }

        return response()->json(['success' => true]);
    }
}
