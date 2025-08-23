<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\NotificationBranch;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationBranch $notification
    ) {
    }

    /**
     * Show notification list
     */
    public function index(Request $request): Factory|View|Application
    {
        $queryParam = [];
        $search = $request->get('search');

        $notifications = $this->notification->query();

        if ($search) {
            $key = explode(' ', $search);
            $notifications->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%")
                        ->orWhere('description', 'like', "%{$value}%");
                }
            });
            $queryParam = ['search' => $search];
        }

        $notifications = $notifications->latest()
            ->paginate(Helpers::pagination_limit())
            ->appends($queryParam);

        return view('branch-views.notification.index', compact('notifications', 'search'));
    }

    /**
     * Store new notification
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            $image_name = null;
            // In store method
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('notification', 'public');
                $image_name = basename($path);

                // Debugging - log the storage path
                \Log::info('Image stored at: ' . $path);
                \Log::info('Public URL would be: ' . Storage::disk('public')->url($path));
            }

            $notification = $this->notification->create([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $image_name,
                'status' => 1,
                'type' => 'general'
            ]);

            Helpers::send_push_notif_to_topic($notification, 'general');
            Toastr::success(translate('Notification sent successfully!'));
        } catch (\Exception $e) {
            \Log::error('Notification error: ' . $e->getMessage());
            Toastr::error(translate('Failed to send notification!'));
        }

        return back();
    }

    /**
     * Edit notification
     */
    public function edit($id): Factory|View|Application
    {
        $notification = $this->notification->findOrFail($id);
        return view('branch-views.notification.edit', compact('notification'));
    }

    /**
     * Update notification
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            $notification = $this->notification->findOrFail($id);
            $notification->title = $request->title;
            $notification->description = $request->description;

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($notification->image) {
                    $this->deleteImage($notification->image);
                }

                $path = $request->file('image')->store('notification', 'public');
                $notification->image = basename($path);
            }

            $notification->save();
            Toastr::success(translate('Notification updated successfully!'));
        } catch (\Exception $e) {
            \Log::error('Notification update error: ' . $e->getMessage());
            Toastr::error(translate('Failed to update notification!'));
        }

        return back();
    }

    /**
     * Update status
     */
    public function status($id, $status): RedirectResponse
    {
        try {
            $notification = $this->notification->findOrFail($id);
            $notification->status = $status;
            $notification->save();
            Toastr::success(translate('Notification status updated!'));
        } catch (\Exception $e) {
            \Log::error('Status update error: ' . $e->getMessage());
            Toastr::error(translate('Notification not found!'));
        }

        return back();
    }

    /**
     * Delete notification
     */
    public function delete($id): RedirectResponse
    {
        try {
            $notification = $this->notification->findOrFail($id);

            if ($notification->image) {
                $this->deleteImage($notification->image);
            }

            $notification->delete();
            Toastr::success(translate('Notification removed!'));
        } catch (\Exception $e) {
            \Log::error('Notification deletion error: ' . $e->getMessage());
            Toastr::error(translate('Notification not found!'));
        }

        return back();
    }

    /**
     * Helper method to delete image
     */
    private function deleteImage(string $imageName): void
    {
        $imagePath = 'notification/' . $imageName;
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}