<?php

namespace socket;
use ZPHP\Socket\Callback\WSServer;
use ZPHP\Socket\Route;
use ZPHP\Core\Config as ZConfig;

class WebSocket extends WSServer
{
    public function wsOnOpen($fd, $data) 
    {
        echo "{$fd} connect success";
        $uid = (substr(strval(microtime(true)), 6, 7)*100)%1000000;
           if($uid<100000)
           {
               $uid += 100000; 
           }
           $this->send($fd, $data.pack("H*", '811e').'{"type":"welcome","id":'.$uid.'}', self::OPCODE_BINARY_FRAME);
           $this->_ws[$fd]['uid'] = $uid;
    }

    public function wsOnClose($fd)
    {
        $this->sendAll($fd, json_encode(array('type'=>'closed', 'id'=>$this->_ws[$fd]['uid'])));
    }

    
    public function sendAll($fd, $data)
    {
        foreach ( $this->_ws as $clid => $info )
        {
            //if( $fd != $clid )
            //{
                $this->send( $clid , $data);
            //}
        }
        
    }


    /**
    * 接收到消息时
    * @see WSProtocol::onSend()
    */
    public function wsOnMessage($fd, $ws)
    {
        $this->log("onSend: ".$ws['message']);
        $message_data = json_decode( $ws['message'] , true );
        if(!$message_data)
        {
            return ;
        }
        
        switch($message_data['type'])
        {
            // 更新用户
            case 'update':
                // 转播给所有用户
                $this->sendAll($fd, json_encode(
                        array(
                                'type'     => 'update',
                                'id'         => $this->_ws[$fd]['uid'],
                                'angle'   => $message_data["angle"]+0,
                                'momentum' => $message_data["momentum"]+0,
                                'x'                   => $message_data["x"]+0,
                                'y'                   => $message_data["y"]+0,
                                'life'                => 1,
                                'name'           => isset($message_data['name']) ? $message_data['name'] : 'Guest.'.$this->_ws[$fd]['uid'],
                                'authorized'  => false,
                                )
                        ));
                return;
            // 聊天
            case 'message':
                // 向大家说
                $new_message = array(
                    'type'=>'message', 
                    'id'=>$this->_ws[$fd]['uid'],
                    'message'=>$message_data['message'],
                );
                return $this->sendAll($fd, json_encode($new_message));
        }
    }

}
