<?php
    function createServerSocket($ip, $port)
    {
        $context = stream_context_create([
            'ssl' => [
                'local_cert' => 'certificates/server.crt',
                'local_pk' => 'certificates/privkey.key',
                'verify_peer' => false
            ]
        ]);
        return stream_socket_server("tls://$ip:$port", $errno, $errstr, STREAM_SERVER_BIND|STREAM_SERVER_LISTEN, $context);
    }

    function sendCommandToClient($clientSocket, $command)
    {
        $response = json_encode(['status' => 'success', 'command' => $command]);
        fwrite($clientSocket, $response);
    }
?>