<?php

namespace App\Helpers;

class ManpowerViewHelper
{
    /**
     * Calculate company total for received manpower
     *
     * @param int $companyId
     * @param array $receivedOfficerTotals
     * @param array $otherRanks
     * @param array $receivedManpower
     * @return int
     */
    public static function calculateReceivedCompanyTotal($companyId, $receivedOfficerTotals, $otherRanks, $receivedManpower)
    {
        $total = $receivedOfficerTotals[$companyId] ?? 0;

        foreach ($otherRanks as $rank) {
            $rankId = $rank->id;
            $companyData = $receivedManpower[$companyId] ?? null;

            if ($companyData && isset($companyData[$rankId])) {
                $total += $companyData[$rankId]->count ?? 0;
            }
        }

        return $total;
    }

    /**
     * Calculate rank total for received manpower
     *
     * @param int $rankId
     * @param array $companies
     * @param array $receivedManpower
     * @return int
     */
    public static function calculateReceivedRankTotal($rankId, $companies, $receivedManpower)
    {
        $total = 0;

        foreach ($companies as $company) {
            $companyId = $company->id;
            $companyData = $receivedManpower[$companyId] ?? null;

            if ($companyData && isset($companyData[$rankId])) {
                $total += $companyData[$rankId]->count ?? 0;
            }
        }

        return $total;
    }

    /**
     * Calculate grand total for received manpower
     *
     * @param array $receivedOfficerTotals
     * @param array $otherRanks
     * @param array $companies
     * @param array $receivedManpower
     * @return int
     */
    public static function calculateReceivedGrandTotal($receivedOfficerTotals, $otherRanks, $companies, $receivedManpower)
    {
        $total = array_sum($receivedOfficerTotals);

        foreach ($otherRanks as $rank) {
            $rankId = $rank->id;

            foreach ($companies as $company) {
                $companyId = $company->id;
                $companyData = $receivedManpower[$companyId] ?? null;

                if ($companyData && isset($companyData[$rankId])) {
                    $total += $companyData[$rankId]->count ?? 0;
                }
            }
        }

        return $total;
    }

    /**
     * Calculate company total for leave manpower
     *
     * @param int $companyId
     * @param array $leaveOfficerTotals
     * @param array $otherRanks
     * @param array $leaveManpower
     * @return int
     */
    public static function calculateLeaveCompanyTotal($companyId, $leaveOfficerTotals, $otherRanks, $leaveManpower)
    {
        $total = $leaveOfficerTotals[$companyId] ?? 0;

        foreach ($otherRanks as $rank) {
            $rankId = $rank->id;
            $companyData = $leaveManpower[$companyId] ?? null;

            if ($companyData && isset($companyData[$rankId])) {
                $total += $companyData[$rankId]->count ?? 0;
            }
        }

        return $total;
    }

    /**
     * Calculate rank total for leave manpower
     *
     * @param int $rankId
     * @param array $companies
     * @param array $leaveManpower
     * @return int
     */
    public static function calculateLeaveRankTotal($rankId, $companies, $leaveManpower)
    {
        $total = 0;

        foreach ($companies as $company) {
            $companyId = $company->id;
            $companyData = $leaveManpower[$companyId] ?? null;

            if ($companyData && isset($companyData[$rankId])) {
                $total += $companyData[$rankId]->count ?? 0;
            }
        }

        return $total;
    }

    /**
     * Calculate grand total for leave manpower
     *
     * @param array $leaveOfficerTotals
     * @param array $otherRanks
     * @param array $companies
     * @param array $leaveManpower
     * @return int
     */
    public static function calculateLeaveGrandTotal($leaveOfficerTotals, $otherRanks, $companies, $leaveManpower)
    {
        $total = array_sum($leaveOfficerTotals);

        foreach ($otherRanks as $rank) {
            $rankId = $rank->id;

            foreach ($companies as $company) {
                $companyId = $company->id;
                $companyData = $leaveManpower[$companyId] ?? null;

                if ($companyData && isset($companyData[$rankId])) {
                    $total += $companyData[$rankId]->count ?? 0;
                }
            }
        }

        return $total;
    }

    /**
     * Calculate company total for without leave manpower
     *
     * @param int $companyId
     * @param array $withoutLeaveOfficerTotals
     * @param array $otherRanks
     * @param array $withoutLeaveManpower
     * @return int
     */
    public static function calculateWithoutLeaveCompanyTotal($companyId, $withoutLeaveOfficerTotals, $otherRanks, $withoutLeaveManpower)
    {
        $total = $withoutLeaveOfficerTotals[$companyId] ?? 0;

        foreach ($otherRanks as $rank) {
            $rankId = $rank->id;
            $companyData = $withoutLeaveManpower[$companyId] ?? null;

            if ($companyData && isset($companyData[$rankId])) {
                $total += $companyData[$rankId]->count ?? 0;
            }
        }

        return $total;
    }

    /**
     * Calculate rank total for without leave manpower
     *
     * @param int $rankId
     * @param array $companies
     * @param array $withoutLeaveManpower
     * @return int
     */
    public static function calculateWithoutLeaveRankTotal($rankId, $companies, $withoutLeaveManpower)
    {
        $total = 0;

        foreach ($companies as $company) {
            $companyId = $company->id;
            $companyData = $withoutLeaveManpower[$companyId] ?? null;

            if ($companyData && isset($companyData[$rankId])) {
                $total += $companyData[$rankId]->count ?? 0;
            }
        }

        return $total;
    }

    /**
     * Calculate grand total for without leave manpower
     *
     * @param array $withoutLeaveOfficerTotals
     * @param array $otherRanks
     * @param array $companies
     * @param array $withoutLeaveManpower
     * @return int
     */
    public static function calculateWithoutLeaveGrandTotal($withoutLeaveOfficerTotals, $otherRanks, $companies, $withoutLeaveManpower)
    {
        $total = array_sum($withoutLeaveOfficerTotals);

        foreach ($otherRanks as $rank) {
            $rankId = $rank->id;

            foreach ($companies as $company) {
                $companyId = $company->id;
                $companyData = $withoutLeaveManpower[$companyId] ?? null;

                if ($companyData && isset($companyData[$rankId])) {
                    $total += $companyData[$rankId]->count ?? 0;
                }
            }
        }

        return $total;
    }
}
