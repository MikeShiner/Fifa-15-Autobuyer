<?php
require_once "../vendor/autoload.php";


use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Subscriber\Cookie as CookieSubscriber;
use Fut\Connector;
use Fut\Request\Forge;

/**
 * the connector will not export your cookie jar anymore
 * keep a reference on this object somewhere to inject it on reconnecting
 */
$client = new Client();
$cookieJar = new CookieJar();
$cookieSubscriber = new CookieSubscriber($cookieJar);
$client->getEmitter()->attach($cookieSubscriber);

$backupcode = 0;
$backupcode = $_POST['backupcode'];

try {

    $connector = new Connector(
        $client,
        'mike.shiner@hotmail.com',
        ' ',
        'milo',
        $backupcode,
        Forge::PLATFORM_PLAYSTATION,
        Forge::ENDPOINT_WEBAPP
    );

    $export = $connector
        ->connect($backupcode)
        ->export();

} catch(Exception $e) {
    die('login failed.');
}
$jsondata = json_encode($export);
echo $jsondata;

// example for playstation accounts to get the credits
// 3. parameter of the forge factory is the actual real http method
// 3. parameter of the forge factory is the actual real http method
// 4. parameter is the overridden method for the webapp headers


//
//echo "search for Ronaldo";
//$locate = false;
//$try = 0;
//    while (!$locate && $try < 28) {
//        $assetId = "190852";
//
//        $forge = Fut\Request\Forge::getForge($client, '/ut/game/fifa15/transfermarket', 'post', 'get');
//        $json = $forge
//            ->setNucId($export['nucleusId'])
//            ->setSid($export['sessionId'])
//            ->setPhishing($export['phishingToken'])
//            ->setBody(array(
//                'maskedDefId' => $assetId,
//                'start' => $try,
//                'num' => 20
//            ))->getJson();
//
//        echo "search for ronaldo...";
//        echo "Ronaldo's found : (" . count($json['auctionInfo']) . ")" . PHP_EOL . PHP_EOL;
//        echo "@@@@@@@@@@@@@@@@@@@@@@@@@@@' TRY: " . $try . " @@@@@@@@@@@@@@@@@@@@@@@@@@@@@";
//        foreach ($json['auctionInfo'] as $auction) {
//            if ($auction['buyNowPrice'] <= 350 && $auction['buyNowPrice'] > 0) {
//                echo "----------------------";
//                echo "auction: " . PHP_EOL;
//                echo " - tradeId: " . $auction['tradeId'] . " #### ...";
//                echo " - current bid: " . $auction['currentBid'] . PHP_EOL;
//                echo " - buy now price: " . $auction['buyNowPrice'] . PHP_EOL;
//                echo " - rating: " . $auction['itemData']['rating'] . PHP_EOL;
//                echo " - expires: ~" . round($auction['expires'] / 60, 0) . " minutes" . PHP_EOL . PHP_EOL;
//                echo "----------------------";
//                $try = $try + 1;
//                $locate = true;
//            } else {
//                echo "-- Buy now: " . $auction['buyNowPrice'];
//                echo "...";
//                echo "tradeId: " . $auction['tradeId'] . " --";
//
//            }
//        }
//        $try += 1;
//
//    }
