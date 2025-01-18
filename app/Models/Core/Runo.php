<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Runo extends Model
{
    use HasFactory;

    /**
     * Get the list of records from database
     *
     * @var array
     */
    public function getRecords($request)
    {

        $start = $request->get('start');
        $end = $request->get('end');
        $filter = $request->get('filter');

        if ($start && $end) {
        } else if ($filter == 'thismonth') {
            $start = Carbon::now()->startOfMonth();
            $end  = Carbon::now();
        } else if ($filter == 'lastmonth') {
            $start = Carbon::now()->subMonth(1)->startOfMonth()->startOfDay();
            $end  = Carbon::now()->subMonth(1)->endOfMonth()->endOfDay();
        } else if ($filter == 'thisyear') {
            $start = Carbon::now()->startOfYear()->startOfMonth()->startOfDay();
            $end  = Carbon::now();
        } else if ($filter == 'lastyear') {
            $start = Carbon::now()->subYear(1)->startOfYear()->startOfMonth()->startOfDay();
            $end  = Carbon::now()->subYear(1)->endOfYear()->endOfMonth()->endOfDay();
        } else if ($filter == 'last7days') {
            $start = Carbon::now()->subDays(7)->startOfDay();
            $end  = Carbon::now()->subDays(1)->endOfDay();
        } else if ($filter == 'last30days') {
            $start = Carbon::now()->subDays(30)->startOfDay();
            $end  = Carbon::now()->subDays(1)->endOfDay();
        } else if ($filter == 'today') {
            $start = Carbon::now()->startOfDay();
            $end  = Carbon::now();
        } else if ($filter == 'yesterday') {
            $start = Carbon::now()->subDay()->startOfDay();
            $end  = Carbon::now()->subDay()->endOfDay();
        } else {
            $start = Carbon::now()->startOfDay();
            $end  = Carbon::now()->endOfDay();
        }
        $data = $this->where('created_at', '>=', $start)->where('created_at', '<=', $end)->orderBy('id', 'asc')->get();
        request()->merge(['start' => $start, 'end' => $end]);
        return $data;
    }

    public function analyse($data)
    {
        $grouped = $data->groupBy('caller_phone');
        $raw = [];
        $scores = [];

        foreach ($grouped as $k => $d) {
            $raw[$k]['name'] = $d->first()->caller_name;
            $raw[$k]['phone'] = $d->first()->caller_phone;
            $raw[$k]['total_talktime'] = round($d->sum('duration') / 60, 1);

            $intr = $d->groupBy('interaction_type');
            if (isset($intr['call'])) {
                $raw[$k]['total_calls'] = count($intr['call']);
                $raw[$k]['connected_calls'] = $intr['call']->where('duration', '!=', 0)->count();
                $raw[$k]['connected_customers'] = $intr['call']->where('duration', '!=', 0);
            } else {
                $raw[$k]['total_calls'] = 0;
                $raw[$k]['connected_calls'] = 0;
                $raw[$k]['connected_customers'] = 0;
            }

            //update the status
            if ($raw[$k]['connected_customers'])
                foreach ($raw[$k]['connected_customers']  as $a => $b) {
                    if (isset($intr['status'])) {
                        $entry = $intr['status']->where('phone', $b['phone'])->first();
                        if ($entry) {
                            if ($entry['status'])
                                $raw[$k]['connected_customers'][$a]['status']  = $entry['status'];
                        }
                    }
                }

            if ($raw[$k]['connected_calls'])
                $raw[$k]['avg_talktime'] = round($d->sum('duration') / $raw[$k]['connected_calls'] / 60, 2);
            else
                $raw[$k]['avg_talktime'] = 0;
            if (isset($intr['status']))
                $raw[$k]['total_interactions'] = count($intr['status']);
            else
                $raw[$k]['total_interactions'] = 0;
            $raw[$k]['unique_customers'] = $d->groupBy('phone')->count();
            $status = $d->groupBy('status');
            $raw[$k]['score'] = intval($d->sum('duration') / 10) +  $raw[$k]['total_interactions'];
            $scores[$k] = $raw[$k]['score'];
            foreach ($status as $s)
                if ($s[0]['status'] != '')
                    $raw[$k]['status'][$s[0]['status']] = count($s);
        }
        arsort($scores);
        foreach ($scores as $k => $v) {
            $scores[$k] = $raw[$k];
        }


        return $scores;
    }
}
