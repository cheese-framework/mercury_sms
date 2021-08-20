<?php

include "./init.php";

new MyPusher();

$data['message'] = 'hello world';

MyPusher::send($data);
