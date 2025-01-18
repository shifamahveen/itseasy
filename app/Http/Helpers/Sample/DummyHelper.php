<?php

namespace App\Http\Helpers\Sample;

use Illuminate\Support\Facades\Storage;

class DummyHelper
{

    public function getAppName($settingsPath)
    {
        $name = null;
        $settings = json_decode(Storage::get($settingsPath));
        if ($settings)
            $name = $settings->name;
        return $name;
    }
}
