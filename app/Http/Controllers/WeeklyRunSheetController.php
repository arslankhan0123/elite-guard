<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\WeeklyRunSheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WeeklyRunSheetController extends Controller
{
    public function index(Request $request)
    {
        $query = WeeklyRunSheet::query()
            ->with('entries:id,weekly_run_sheet_id,site_id')
            ->withCount('entries')
            ->orderByDesc('week_start_date');

        if ($request->filled('week')) {
            $query->whereDate('week_start_date', Carbon::parse($request->input('week'))->startOfWeek(Carbon::MONDAY));
        }

        $runSheets = $query->get();

        return view('admin.weekly-run-sheets.index', compact('runSheets'));
    }

    public function create()
    {
        return view('admin.weekly-run-sheets.form', [
            'runSheet' => new WeeklyRunSheet([
                'week_start_date' => now()->startOfWeek(Carbon::MONDAY),
            ]),
            'sites' => Site::where('status', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(WeeklyRunSheet $weeklyRunSheet)
    {
        $weeklyRunSheet->load('entries.site');

        return view('admin.weekly-run-sheets.show', ['runSheet' => $weeklyRunSheet]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRunSheet($request);

        DB::transaction(function () use ($validated) {
            $runSheet = WeeklyRunSheet::create([
                'name' => $validated['name'],
                'week_start_date' => Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString(),
                'notes' => $validated['notes'] ?? null,
            ]);

            $runSheet->entries()->createMany($this->normaliseEntries($validated['entries']));
        });

        return redirect()->route('weekly-run-sheets.index')->with('success', 'Weekly runsheet created successfully.');
    }

    public function edit(WeeklyRunSheet $weeklyRunSheet)
    {
        $weeklyRunSheet->load('entries.site');

        return view('admin.weekly-run-sheets.form', [
            'runSheet' => $weeklyRunSheet,
            'sites' => Site::where('status', true)->orWhereIn('id', $weeklyRunSheet->entries->pluck('site_id'))->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, WeeklyRunSheet $weeklyRunSheet)
    {
        $validated = $this->validateRunSheet($request);

        DB::transaction(function () use ($validated, $weeklyRunSheet) {
            $weeklyRunSheet->update([
                'name' => $validated['name'],
                'week_start_date' => $weeklyRunSheet->week_start_date ?? Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString(),
                'notes' => $validated['notes'] ?? null,
            ]);

            $weeklyRunSheet->entries()->delete();
            $weeklyRunSheet->entries()->createMany($this->normaliseEntries($validated['entries']));
        });

        return redirect()->route('weekly-run-sheets.index')->with('success', 'Weekly runsheet updated successfully.');
    }

    public function destroy(WeeklyRunSheet $weeklyRunSheet)
    {
        $weeklyRunSheet->delete();

        return redirect()->route('weekly-run-sheets.index')->with('success', 'Weekly runsheet deleted successfully.');
    }

    private function validateRunSheet(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.day_of_week' => ['required', 'integer', 'between:1,7'],
            'entries.*.site_id' => ['required', 'integer', 'exists:sites,id'],
            'entries.*.tour_name' => ['required', 'string', 'max:255'],
            'entries.*.start_time' => ['required', 'date_format:H:i'],
            'entries.*.end_time' => ['required', 'date_format:H:i'],
        ], [
            'entries.required' => 'Add at least one tour to the weekly runsheet.',
            'entries.min' => 'Add at least one tour to the weekly runsheet.',
        ]);
    }

    private function normaliseEntries(array $entries): array
    {
        $sequences = [];

        return collect($entries)->map(function (array $entry) use (&$sequences) {
            $day = (int) $entry['day_of_week'];
            $sequences[$day] = ($sequences[$day] ?? 0) + 1;

            return [
                'day_of_week' => $day,
                'site_id' => (int) $entry['site_id'],
                'tour_name' => $entry['tour_name'],
                'start_time' => $entry['start_time'],
                'end_time' => $entry['end_time'],
                'sequence' => $sequences[$day],
            ];
        })->all();
    }
}
