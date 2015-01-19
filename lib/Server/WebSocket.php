<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 */


namespace Server;
use ZPHP\Core,
    ZPHP\Protocol;

class WebSocket
{
    public function run()
    {
        $server = Protocol\Factory::getInstance('Protocol\WebSocket');
        $server->parse($_REQUEST);
        return Core\Route::route($server);
    }

}