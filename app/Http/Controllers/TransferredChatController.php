<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use Illuminate\Http\Request;

class TransferredChatController extends Controller
{
    /**
     * عرض صفحة الدردشة المحولة (بيانات من قاعدة store_agent)
     */
    public function index(Request $request)
    {
        $chats = ChatSession::where('agent_type', 'human')
            ->orderByDesc('updated_at')
            ->get();

        return view('transferred-chat.index', compact('chats'));
    }

    /**
     * تحديث نوع المحادثة (ذكاء اصطناعي / إنسان) في جدول chat_sessions
     */
    public function updateType(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:human,greeting',
        ]);

        $session = ChatSession::find($id);

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'المحادثة غير موجودة'], 404);
        }

        $session->agent_type = $request->type;
        $session->save();

        return response()->json([
            'success' => true,
            'type' => $request->type,
        ]);
    }
}
