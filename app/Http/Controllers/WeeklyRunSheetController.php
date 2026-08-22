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
                'monday_start_time' => $validated['monday_start_time'],
                'monday_end_time' => $validated['monday_end_time'],
                'tuesday_start_time' => $validated['tuesday_start_time'],
                'tuesday_end_time' => $validated['tuesday_end_time'],
                'wednesday_start_time' => $validated['wednesday_start_time'],
                'wednesday_end_time' => $validated['wednesday_end_time'],
                'thursday_start_time' => $validated['thursday_start_time'],
                'thursday_end_time' => $validated['thursday_end_time'],
                'friday_start_time' => $validated['friday_start_time'],
                'friday_end_time' => $validated['friday_end_time'],
                'saturday_start_time' => $validated['saturday_start_time'],
                'saturday_end_time' => $validated['saturday_end_time'],
                'sunday_start_time' => $validated['sunday_start_time'],
                'sunday_end_time' => $validated['sunday_end_time'],
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
                'monday_start_time' => $validated['monday_start_time'],
                'monday_end_time' => $validated['monday_end_time'],
                'tuesday_start_time' => $validated['tuesday_start_time'],
                'tuesday_end_time' => $validated['tuesday_end_time'],
                'wednesday_start_time' => $validated['wednesday_start_time'],
                'wednesday_end_time' => $validated['wednesday_end_time'],
                'thursday_start_time' => $validated['thursday_start_time'],
                'thursday_end_time' => $validated['thursday_end_time'],
                'friday_start_time' => $validated['friday_start_time'],
                'friday_end_time' => $validated['friday_end_time'],
                'saturday_start_time' => $validated['saturday_start_time'],
                'saturday_end_time' => $validated['saturday_end_time'],
                'sunday_start_time' => $validated['sunday_start_time'],
                'sunday_end_time' => $validated['sunday_end_time'],
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
            'monday_start_time' => ['required', 'date_format:H:i'],
            'monday_end_time' => ['required', 'date_format:H:i'],
            'tuesday_start_time' => ['required', 'date_format:H:i'],
            'tuesday_end_time' => ['required', 'date_format:H:i'],
            'wednesday_start_time' => ['required', 'date_format:H:i'],
            'wednesday_end_time' => ['required', 'date_format:H:i'],
            'thursday_start_time' => ['required', 'date_format:H:i'],
            'thursday_end_time' => ['required', 'date_format:H:i'],
            'friday_start_time' => ['required', 'date_format:H:i'],
            'friday_end_time' => ['required', 'date_format:H:i'],
            'saturday_start_time' => ['required', 'date_format:H:i'],
            'saturday_end_time' => ['required', 'date_format:H:i'],
            'sunday_start_time' => ['required', 'date_format:H:i'],
            'sunday_end_time' => ['required', 'date_format:H:i'],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.day_of_week' => ['required', 'integer', 'between:1,7'],
            'entries.*.site_id' => ['required', 'integer', 'exists:sites,id'],
            'entries.*.tour_name' => ['required', 'string', 'max:255'],
            'entries.*.start_time' => ['nullable', 'date_format:H:i'],
            'entries.*.end_time' => ['nullable', 'date_format:H:i'],
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
                'start_time' => !empty($entry['start_time']) ? $entry['start_time'] : null,
                'end_time' => !empty($entry['end_time']) ? $entry['end_time'] : null,
                'sequence' => $sequences[$day],
            ];
        })->all();
    }
}
