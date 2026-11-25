<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    private function patients(): Builder
    {
        $query = Patient::query();

        if (Schema::hasColumn('patients', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return $query;
    }

    private function visits(): Builder
    {
        return Visit::query()->whereIn(
            'patient_id',
            $this->patients()->select('patient_id')
        );
    }

    public function clinical(Request $request): View|RedirectResponse
    {
        if ($request->query('workspace') === 'research') {
            return redirect()->route('dashboard.research');
        }

        $today = today()->toDateString();

        $visits = $this->visits();

        $scheduled = (clone $visits)
            ->whereNull('visited_at')
            ->whereNotNull('due_date');

        $stats = [
            'Registered Patients' => $this->patients()->count(),

            'Upcoming Visits' => (clone $scheduled)
                ->whereDate('due_date', '>=', $today)
                ->count(),

            'Overdue Visits' => (clone $scheduled)
                ->whereDate('due_date', '<', $today)
                ->count(),

            'Completed Visits' => (clone $visits)
                ->whereNotNull('visited_at')
                ->count(),
        ];

        $attentionVisits = (clone $scheduled)
            ->with('patient')
            ->orderBy('due_date')
            ->orderBy('visit_id')
            ->limit(10)
            ->get();

        $recentPatients = $this->patients()
            ->orderByDesc('patient_id')
            ->limit(5)
            ->get();

        $alertsAvailable = Schema::hasTable('clinical_alerts');
        $openAlertCount = null;
        $alerts = collect();

        if ($alertsAvailable) {
            $alertQuery = DB::table('clinical_alerts')
                ->whereIn(
                    'visit_id',
                    $this->visits()->select('visit_id')
                )
                ->where('status', 'open');

            $openAlertCount = (clone $alertQuery)->count();

            $alerts = (clone $alertQuery)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return view('dashboards.clinical', compact(
            'stats',
            'attentionVisits',
            'recentPatients',
            'alertsAvailable',
            'openAlertCount',
            'alerts'
        ));
    }

    public function research(): View
    {
        $patientCount = $this->patients()->count();

        $patientIds = $this->patients()
            ->select('patient_id');

        $eligibility = null;
        $unreviewedCount = null;

        // Count only the latest eligibility review for each patient.
        if (Schema::hasColumns('eligibility_reviews', [
            'id',
            'patient_id',
            'status',
        ])) {
            $latestReviewIds = DB::table('eligibility_reviews')
                ->whereNotNull('patient_id')
                ->selectRaw('MAX(id)')
                ->groupBy('patient_id');

            $counts = DB::table('eligibility_reviews')
                ->whereIn('patient_id', clone $patientIds)
                ->whereIn('id', $latestReviewIds)
                ->select('status')
                ->selectRaw('COUNT(*) AS total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $eligibility = [
                'Eligible' => (int) ($counts['eligible'] ?? 0),
                'Pending Review' => (int) ($counts['pending'] ?? 0),
                'Not Eligible' => (int) ($counts['not_eligible'] ?? 0),
            ];

            $reviewedPatientCount = (int) $counts->sum();

            $unreviewedCount = $patientCount - $reviewedPatientCount;
        }

        // Count patients whose latest consent is signed
        // and has not been withdrawn.
        $consentedCount = $this->patients()
            ->whereHas('latestConsent', function (Builder $query) {
                $query->whereNotNull('signed_at')
                    ->whereNull('withdrawn_at');
            })
            ->count();

        $timePoints = [
            'baseline' => 'Baseline',
            'month_3' => '3 Months',
            'month_6' => '6 Months',
            'month_12' => '12 Months',
        ];

        $visitProgress = [];

        $today = today()->toDateString();

        foreach ($timePoints as $key => $label) {
            $visits = $this->visits()
                ->where('time_point', $key);

            $completed = (clone $visits)
                ->whereNotNull('visited_at')
                ->count();

            $visitProgress[] = [
                'label' => $label,

                'records' => (clone $visits)->count(),

                'completed' => $completed,

                'overdue' => (clone $visits)
                    ->whereNull('visited_at')
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today)
                    ->count(),

                'percentage' => $patientCount > 0
                    ? round(($completed / $patientCount) * 100, 1)
                    : 0,
            ];
        }

        $completedPatients = $this->visits()
            ->whereIn('time_point', array_keys($timePoints))
            ->whereNotNull('visited_at')
            ->select('patient_id')
            ->groupBy('patient_id')
            ->havingRaw('COUNT(DISTINCT time_point) = 4');

        $allVisitsCompleted = DB::query()
            ->fromSub($completedPatients, 'completed_patients')
            ->count();

        $seriousEventCount = null;

        if (Schema::hasColumns('adverse_events', [
            'visit_id',
            'is_serious',
        ])) {
            $seriousEventCount = DB::table('adverse_events')
                ->whereIn(
                    'visit_id',
                    $this->visits()->select('visit_id')
                )
                ->where('is_serious', true)
                ->count();
        }

        return view('dashboards.research', compact(
            'patientCount',
            'eligibility',
            'unreviewedCount',
            'consentedCount',
            'visitProgress',
            'allVisitsCompleted',
            'seriousEventCount'
        ));
    }

    public function exportVisits(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                throw new \RuntimeException(
                    'Unable to open the CSV output stream.'
                );
            }

            fputcsv($output, [
                'study_code',
                'time_point',
                'due_date',
                'completed_at',
            ], ',', '"', '');

            $visits = $this->visits()
                ->with('patient')
                ->lazyById(500, 'visit_id');

            foreach ($visits as $visit) {
                if (! $visit->patient) {
                    continue;
                }

                $code = (string) $visit->patient->study_code;

                // Prevent spreadsheet formulas in exported study codes.
                if (preg_match('/^[\s\x{FEFF}]*[=+\-@]/u', $code)) {
                    $code = "'" . $code;
                }

                fputcsv($output, [
                    $code,
                    $visit->time_point,
                    $visit->due_date?->format('Y-m-d'),
                    $visit->visited_at?->format('Y-m-d H:i:s'),
                ], ',', '"', '');
            }

            fclose($output);
        }, 'research-visit-summary.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }
}