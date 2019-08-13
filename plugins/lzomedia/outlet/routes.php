<?php

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Promise as GuzzlePromise;
use Lovata\Shopaholic\Classes\Import\ImportOfferModelFromCSV;

Route::get('onGetOutlet', function () {


    $client = new GuzzleClient(['timeout' => 12.0]); // see how i set a timeout


    $requestPromises = [];

    $urls = [];
    for ($i = 0; $i <= 3; $i++) {
        $urls[$i] = 'https://www.emag.ro/search-by-url?templates[0]=full&is_eab49=false&url=/search/geci-dama/p' . $i;
    }

    foreach ($urls as $key => $url) {


        logger($url);
        $requestPromises[$key] = $client->getAsync($url);
    }

    $results = GuzzlePromise\settle($requestPromises)->wait();

    $responses = collect([]);

    foreach ($results as $key => $result) {



        if ($result['state'] === 'fulfilled') {

            $response = $result['value'];

            if ($response->getStatusCode() == 200) {

                $json = json_decode($response->getBody()->getContents());

                if(!is_null($json)){


                    foreach ($json->data->items as $item){

                        $productImported = new \LzoMedia\Outlet\Services\ProductsImporter($item);

                        $pricesImport = new \LzoMedia\Outlet\Services\PricesImporter();

                        $response = $productImported->process();

                        $pricesImport->setJson($item);

                        $pricesImport->setProduct($response->obModel);

                        $pricesImport->process();



                    }

                    $responses->push($pricesImport);

                }




            } else {

                logger('The status was not 200');

            }


        } else if ($result['state'] === 'rejected') {

            logger('Rejected');

        } else {

            logger('Unknown');

        }


    }



    dd($responses);



});
