<?php

namespace App\Http\Controllers;

use App\Models\Number;
use App\Repositories\NumberRepository;
use Illuminate\Http\Request;

class NumberController extends Controller
{
    protected $numberRepo;

    public function __construct(NumberRepository $numberRepo)
    {
        $this->numberRepo = $numberRepo;
    }

    public function index()
    {
        $numbers = $this->numberRepo->getAllNumbers();
        return view('admin.numbers.index', compact('numbers'));
    }

    public function create()
    {
        return view('admin.numbers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'designation'      => 'required|string|max:255',
            'label'            => 'nullable|string|max:255',
            'number'           => 'required|string|max:20',
            'number_with_code' => 'nullable|string|max:30',
            'type'             => 'nullable|string|max:100',
            'status'           => 'required|boolean',
        ]);

        $this->numberRepo->createNumber($request);

        return redirect()->route('numbers.index')->with('success', 'Number created successfully.');
    }

    public function edit($id)
    {
        $number = $this->numberRepo->findNumberById($id);
        if (!$number) {
            return redirect()->route('numbers.index')->with('error', 'Number not found.');
        }
        return view('admin.numbers.edit', compact('number'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'designation'      => 'required|string|max:255',
            'label'            => 'nullable|string|max:255',
            'number'           => 'required|string|max:20',
            'number_with_code' => 'nullable|string|max:30',
            'type'             => 'nullable|string|max:100',
            'status'           => 'required|boolean',
        ]);

        $this->numberRepo->updateNumber($request, $id);

        return redirect()->route('numbers.index')->with('success', 'Number updated successfully.');
    }

    public function delete($id)
    {
        $this->numberRepo->deleteNumber($id);
        return redirect()->route('numbers.index')->with('success', 'Number deleted successfully.');
    }
}
