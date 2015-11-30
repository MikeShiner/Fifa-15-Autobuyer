<?php
require_once "../vendor/autoload.php";


use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Subscriber\Cookie as CookieSubscriber;
use Fut\Connector;
use Fut\Request\Forge;

$client = new Client();
$cookieJar = new CookieJar();
$cookieSubscriber = new CookieSubscriber($cookieJar);
$client->getEmitter()->attach($cookieSubscriber);

if (isset($_REQUEST["nucleusId"]) && isset($_REQUEST["sessionId"]) && isset($_REQUEST["phishingToken"])) {
    $export['nucleusId'] = $_REQUEST["nucleusId"];
    $export['sessionId'] = $_REQUEST["sessionId"];
    $export['phishingToken'] = $_REQUEST["phishingToken"];
    if ($_REQUEST['funId'] == "getCoins") {
        getCoins($client, $export);
        $_REQUEST = [];
    } else if ($_REQUEST['funId'] == "playerSearch") {
        playerSearch($client, $export, $_REQUEST['playerId'], $_REQUEST['maxPrice']);
        $_REQUEST = [];
    } else if ($_REQUEST['funId'] == "getTradePile") {
        $returnPile = getTradePile($client, $export);
        var_dump($returnPile);
    } else if ($_REQUEST['funId'] == "watchlist") {
        var_dump(getWatchlist($client, $export));
//        echo getWatchlist($client, $export);
    } else if ($_REQUEST['funId'] == "auction") {
        echo(getCardImg($id = "168542"));
    } else if ($_REQUEST['funId'] == "marketlist") {
       testPlayerSearch($client, $export, $_REQUEST['playerID'], $_REQUEST['maxPrice']);
//        echo json_encode($res);
    } else if ($_REQUEST['funId'] == "autolist") {
        autoList($client, $export, $_REQUEST['addBin'], $_REQUEST['addStart']);
    }
} else {
    echo "No funID/Login ID received.";
}

function testPlayerSearch($client, $export, $playerId = 168542, $maxPrice)
{
// Search for Buy Now:
//    $itemdata = [];
//    $forge = Fut\Request\Forge::getForge($client, '/ut/game/fifa15/transfermarket', 'post', 'get');
//    $json = $forge
//        ->setNucId($export['nucleusId'])
//        ->setSid($export['sessionId'])
//        ->setPhishing($export['phishingToken'])
//        ->setBody(array(
//            'maskedDefId' => $playerId,
//            'start' => 0,
//            'num' => 50,
//            'maxb' => 4400
//        ))->getJson();
// If Max <- 4400 then Buy.
    // Macr/micr/maxb/minb

//    foreach ($json['auctionInfo'] as $auction) {
//        buynow($client, $export, $auction['tradeId'], $auction['buyNowPrice']);
//    }

    $itemdata = [];
    $forge = Fut\Request\Forge::getForge($client, '/ut/game/fifa15/transfermarket', 'post', 'get');
    $json = $forge
        ->setNucId($export['nucleusId'])
        ->setSid($export['sessionId'])
        ->setPhishing($export['phishingToken'])
        ->setBody(array(
            'maskedDefId' => $playerId,
            'start' => 0,
            'num' => 50,
            'macr' => 4500
        ))->getJson();

    if (isset($json['auctionInfo'])) {
        foreach ($json['auctionInfo'] as $auction) {
            if ($auction['currentBid'] <= "4500" && $auction['currentBid'] > "0") {
                placeBid($client, $export, $auction['tradeId'], $auction['currentBid'] + 100);
            } else if ($auction['currentBid'] < "1" && $auction['startingBid'] < "4500") {
                placeBid($client, $export, $auction['tradeId'], $auction['startingBid'] + 100);
            }
//        $itemdata[] = array(
//            'tradeId' => $auction['tradeId'],
//            'curBid' => $auction['currentBid'],
//            'bin' => $auction['buyNowPrice'],
//            'start' => $auction['startingBid'],
//            'rate' => $auction['itemData']['rating'],
//            'expire' => round($auction['expires'] / 60, 0)
//        );
        }
    } else {

    }


//    return $itemdata;
}

function getCoins($client, $export)
{

    $forge = Fut\Request\Forge::getForge($client, '/ut/game/fifa15/user/credits', 'post', 'get');
    $json = $forge
        ->setNucId($export['nucleusId'])
        ->setSid($export['sessionId'])
        ->setPhishing($export['phishingToken'])
        ->getJson();

    echo $json['credits'];
}

