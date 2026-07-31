<?php

namespace Tests\Feature;

use App\Models\EmployeeBankDetail;
use App\Models\EmployeeCandidate;
use App\Models\EmployeeLicenseDetail;
use App\Models\Orientation;
use App\Models\OrientationAttempt;
use App\Models\Policy;
use App\Models\SignedPolicy;
use App\Models\TaxDocument;
use App\Models\TaxDocumentSubmission;
use App\Models\User;
use App\Services\ProfileCompletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_all_five_profile_completion_sections(): void
    {
        Orientation::query()->delete();
        Policy::query()->delete();
        TaxDocument::query()->delete();
        $user = User::factory()->create();

        EmployeeCandidate::create([
            'user_id' => $user->id,
            'first_name' => 'Jane', 'last_name' => 'Guard', 'dob' => '1990-01-01',
            'sin' => '123456789', 'phone' => '5551234567', 'email' => 'jane@example.com',
            'address' => '1 Main St', 'city' => 'Calgary', 'province' => 'AB',
            'postal_code' => 'T1T1T1', 'emergency_contact_name' => 'John',
            'emergency_contact_phone' => '5559876543',
        ]);
        EmployeeBankDetail::create(['user_id' => $user->id, 'void_cheque_file' => ['cheque.pdf']]);
        EmployeeLicenseDetail::create([
            'user_id' => $user->id,
            'security_license_file' => ['security.pdf'],
            'drivers_license_file' => ['driver.pdf'],
            'work_eligibility_file' => ['eligibility.pdf'],
            'other_documents_file' => ['other.pdf'],
        ]);

        $orientation = Orientation::create(['type' => 'General', 'status' => true]);
        OrientationAttempt::create([
            'user_id' => $user->id, 'orientation_id' => $orientation->id,
            'score' => 100, 'is_passed' => true,
        ]);
        $policy = Policy::create(['type' => 'Conduct', 'status' => true]);
        SignedPolicy::create([
            'user_id' => $user->id, 'policy_id' => $policy->id, 'agreed' => 'yes',
        ]);
        $taxDocument = TaxDocument::create(['type' => 'Td1-Fill', 'file_path' => 'td1.pdf']);
        TaxDocumentSubmission::create([
            'user_id' => $user->id, 'tax_document_id' => $taxDocument->id,
            'document_path' => 'completed-td1.pdf',
        ]);

        $completion = app(ProfileCompletionService::class)->calculate($user);

        $this->assertSame(100, $completion['percentage']);
        $this->assertSame(['completed' => 1, 'total' => 1, 'percentage' => 100], $completion['sections']['orientation_exams']);
        $this->assertSame(['completed' => 1, 'total' => 1, 'percentage' => 100], $completion['sections']['policies']);
        $this->assertSame(['completed' => 1, 'total' => 1, 'percentage' => 100], $completion['sections']['tax_documents']);
    }

    public function test_profile_page_shows_profile_completion(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk()->assertSee('Profile complete')->assertSee('60%');
    }
}
