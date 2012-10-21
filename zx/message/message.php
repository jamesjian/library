<?php

namespace Zx\Message;

class Message {

    public static function init_message() {
        $_SESSION['success_message'] = '';
        $_SESSION['error_message'] = '';
    }

    public static function set_error_message($message = '') {
        $_SESSION['error_message'] = $message;
        //\Zx\Test\Test::object_log('$_SESSION error', $_SESSION, __FILE__, __LINE__, __CLASS__, __METHOD__);
    }

    public static function get_error_message() {
        if (isset($_SESSION['error_message'])) {
            return $_SESSION['error_message'];
        } else {
            return '';
        }
    }

    public static function set_success_message($message) {
        $_SESSION['success_message'] = $message;
    }

    public static function get_success_message() {
        if (isset($_SESSION['success_message'])) {
            return $_SESSION['success_message'];
        } else {
            return '';
        }
    }

    public static function show_message() {
        //\Zx\Test\Test::object_log('$_SESSION', $_SESSION, __FILE__, __LINE__, __CLASS__, __METHOD__);

        if (isset($_SESSION['error_message']) && $_SESSION['error_message'] != '') {
            echo '<span class="zx-error-message">' . $_SESSION['error_message'] . '</span>';
        } else {
            if (isset($_SESSION['success_message']) && $_SESSION['success_message'] != '') {
                echo '<span class="zx-success-message">' . $_SESSION['success_message'] . '</span>';
            }
        }

        self::init_message();
    }

}