function getTradePile($client, $export)
{

    $forge = Fut\Request\Forge::getForge($client, '/ut/game/fifa15/tradepile', 'post', 'get');
    $json = $forge
        ->setNucId($export['nucleusId'])
        ->setSid($export['sessionId'])
        ->setPhishing($export['phishingToken'])
        ->getJson();

    foreach ($json['auctionInfo'] as $auction) {

        $itemdata[] = array(
            'id' => $auction['itemData']['id'],
            'rating' => $auction['itemData']['rating'],
            'lastSale' => $auction['itemData']['lastSalePrice'],
            'itemState' => $auction['itemData']['itemState'],
            'tradeState' => $auction['tradeState'],
            'expire' => round($auction['expires'] / 60, 0),
            'buyNowPrice' => $auction['buyNowPrice'],
            'currentBid' => $auction['currentBid'],
            'startingBid' => $auction['startingBid']
        );
    }
    if (empty($itemdata)) {
        $itemdata = "Nothing in Trade Pile.";
    }
//    listAuction($client, $export, $itemdata[1]['id'], "2400", "2200");
    return $itemdata;
}


function getAuctioningItems($client, $export)
{
    $forge = Fut\Request\Forge::getForge($client, '/ut/game/fifa15/tradepile', 'post', 'get');
    $json = $forge
        ->setNucId($export['nucleusId'])
        ->setSid($export['sessionId'])
        ->setPhishing($export['phishingToken'])
        ->getJson();

    foreach ($json['auctionInfo'] as $auction) {
        if ($auction['itemData']['itemState'] == "forSale") {
            $itemdata[] = array(
                'id' => $auction['itemData']['id'],
                'rating' => $auction['itemData']['rating'],
                'lastSale' => $auction['itemData']['lastSalePrice'],
                'itemState' => $auction['itemData']['itemState'],
            );
        } else {
            $itemdata[] = "None on market";
        }
    }

}

function playerSearch($client, $export, $playerId, $maxPrice)
{

    // zone = player / defence..
    // micr = minimum price
    // macr = maximum price
    // minb = minimum buynow
    // maxb = maximum buynow
    // playStyle = Chemistry (Integer value - Basic: 250)

    $itemdata = [];
    $forge = Fut\Request\Forge::getForge($client, '/ut/game/fifa15/transfermarket', 'post', 'get');
    $json = $forge
        ->setNucId($export['nucleusId'])
        ->setSid($export['sessionId'])
        ->setPhishing($export['phishingToken'])
        ->setBody(array(
            'maskedDefId' => $playerId,
            'start' => 0,
            'num' => 50
        ))->getJson();
    foreach ($json['auctionInfo'] as $auction) {
        if (($auction['currentBid'] < $maxPrice && $auction['currentBid'] > "0" && (round($auction['expires'] / 60, 0)) < 4) OR
            ($auction['currentBid'] < "1" && $auction['startingBid'] < $maxPrice && (round($auction['expires'] / 60, 0)) < 4)
        ) {
            $itemdata[] = array(
                'tradeId' => $auction['tradeId'],
                'curBid' => $auction['currentBid'],
                'bin' => $auction['buyNowPrice'],
                'start' => $auction['startingBid'],
                'rate' => $auction['itemData']['rating'],
                'expire' => round($auction['expires'] / 60, 0)
            );
            placeBid($client, $export, $itemdata[0]['tradeId'], $maxPrice);


        }
    }
    echo "not found";
}

function placeBid($client, $export, $tradeId, $maxPrice)
{
    $buildstring = array(
        'bid' => $maxPrice
    );

    $forge = Fut\Request\Forge::getForge($client, "/ut/game/fifa15/trade/" . $tradeId . "/bid", 'post', 'put');
    $json = $forge
        ->setNucId($export['nucleusId'])
        ->setSid($export['sessionId'])
        ->setPhishing($export['phishingToken'])
        ->setBody($buildstring, true)
        ->getJson();

}

function listAuction($client, $export, $id, $bin, $startingBid)
{
// Can't auction  193501918531
    $forge = Fut\Request\Forge::getForge($client, '/ut/game/fifa15/auctionhouse', 'post', 'post');
    $json = $forge
        ->setNucId($export['nucleusId'])
        ->setSid($export['sessionId'])
        ->setPhishing($export['phishingToken'])
        ->setBody(array(
            'itemData' => array(
                'id' => $id),
            'buyNowPrice' => $bin,
            'startingBid' => $startingBid,
            'duration' => 3600), true)
        ->getJson();


}

