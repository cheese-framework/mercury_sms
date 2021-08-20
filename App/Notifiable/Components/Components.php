<?php

namespace App\Notifiable\Components;

class Components
{
    const BTN_PRIMARY = 'primary';
    const BTN_SUCCESS = 'success';
    const DEFAULT_FONT_SIZE = '15px';

    private static function btn()
    {
        return "
            margin: 10px;
            text-align: center;
            font-weight: bold;
        ";
    }

    public static function header($text, $type = "h1", $align = "left")
    {
        $style = "margin-top: 0;
        font-weight: 300;
        text-transform: uppercase;
        letter-spacing: 1px;
        word-spacing: 4px;
        padding: 15px;
        font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, Open Sans, Helvetica Neue, sans-serif;
        margin-bottom:0;
        text-align: $align;
        ";
        return "<$type style='$style'>$text</$type>";
    }

    private static function btnPrimary()
    {
        $style = self::btn();
        $style .= " background-color: #4e6ebf;
            border: 1px solid #4e6ebf;
            color: #ffffff;
            display: inline-block;
            padding: 10px 30px;
            font-weight: 300;
            text-decoration: none;
            border-radius: 200px;
            font-weight: 700;
            transition: background-color 0.2s, border 0.2s, color 0.2s;
        ";
        return $style;
    }

    private static function card()
    {
        return "position: relative;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 3px;
        width: 90%;
        margin: 0 auto;
        padding: 10px;
        ";
    }


    public static function wrapper($content, $type = "div", $align = "left")
    {
        return "<$type style='" . self::card() . " text-align:$align'>$content</$type>";
    }

    private static function prettify($size = self::DEFAULT_FONT_SIZE)
    {
        $style = "font-size: $size;
        font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, Open Sans, Helvetica Neue, sans-serif;
        word-spacing: 1px;
        margin: 3px;
        -webkit-box-flex: 1;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        padding: .85rem;
        ";
        return $style;
    }

    private static function btnSuccess()
    {
        $style = self::btn();
        $style .= " background-color: #209b56;
            border: 1px solid #209b56;
            color: #fff;
            display: inline-block;
            padding: 10px 30px;
            font-weight: 300;
            text-decoration: none;
            border-radius: 200px;
            transition: background-color 0.2s, border 0.2s, color 0.2s;
            margin: 10px;
            font-weight: 700;
        ";
        return $style;
    }


    public static function button($value = "Click me", $style = self::BTN_PRIMARY)
    {
        if ($style == self::BTN_PRIMARY) {
            $button = "<button style='" . self::btnPrimary() . "'>$value</button>";
        } else if ($style == self::BTN_SUCCESS) {
            $button = "<button style='" . self::btnSuccess() . "'>$value</button>";
        } else {
            $button = "<button style='" . self::btnPrimary() . "'>$value</button>";
        }
        return $button;
    }

    public static function link($url, $value = "Click me", $style = self::BTN_PRIMARY, $target = '')
    {
        if ($style == self::BTN_PRIMARY) {
            $button = "<a href='$url' style='" . self::btnPrimary() . "'>$value</a>";
        } else if ($style == self::BTN_SUCCESS) {
            $button = "<a href='$url' style='" . self::btnSuccess() . "'>$value</a>";
        } else {
            $button = "<a href='$url' style='" . self::btnPrimary() . "' target='$target'>$value</a>";
        }
        return $button;
    }

    public static function body($text, $isHTML = TRUE, $align = "left")
    {
        if ($isHTML) {
            $text = nl2br($text);
        }
        return $text = "<p style='" . self::prettify() . ";text-align:$align'>$text</p>";
    }
}
