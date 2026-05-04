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

        NoticeBoard::create($request->all());

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

        return redirect()->route('notice-board.index')->with('success', 'Notice updated successfully.');
    }

    public function destroy($id)
    {
        $notice = NoticeBoard::findOrFail($id);
        $notice->delete();

        return redirect()->route('notice-board.index')->with('success', 'Notice deleted successfully.');
    }
}
