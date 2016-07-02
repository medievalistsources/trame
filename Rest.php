<?php

//error_reporting(E_ALL);ini_set("display_errors", 1);

include 'common.inc.php';
require_once 'appl/includes/XML2Array.php';

function connetti() {
    global $conn, $newconn;
    $newconn = new conn(LOGIN_STRING, DB_USER, DB_PASSWORD, DB_NAME);
    $conn = $newconn->getconn();
}

function sconnetti() {
    global $newconn;
    $newconn->dbclose();
}

function doSimpleSearch($req) {
    global $conn;
    connetti();
    $dbs = $req->dbs;
    $par['Field0'] = $req->freeText;
    $par['maxnum'] = 100;
    foreach ($dbs as $sito) {
        if ($sito == 127) {
            $par['maxnum'] = 128;
        }
        $par['dbs'] = $sito;

        $Search = new Freetext($conn, $par);
        $res = new ResultRest();
        $Search->attach($res);
        $Search->StartSearch();
        $results = $res->getLinks();
        $vett["site_" . $sito] = $results;
    }
    sconnetti();
    echo(json_encode($vett));
}

function doAdvancedSearch($req) {
    global $conn;
    connetti();
    $par['Field1'] = $req->title;
    $par['Field2'] = $req->author;
    $par[maxnum] = 100;
    $dbs = $req->dbs;
    foreach ($dbs as $sito) {

        if ($sito == 127) {
            $par['maxnum'] = 128;
        }
        $par['dbs'] = $sito;
        $Search = new AdvancedSearch($conn, $par);
        $res = new ResultRest();
        $Search->attach($res);
        $Search->StartSearch();
        $results = $res->getLinks();
        $vett["site_" . $sito] = $results;
    }
    sconnetti();
    echo json_encode($vett);
}
if(!isset($_REQUEST['q'])) {
    sendError();
    return;
}
$req = json_decode($_REQUEST["q"]);
$tipoRichiesta = $req->qtype;
if (!($tipoRichiesta === 'freetext') && !($tipoRichiesta === 'advanced')) { //TODO finire tipi
    sendError();
    return;
}

if ($tipoRichiesta === 'freetext') {

    doSimpleSearch($req);
}

if ($tipoRichiesta === 'advanced') {

    doAdvancedSearch($req);
}
?>
