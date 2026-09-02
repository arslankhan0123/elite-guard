<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NoticeBoard;
use Illuminate\Http\Request;

class NoticeBoardController extends Controller
{
    public function index()
    {
        $notices = NoticeBoard::orderBy('date', 'desc')->get();
        $data = [
            'status' => true,
            'message' => 'Notice Board Data fetched successfully.',
            'notices' => $notices
        ];
        return view('admin.notice-board.index', compact('data'));
    }

    public function create()
    {
        return view('admin.notice-board.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'subject' => 'required|string|max:255',
            'long_description' => 'required|string',
        ]);

        $notice = NoticeBoard::create($request->all());

        // Dispatch FCM Push Notification to all devices
        try {
            $users = \App\Models\User::whereNotNull('fcm_token')->get();
            if ($users->isNotEmpty() && $notice) {
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\NoticeBoardNotification($notice, false));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("NoticeBoard FCM Notification failed: " . $e->getMessage());
        }

        return redirect()->route('notice-board.index')->with('success', 'Notice created successfully.');
    }

    public function edit($id)
    {
        $notice = NoticeBoard::findOrFail($id);
        return view('admin.notice-board.edit', compact('notice'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'subject' => 'required|string|max:255',
            'long_description' => 'required|string',
        ]);

        $notice = NoticeBoard::findOrFail($id);
        $notice->update($request->all());

        // Dispatch FCM Push Notification for update
        try {
            $users = \App\Models\User::whereNotNull('fcm_token')->get();
            if ($users->isNotEmpty() && $notice) {
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\NoticeBoardNotification($notice, true));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("NoticeBoard FCM Notification update failed: " . $e->getMessage());
        }

        return redirect()->route('notice-board.index')->with('success', 'Notice updated successfully.');
    }

    public function destroy($id)
    {
        $notice = NoticeBoard::findOrFail($id);
        $notice->delete();

        return redirect()->route('notice-board.index')->with('success', 'Notice deleted successfully.');
    }
}
