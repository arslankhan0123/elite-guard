<?php

namespace App\Http\Controllers;

use App\Models\PostEsc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostEscController extends Controller
{
    public function index()
    {
        $posts = PostEsc::orderBy('date', 'desc')->get();

        return view('admin.post-esc.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.post-esc.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        if ($request->hasFile('pdf')) {
            $data['pdf_path'] = $request->file('pdf')->store('documents/post-esc', 'public');
        }

        unset($data['pdf']);
        PostEsc::create($data);

        return redirect()->route('post-esc.index')->with('success', 'Post & ESC created successfully.');
    }

    public function edit($id)
    {
        $post = PostEsc::findOrFail($id);

        return view('admin.post-esc.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = PostEsc::findOrFail($id);
        $data = $request->validate($this->rules());

        if ($request->hasFile('pdf')) {
            $newPath = $request->file('pdf')->store('documents/post-esc', 'public');

            if ($post->pdf_path) {
                Storage::disk('public')->delete($post->pdf_path);
            }

            $data['pdf_path'] = $newPath;
        }

        unset($data['pdf']);
        $post->update($data);

        return redirect()->route('post-esc.index')->with('success', 'Post & ESC updated successfully.');
    }

    public function download($id)
    {
        $post = PostEsc::findOrFail($id);

        abort_unless($post->pdf_path && Storage::disk('public')->exists($post->pdf_path), 404);

        return Storage::disk('public')->download(
            $post->pdf_path,
            pathinfo($post->pdf_path, PATHINFO_BASENAME)
        );
    }

    public function destroy($id)
    {
        $post = PostEsc::findOrFail($id);

        if ($post->pdf_path) {
            Storage::disk('public')->delete($post->pdf_path);
        }

        $post->delete();

        return redirect()->route('post-esc.index')->with('success', 'Post & ESC deleted successfully.');
    }

    private function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'subject' => ['required', 'string', 'max:255'],
            'long_description' => ['required', 'string'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
