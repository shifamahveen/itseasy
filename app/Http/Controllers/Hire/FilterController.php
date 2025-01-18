<?php

namespace App\Http\Controllers\Hire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Hire\Job as Model;

class FilterController extends Controller
{

    public function index()
    {
        return view('apps.hire.job.snippet.filter');
    }

    public function filterByYear(Request $request, $year)
    {
        // Define a cache key for this specific year's results
        $cacheKey = "filtered_data_year_{$year}";

        // Check if the data is already cached
        if (Cache::has($cacheKey)) {
            $filteredData = Cache::get($cacheKey);
        } else {
            // If not cached, retrieve and cache the data
            $filteredData = Model::where('date_column', $year)->get();

            // Cache the filtered data for a specified duration (e.g., 1 hour)
            Cache::put($cacheKey, $filteredData, now()->addHour());
        }

        return view('filtered_data', compact('filteredData'));
    }
}