function getWatchlist($client, $export)
{
    // Can do post 'delete' withh attached ID's to remove them.
    //POST, override: DELETE? ut/game/fifa15/watchlist?tradeId=153888152333%2C153888183022%2C153888234037%2C153888236460%2C153888239451%2C153888129757%2C153888208249%2C153888097004
    // Name : trdaeId
    // Value : 153888152333,153888183022,153888234037,153888236460,153888239451,153888129757,153888208249,153888097004

    $forge = Fut\Request\Forge::getForge($client, '/ut/game/fifa15/watchlist', 'post', 'get');
    $json = $forge
        ->setNucId($export['nucleusId'])
        ->setSid($export['sessionId'])
        ->setPhishing($export['phishingToken'])
        ->getJson();

    foreach ($json['auctionInfo'] as $auction) {
            $itemdata[] = array(
                'id' => $auction['itemData']['id'],
                'tradeId' => $auction ['tradeId'],
                'rating' => $auction['itemData']['rating'],
                'lastSale' => $auction['itemData']['lastSalePrice'],
                'itemState' => $auction['itemData']['itemState'],
            );

    }
    return $itemdata;
//    if (isset($itemdata)) {
//        $watchlistData = array();
//        for ($i = 0; $i < count($itemdata); $i++) {
//            sendToPile($client, $export, $itemdata[$i]['id']);
//            array_push($watchlistData, $itemdata[$i]['id']);
//        }
//        return $watchlistData;
//    } else {
//        return "Nothing in watchlist.";
//    }
}

function sendToPile($client, $export, $id)
{
    $forge = Fut\Request\Forge::getForge($client, '/ut/game/fifa15/item', 'post', 'put');
    $json = $forge
        ->setNucId($export['nucleusId'])
        ->setSid($export['sessionId'])
        ->setPhishing($export['phishingToken'])
        ->setBody(array(
            'itemData' => [array(
                'pile' => 'trade',
                'id' => $id
            )]), true)
        ->getJson();
    return $json;
}

function removeSold($client, $export, $tradeId)
{
    // POST: override DELTE? POST /ut/game/fifa15/trade/153880409152 HTTP/1.1  <-- $tradeId
    // NO WEBFORM

    $forge = Fut\Request\Forge::getForge($client, '/ut/game/fifa15/trade/'.$tradeId, 'post', 'delete');
    $json = $forge
        ->setNucId($export['nucleusId'])
        ->setSid($export['sessionId'])
        ->setPhishing($export['phishingToken'])
        ->getJson();
    return $json;

}


function autoList($client, $export, $addBin, $addStart)
{
    $bin = (int)$addBin;
    $start = (int)$addStart;
    $returnData = array('watchlist' => array(), 'sendPile' => array(), 'tradepile' => array(), 'forsale' => array());
    $watchlistIDs = getWatchlist($client, $export);

    if ($watchlistIDs == "Nothing in watchlist.") {
        array_push($returnData['watchlist'], "Nothing in watchlist.");
    } else {
        for ($i = 0; $i < count($watchlistIDs); $i++) {
            array_push($returnData['watchlist'], $watchlistIDs[$i]);
            $success = sendToPile($client, $export, $watchlistIDs[$i]);
            if ($success['success'] = true){
                array_push($returnData['sendPile'], $success['id']);
            }
        }
    }

    $readyCards = getTradePile($client, $export);
    $active = 0;
    $sold = 0;

    if ($readyCards == "Nothing in Trade Pile.") {
        array_push($returnData['tradepile'], "Noting in trade pile.");
    } else {
        for ($i = 0; $i < count($readyCards); $i++) {
            if ($readyCards[$i]['tradeState'] == "expired" OR($readyCards[$i]['tradeState'] == NULL && $readyCards[$i]['itemState'] == "free")) {
                listAuction($client, $export, $readyCards[$i]['id'], ($readyCards[$i]['lastSale'] + $bin), ($readyCards[$i]['lastSale']+$start));
            } else if ($readyCards[$i]['tradeState'] == "closed") {
                $sold++;
            } else if ($readyCards[$i]['tradeState'] == "active") {
                $active++;
                $returnData['forsale'][$i-$sold]['buyNowPrice'] = $readyCards[$i]['buyNowPrice'];
                $returnData['forsale'][$i-$sold]['currentBid'] = $readyCards[$i]['currentBid'];
                $returnData['forsale'][$i-$sold]['expire'] = $readyCards[$i]['expire'];
                $returnData['forsale'][$i-$sold]['startingBid'] = $readyCards[$i]['startingBid'];
            }
        }
        $returnData['tradepile']['active'] = $active;
        $returnData['tradepile']['sold'] = $sold;
    }
    $encode = json_encode($returnData);
    echo $encode;

    // get result: check result_decode. => then index ['watchlist']
    // Watchlist = could be 'Nothing in watchlist' or ID's in [watchlist][1],[2],[3] etc. use Count()
}

function getCardImg ($id){
    $key = "8D941B48-51BB-4B87-960A-06A61A62EBC0";
        $pic = "https://fifa15.content.easports.com/fifa/fltOnlineAssets/".$key."/2015/fut/items/images/players/web/".$id.".png";
        return $pic;

}

function quicksell($id){
    // POST : Override = Delete - POST /ut/game/fifa15/item/+ID   165713442433
    // Reply :: {"items":[{"id":165713442433}],"totalCredits":44627}
}