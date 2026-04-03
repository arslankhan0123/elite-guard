<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Employee;
use App\Models\NfcTag;
use App\Models\Site;
use Carbon\Carbon;

class ReportRepository
{
    /**
     * Date range calculate karo based on filter
     */
    public function getDateRange($date_filter)
    {
        $now = Carbon::now();

        switch ($date_filter) {
            case 'today':
                return [$now->startOfDay()->copy(), $now->copy()->endOfDay()];
            case 'yesterday':
                return [$now->subDay()->startOfDay()->copy(), $now->copy()->endOfDay()];
            case 'current_week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'previous_week':
                return [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()];
            case 'current_month':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            case 'previous_month':
                return [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];
            default:
                return [null, null]; // No date filter
        }
    }

    /**
     * Companies report
     */
    public function getCompaniesReport($date_filter = null)
    {
        $query = Company::orderBy('id', 'desc');

        if ($date_filter) {
            [$from, $to] = $this->getDateRange($date_filter);
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }

        return $query->get();
    }

    /**
     * Sites report
     */
    public function getSitesReport($date_filter = null)
    {
        $query = Site::with('company')->orderBy('id', 'desc');

        if ($date_filter) {
            [$from, $to] = $this->getDateRange($date_filter);
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }

        return $query->get();
    }

    /**
     * NFC Tags report
     */
    public function getNfcTagsReport($date_filter = null)
    {
        $query = NfcTag::with('site.company')->orderBy('id', 'desc');

        if ($date_filter) {
            [$from, $to] = $this->getDateRange($date_filter);
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }

        return $query->get();
    }

    /**
     * Employees report
     */
    public function getEmployeesReport($date_filter = null)
    {
        $query = Employee::with('user')->orderBy('id', 'desc');

        if ($date_filter) {
            [$from, $to] = $this->getDateRange($date_filter);
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }

        return $query->get();
    }

    /**
     * Main report fetch - type aur date filter se
     */
    public function getReport($type, $date_filter)
    {
        switch ($type) {
            case 'companies':
                return $this->getCompaniesReport($date_filter);
            case 'sites':
                return $this->getSitesReport($date_filter);
            case 'nfc_tags':
                return $this->getNfcTagsReport($date_filter);
            case 'employees':
                return $this->getEmployeesReport($date_filter);
            default:
                return collect();
        }
    }
}
