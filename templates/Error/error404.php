<?php
$this->disableAutoLayout();

?>
<title>
    Error 404
</title>

<?= $this->Html->meta('icon') ?>



<header>
    <button style="width: auto; margin: 10px" onclick="history.go(-1);">Go back</button>
</header>
<a href="" class="fa fa-arrow-left"></a>
<div class="error">
    <h1>404</h1>
    <p>We're sorry but it looks like that page doesn't exist anymore.</p>
</div>



<style>
    body,
    html {
        padding: 0;
        margin: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background: #810529;
        font-family: 'Montserrat', sans-serif;
        color: #fff
    }

    html {
        background: url('https://static.pexels.com/photos/818/sea-sunny-beach-holiday.jpg');
        background-size: cover;
        background-position: bottom
    }

    .error {
        text-align: center;
        padding: 16px;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
        -webkit-transform: translateY(-50%)
    }

    h1 {
        margin: -10px 0 -30px;
        font-size: calc(17vw + 40px);
        opacity: .8;
        letter-spacing: -17px;
    }

    p {
        opacity: .8;
        font-size: 20px;
        margin: 8px 0 38px 0;
        font-weight: bold
    }

    input,
    button,
    input:focus,
    button:focus {
        border: 0;
        outline: 0!important;
    }

    input {
        width: 300px;
        padding: 14px;
        max-width: calc(100% - 80px);
        border-radius: 6px 0 0 6px;
        font-weight: 400;
        font-family: 'Montserrat', sans-serif;
    }

    button {
        width: 40px;
        padding: 14.5px 16px 14.5px 12.5px;
        vertical-align: top;
        border-radius: 0 6px 6px 0;
        color: grey;
        background: silver;
        cursor: pointer;
        transition: all 0.4s
    }

    button:hover {
        color: white;
        background: #9A5C32
    }

    .fa-arrow-left {
        position: fixed;
        top: 30px;
        left: 30px;
        font-size: 2em;
        color:white;
        text-decoration:none
    }
</style>
