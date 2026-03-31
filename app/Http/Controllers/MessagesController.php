<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\Message\StoreMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $chats = Conversation::where('user_one', $userId)
            ->orWhere('user_two', $userId)
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }, 'userOne', 'userTwo'])
            ->get()
            ->map(function ($chat) use ($userId) {
                $interlocutor = $chat->user_one === $userId ? $chat->userTwo : $chat->userOne;
                return [
                    'id' => $chat->id,
                    'interlocutor' => [
                        'id' => $interlocutor->id,
                        'name' => $interlocutor->name,
                        'role' => $interlocutor->role,
                        'avatar' => "https://ui-avatars.com/api/?name=" . urlencode($interlocutor->name) . "&background=6366f1&color=fff&size=150"
                    ],
                    'last_message' => $chat->messages->last()?->text ?? '',
                    'messages' => $chat->messages
                ];
            });

        return Inertia::render('Messages', [
            'chats' => $chats,
            'activeChatId' => $request->query('chat') ? (int) $request->query('chat') : null
        ]);
    }

    public function store(StoreMessage $request)
    {
        $request->validated();

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => Auth::id(),
            'text' => $request->text
        ]);

        $conversation = Conversation::find($request->conversation_id);
        $recipientId = $conversation->user_one === Auth::id()
            ? $conversation->user_two
            : $conversation->user_one;

        Notification::create([
            'user_id' => $recipientId,
            'title' => 'Новое сообщение',
            'desc' => Auth::user()->name . ': "' . mb_strimwidth($request->text, 0, 50, "...") . '"',
            'type' => 'message'
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return back();
    }

    public function startConversation(User $user)
    {
        $authId = Auth::id();
        $curatorId = $user->id;

        if ($authId === $curatorId) {
            return redirect()->route('messages.index');
        }

        $conversation = Conversation::where(function ($query) use ($authId, $curatorId) {
            $query->where('user_one', $authId)->where('user_two', $curatorId);
        })->orWhere(function ($query) use ($authId, $curatorId) {
            $query->where('user_one', $curatorId)->where('user_two', $authId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one' => $authId,
                'user_two' => $curatorId
            ]);
        }

        return redirect()->route('messages.index', ['chat' => $conversation->id]);
    }
}
