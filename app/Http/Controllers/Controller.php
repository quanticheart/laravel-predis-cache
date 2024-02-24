<?php

namespace App\Http\Controllers;

use App\Cache\InternalCache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param InternalCache $cache
     */
    public function __construct(public InternalCache $cache)
    {
    }


    function data()
    {
        $data = $this->cache->get("test");
        $redis = false;
        if (isset($data)) {
            $this->cache->delete("test");
            $redis = true;
        } else {
            // Generated @ codebeautify.org
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://digimoncard.io/api-public/getAllCards.php?sort=name&series=Digimon%20Card%20Game&sortdirection=asc');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $data = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            $this->cache->set("test", $data, 10);
        }

        return response()->json(["redis" => $redis, "data" => $data]);
    }
}
