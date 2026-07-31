<?php

namespace App\Services;

use App\Models\Orientation;
use App\Models\Policy;
use App\Models\TaxDocument;
use App\Models\User;

class ProfileCompletionService
{
    private const PERSONAL_FIELDS = [
        'first_name', 'last_name', 'dob', 'sin', 'phone', 'email',
        'address', 'city', 'province', 'postal_code',
        'emergency_contact_name', 'emergency_contact_phone',
    ];

    private const DOCUMENT_FIELDS = [
        'bankDetail.void_cheque_file',
        'licenseDetail.security_license_file',
        'licenseDetail.drivers_license_file',
        'licenseDetail.work_eligibility_file',
        'licenseDetail.other_documents_file',
    ];

    public function calculate(User $user): array
    {
        $user->loadMissing([
            'candidate', 'bankDetail', 'licenseDetail',
            'orientationAttempts', 'signedPolicies', 'taxDocumentSubmissions',
        ]);

        $personalCompleted = collect(self::PERSONAL_FIELDS)
            ->filter(fn (string $field) => $this->filled(data_get($user->candidate, $field)))
            ->count();

        $documentsCompleted = collect(self::DOCUMENT_FIELDS)
            ->filter(fn (string $field) => $this->filled(data_get($user, $field)))
            ->count();

        $orientationIds = Orientation::where('status', true)->pluck('id');
        $passedOrientations = $user->orientationAttempts
            ->where('is_passed', true)
            ->whereIn('orientation_id', $orientationIds)
            ->pluck('orientation_id')->unique()->count();

        $policyIds = Policy::where('status', true)->pluck('id');
        $signedPolicies = $user->signedPolicies
            ->where('agreed', 'yes')
            ->whereIn('policy_id', $policyIds)
            ->pluck('policy_id')->unique()->count();

        $taxDocumentIds = TaxDocument::pluck('id');
        $taxDocumentsCompleted = $user->taxDocumentSubmissions
            ->whereIn('tax_document_id', $taxDocumentIds)
            ->pluck('tax_document_id')->unique()->count();

        $sections = [
            'personal_info' => $this->section($personalCompleted, count(self::PERSONAL_FIELDS)),
            'orientation_exams' => $this->section($passedOrientations, $orientationIds->count()),
            'documents' => $this->section($documentsCompleted, count(self::DOCUMENT_FIELDS)),
            'policies' => $this->section($signedPolicies, $policyIds->count()),
            'tax_documents' => $this->section($taxDocumentsCompleted, $taxDocumentIds->count()),
        ];

        return [
            'percentage' => (int) round(collect($sections)->avg('percentage')),
            'sections' => $sections,
        ];
    }

    private function section(int $completed, int $total): array
    {
        return [
            'completed' => $completed,
            'total' => $total,
            'percentage' => $total === 0 ? 100 : (int) round(($completed / $total) * 100),
        ];
    }

    private function filled(mixed $value): bool
    {
        if (is_array($value)) {
            return count(array_filter($value, fn ($item) => $item !== null && $item !== '')) > 0;
        }

        return $value !== null && trim((string) $value) !== '';
    }
}
