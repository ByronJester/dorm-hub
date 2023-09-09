<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function uploadFile($file)
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => "dcmgsini6",
                'api_key' => "522938554472129",
                'api_secret' => "FrOIDIXbv7JfcVc9LpjZB1RMJQA"
            ],
            'url' => [
              'secure' => true
            ]
        ]);

        $upload = (new UploadApi())->upload($file);

        return $upload["url"];
    }
}
