<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Client;
use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Total counts for the 4 cards
        $leadsCount      = Lead::count();
        $clientsCount    = Client::count();
        $projectsCount   = Project::count();
        $quotationsCount = Quotation::count();

        $activeProjectsCount   = Project::where('status', 'active')->count();
        $pendingQuotationsCount = Quotation::where('status', 'pending')->count();

        $leadsGrowth   = $this->growthPercent(Lead::class);
        $clientsGrowth = $this->growthPercent(Client::class);

        return view('admin.dashboard', compact(
            'leadsCount',
            'clientsCount',
            'projectsCount',
            'quotationsCount',
            'activeProjectsCount',
            'pendingQuotationsCount',
            'leadsGrowth',
            'clientsGrowth'
        ));
    }

    
    private function growthPercent(string $model): float
    {
        $thisMonth = $model::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

        $lastMonth = $model::whereMonth('created_at', now()->subMonth()->month)
        ->whereYear('created_at', now()->subMonth()->year)
        ->count();

        if ($lastMonth == 0) {
            return $thisMonth > 0 ? 100 : 0;
        }

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }
}