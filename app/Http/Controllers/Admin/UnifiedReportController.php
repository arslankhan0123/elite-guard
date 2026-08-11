<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportDailyShiftForm;
use App\Models\ReportGeneralForm;
use App\Models\ReportIncidentForm;
use App\Models\ReportSecurityGuardDisciplinaryForm;
use App\Models\Assessment;
use App\Models\DailyVehicleChecklist;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UnifiedReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'general'); // Default to general report
        $users = User::all();
        $data = [];
        $view = 'admin.unified-reports.partials.' . $type;

        switch ($type) {
            case 'disciplinary':
                $query = ReportSecurityGuardDisciplinaryForm::with('user');
                $this->applyFilters($query, $request);
                $data['reports'] = $query->latest()->paginate(10)->withQueryString();
                break;
            case 'incident':
                $query = ReportIncidentForm::with(['user', 'images']);
                $this->applyFilters($query, $request);
                $data['reports'] = $query->latest()->paginate(10)->withQueryString();
                break;
            case 'general':
                $query = ReportGeneralForm::with(['user', 'images']);
                $this->applyFilters($query, $request);
                $data['reports'] = $query->latest()->paginate(10)->withQueryString();
                break;
            case 'daily-shift':
                $query = ReportDailyShiftForm::with(['user', 'patrolEntries']);
                $this->applyFilters($query, $request);
                $data['reports'] = $query->latest()->paginate(10)->withQueryString();
                break;
            case 'assessments':
                $query = Assessment::with('user');
                $this->applyFilters($query, $request);
                $data['assessments'] = $query->latest()->paginate(10)->withQueryString();
                break;
            case 'vehicle-checklist':
                $query = DailyVehicleChecklist::with(['user', 'issueImages']);
                $this->applyFilters($query, $request);
                if ($request->document_status) {
                    if ($request->document_status == 'uploaded') {
                        $query->whereNotNull('documents');
                    } elseif ($request->document_status == 'not_uploaded') {
                        $query->whereNull('documents');
                    }
                }
                $data['checklists'] = $query->latest()->paginate(10)->withQueryString();
                break;
            case 'fire-watch':
                $query = \App\Models\FireWatchReport::with(['user', 'patrolLogs']);
                $this->applyFilters($query, $request);
                $data['reports'] = $query->latest()->paginate(10)->withQueryString();
                break;
            case 'shift-adjustment':
                $query = \App\Models\ShiftAdjustmentForm::with('user');
                $this->applyFilters($query, $request);
                $data['adjustments'] = $query->latest()->paginate(10)->withQueryString();
                break;
        }

        return view('admin.unified-reports.index', compact('users', 'type', 'data'));
    }

    private function applyFilters($query, $request)
    {
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_range) {
            $range = $request->date_range;
            switch ($range) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'current_week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                    break;
                case 'current_month':
                    $query->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                          ->whereYear('created_at', Carbon::now()->subMonth()->year);
                    break;
                case 'current_year':
                    $query->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
            }
        }
    }

    public function show($type, $id)
    {
        $report = $this->getReportInstance($type, $id);
        $title = ucwords(str_replace('-', ' ', $type)) . ' Details';
        $attributes = $this->displayAttributes($report);

        return view('admin.unified-reports.show', compact('report', 'type', 'title', 'attributes'));
    }

    public function edit($type, $id)
    {
        $report = $this->getReportInstance($type, $id);
        $title = 'Edit ' . ucwords(str_replace('-', ' ', $type)) . ' #' . $id;

        $attributes = $this->displayAttributes($report)->except($this->mediaFields());

        return view('admin.unified-reports.edit', compact('report', 'type', 'title', 'attributes'));
    }

    public function update(Request $request, $type, $id)
    {
        $report = $this->getReportInstance($type, $id);
        $attributes = $this->displayAttributes($report)->except($this->mediaFields());

        $rules = [];
        foreach ($attributes as $key => $val) {
            $cast = $report->getCasts()[$key] ?? null;
            if (in_array($cast, ['date', 'datetime', 'immutable_date', 'immutable_datetime'], true)
                && is_string($request->input($key))) {
                $request->merge([$key => trim($request->input($key), "\"'")]);
            }
            $rules[$key] = in_array($cast, ['date', 'datetime', 'immutable_date', 'immutable_datetime'], true)
                ? 'nullable|date'
                : 'nullable';
        }
        $validated = $request->validate($rules);

        foreach ($validated as $key => $value) {
            $cast = $report->getCasts()[$key] ?? null;
            if ($cast === 'boolean') {
                $validated[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                continue;
            }

            if ($value !== null && in_array($cast, ['date', 'datetime', 'immutable_date', 'immutable_datetime'], true)) {
                $value = trim($value, "\"'");
                $validated[$key] = Carbon::parse($value)->format(
                    in_array($cast, ['datetime', 'immutable_datetime'], true) ? 'Y-m-d H:i:s' : 'Y-m-d'
                );
            }

            if ($report->hasCast($key, ['array', 'json', 'object', 'collection']) && is_string($value)) {
                $validated[$key] = json_decode($value, true) ?? $value;
            }
        }

        DB::transaction(function () use ($report, $validated, $request) {
            $report->update($validated);

            foreach ($this->editableRelations($report) as $relationName => $items) {
                foreach ($request->input("relations.$relationName", []) as $relationId => $values) {
                    $item = $items->firstWhere('id', (int) $relationId);
                    if ($item) {
                        $item->update(collect($values)->only($item->getFillable())->all());
                    }
                }
            }
        });

        return redirect()->route('reports.all', ['type' => $type])->with('success', ucwords(str_replace('-', ' ', $type)) . ' updated successfully!');
    }

    public function downloadPdf($type, $id)
    {
        $report = $this->getReportInstance($type, $id);
        $title = ucwords(str_replace('-', ' ', $type)) . ' Report';
        $attributes = $this->displayAttributes($report);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.unified-reports.pdf', compact('report', 'type', 'title', 'attributes'));
        return $pdf->download(str_replace(' ', '_', strtolower($title)) . '_' . $id . '.pdf');
    }

    public function destroy($type, $id)
    {
        $report = $this->getReportInstance($type, $id);
        $files = $this->reportFiles($report);

        DB::transaction(function () use ($report) {
            foreach (['images', 'issueImages', 'patrolLogs', 'patrolEntries'] as $relation) {
                if ($report->relationLoaded($relation)) {
                    $report->getRelation($relation)->each->delete();
                }
            }

            $report->delete();
        });

        foreach ($files as $file) {
            File::delete($file);
        }

        return redirect()->route('reports.all', ['type' => $type])
            ->with('success', ucwords(str_replace('-', ' ', $type)) . ' deleted successfully!');
    }

    private function getReportInstance($type, $id)
    {
        $config = [
            'disciplinary' => [ReportSecurityGuardDisciplinaryForm::class, ['user']],
            'incident' => [ReportIncidentForm::class, ['user', 'images']],
            'general' => [ReportGeneralForm::class, ['user', 'images']],
            'daily-shift' => [ReportDailyShiftForm::class, ['user', 'patrolEntries']],
            'assessments' => [Assessment::class, ['user']],
            'vehicle-checklist' => [DailyVehicleChecklist::class, ['user', 'issueImages']],
            'fire-watch' => [\App\Models\FireWatchReport::class, ['user', 'patrolLogs']],
            'shift-adjustment' => [\App\Models\ShiftAdjustmentForm::class, ['user']],
        ];

        abort_unless(isset($config[$type]), 404);

        [$model, $relations] = $config[$type];

        return $model::with($relations)->findOrFail($id);
    }

    private function displayAttributes($report)
    {
        return collect(array_keys($report->getAttributes()))
            ->reject(fn ($key) => in_array($key, ['id', 'user_id', 'created_at', 'updated_at', 'deleted_at']))
            ->mapWithKeys(fn ($key) => [$key => $report->getAttribute($key)]);
    }

    private function mediaFields(): array
    {
        return ['signature', 'employee_signature', 'supervisor_signature', 'documents'];
    }

    private function reportFiles($report): array
    {
        $values = collect($this->mediaFields())
            ->map(fn ($field) => $report->getAttribute($field));

        foreach (['images', 'issueImages'] as $relation) {
            if ($report->relationLoaded($relation)) {
                $values = $values->merge(
                    $report->getRelation($relation)->flatMap(
                        fn ($item) => collect(['image_path', 'observation_image_path', 'cleared_area_image_path', 'path'])
                            ->map(fn ($field) => $item->getAttribute($field))
                    )
                );
            }
        }

        return $values->filter()
            ->map(fn ($value) => $this->localFilePath($value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function localFilePath($value): ?string
    {
        if (! is_string($value) || str_starts_with($value, 'data:')) {
            return null;
        }

        $path = rawurldecode(parse_url($value, PHP_URL_PATH) ?: $value);
        $path = ltrim(str_replace('\\', '/', $path), '/');
        $candidates = str_starts_with($path, 'storage/')
            ? [storage_path('app/public/' . substr($path, 8))]
            : [public_path($path), storage_path('app/public/' . $path)];

        $roots = [realpath(public_path()), realpath(storage_path('app/public'))];
        foreach ($candidates as $candidate) {
            $real = realpath($candidate);
            if ($real && collect($roots)->filter()->contains(
                fn ($root) => $real === $root || str_starts_with($real, $root . DIRECTORY_SEPARATOR)
            )) {
                return $real;
            }
        }

        return null;
    }

    private function editableRelations($report): array
    {
        return collect(['patrolLogs', 'patrolEntries'])
            ->filter(fn ($relation) => $report->relationLoaded($relation))
            ->mapWithKeys(fn ($relation) => [$relation => $report->getRelation($relation)])
            ->all();
    }
}
