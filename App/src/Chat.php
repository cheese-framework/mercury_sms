<?php


namespace MyApp;

date_default_timezone_set("Africa/Banjul");

use App\Core\Helper as CoreHelper;
use Chatroom;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../classes/Database.php";
require_once __DIR__ . "/../classes/Helper.php";
require_once __DIR__ . "/Chatroom.php";

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf(
            'Connection %d sending message "%s" to %d other connection%s' . "\n",
            $from->resourceId,
            $msg,
            $numRecv,
            $numRecv == 1 ? '' : 's'
        );

        $data = json_decode($msg, true);

        $data['msg'] = nl2br($data['msg']);
        $userData = CoreHelper::getStaffRecord($data['userId']);
        $userName = $userData->staff_name;
        $data['date'] = date("dS F, Y H:i:sa");

        $chatObj = new Chatroom();
        $chatObj->setUserId($data['userId']);
        $chatObj->setMessage($data['msg']);
        $chatObj->setSchoolId($data['school']);
        $chatObj->setCreatedOn($data['date']);

        $chatObj->saveChat();


        foreach ($this->clients as $client) {
            if ($from == $client) {
                $data['from'] = "Me";
            } else {
                $data['from'] = $userName;
            }
            $client->send(json_encode($data));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
