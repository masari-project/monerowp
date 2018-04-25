<?php
/**
 * Created by IntelliJ IDEA.
 * User: Cedric
 * Date: 25/04/2018
 * Time: 15:45
 */

function getPriceGromSouthxchange(){
	$msr_price = file_get_contents('https://www.southxchange.com/api/price/MSR/BTC');
	$price = json_decode($msr_price, TRUE);
	if($price !== null){
		return array($price["Last"],$price["Volume24Hr"]);
	}
	return array(null,null);
}

function getPriceFromTradeOgre(){
	$rawMakerts = file_get_contents('https://tradeogre.com/api/v1/markets');
	$markets = json_decode($rawMakerts, TRUE);
	if($markets !== null){
		foreach($markets as $market){
			$marketSymbol = array_keys($market)[0];
			if($marketSymbol === 'BTC-MSR'){
				$pricePerMsr = (float)$market[$marketSymbol]['price'];
				$volumeInBtc = (float)$market[$marketSymbol]['volume'];
				return array($pricePerMsr,$volumeInBtc/$pricePerMsr);
			}
		}
	}
	return array(null,null);
}


$totalVolume = 0;
$avgSatoshis = 0;

list($southxchangePrice,$southxchangeVolume) = getPriceGromSouthxchange();
list($tradeOgrePrice,$tradeogreVolume) = getPriceFromTradeOgre();

if($southxchangePrice !== null){
	$totalVolume += $southxchangeVolume;
	$avgSatoshis += $southxchangePrice;
}
if($tradeOgrePrice !== null){
	$totalVolume += $tradeogreVolume;
	$avgSatoshis += $tradeOgrePrice;
}

$msrSatoshiPrice = 99999;
if($totalVolume > 0){
	$msrSatoshiPrice = 0;
	if($southxchangePrice !== null) $msrSatoshiPrice += $southxchangePrice * ($southxchangeVolume / $totalVolume);
	if($tradeOgrePrice !== null) $msrSatoshiPrice += $tradeOgrePrice * ($tradeogreVolume / $totalVolume);

	var_dump($msrSatoshiPrice);
}