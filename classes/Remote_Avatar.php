<?php

if (!defined('GNUSOCIAL')) {
        exit(1);
}

class Remote_Avatar extends Avatar
{
    public $remote_url;

    function displayUrl()
    {
        return $this->remote_url;
    }
}

