<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchConversation;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BranchConversationController extends Controller
{
    public function __construct(
        private Branch $branch,
        private BranchConversation $conversation,
        private User $user
    ) {
    }

    /**
     * @return Application|Factory|View
     */
    public function list(): View|Factory|Application
    {
        $conversations = DB::table('branch_conversations')->latest()->get();
        return view('branch-views.messages.index', compact('conversations'));
    }

    /**
     * @param $user_id
     * @return JsonResponse
     */
    public function view($user_id): JsonResponse
    {
        $conversations = $this->conversation->where('user_id', $user_id)
            ->orderBy('created_at', 'asc')
            ->get();

        $this->conversation->where('user_id', $user_id)->update(['checked' => 1]);
        $user = $this->user->find($user_id);

        return response()->json([
            'view' => view('branch-views.messages.partials._conversations', compact('conversations', 'user'))->render()
        ]);
    }

    /**
     * @param Request $request
     * @param $user_id
     * @return JsonResponse
     */
    public function store(Request $request, $user_id): JsonResponse
    {
        if (!$request->reply && empty($request->file('images'))) {
            return response()->json([], 403);
        }

        if ($request->images) {
            $id_img_names = [];
            foreach ($request->images as $img) {
                $image = Helpers::upload('branch_conversation/', 'png', $img);
                $image_url = $image;
                $id_img_names[] = $image_url;
            }
            $images = $id_img_names;
        } else {
            $images = null;
        }

        DB::table('branch_conversations')->insert([
            'user_id' => $user_id,
            'reply' => $request->reply,
            'attachment' => json_encode($images),
            'checked' => 1,
            'is_reply' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $userConversation = $this->conversation->where(['user_id' => $user_id])->get();
        $user = $this->user->find($user_id);

        $fcm_token = $user->cm_firebase_token;
        $data = [
            'title' => translate('New message arrived'),
            'description' => Str::limit($request->reply ?? '', 500),
            'order_id' => '',
            'image' => '',
            'type' => 'message',
        ];
        try {
            Helpers::send_push_notif_to_device($fcm_token, $data);
        } catch (\Exception $exception) {
            //
        }

        return response()->json([
            'view' => view('branch-views.messages.partials._conversations', compact('userConversation', 'user'))->render()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        try {
            $branch = $this->branch->find(auth('branch')->id());
            $branch->fcm_token = $request->fcm_token;
            $branch->save();

            return response()->json(['message' => 'FCM token updated successfully.'], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'FCM token updated failed.'], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getConversations(Request $request): JsonResponse
    {
        $conversations = DB::table('branch_conversations')->latest()->get();
        return response()->json([
            'conversation_sidebar' => view('branch-views.messages.partials._list', compact('conversations'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    public function getFirebaseConfig(Request $request)
    {
        $config = Helpers::get_business_settings('firebase_message_config');
        return $config;
    }

}
