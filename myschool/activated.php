<?php
include_once './includes/header.php';

if (isset($_GET['token']) && $_GET['token'] != "") {
} else {
    Helper::showNotPermittedPage();
}

?>
<style>
    body {
        background-color: #95c2de;
    }

    .mainbox {
        background-color: #95c2de;
        margin: auto;
        height: 600px;
        width: 600px;
        position: relative;
    }

    .err {
        color: #ffffff;
        font-family: 'Nunito Sans', sans-serif;
        font-size: 11rem;
        position: absolute;
        left: 20%;
        top: 8%;
    }

    .far {
        position: absolute;
        font-size: 10.5rem;
        left: 42%;
        top: 15%;
        color: #ffffff;
    }

    .err2 {
        color: #ffffff;
        font-family: 'Nunito Sans', sans-serif;
        font-size: 11rem;
        position: absolute;
        left: 68%;
        top: 8%;
    }

    .msg {
        text-align: center;
        font-family: 'Nunito Sans', sans-serif;
        font-size: 1.6rem;
        position: absolute;
        left: 16%;
        top: 45%;
        width: 75%;
    }

    a {
        text-decoration: none;
        color: white;
    }

    a:hover {
        text-decoration: underline;
    }
</style>
<div class="mainbox">
    <div class="err"></div>
    <i class="far mdi mdi-check-circle"></i>
    <div class="err2"></div>
    <div class="msg">
        You have been fully activated!
        <p>Let's go <a href="index.php">home</a> now</p>
    </div>
</div>