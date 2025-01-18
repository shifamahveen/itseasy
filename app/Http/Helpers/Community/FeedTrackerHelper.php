<?php

namespace App\Http\Helpers\Community;
//load model
use App\Models\Community\FeedTracker;

class FeedTrackerHelper
{
    /**
     * get the tacker category stats
     */
    public function getTrackerCategoryStats($id)
    {
        //load global settings
        $viewers = FeedTracker::select('feed_id', 'category')->where('feed_id', $id)->get()->groupBy('category');
        $categories = ["All" => 0];
        foreach ($viewers as $category => $items) {
            if ($category == "")
                $category = "Other";
            $categories[$category] = count($items);
            $categories["All"] += $categories[$category];
        }
        return $categories;
    }
}
