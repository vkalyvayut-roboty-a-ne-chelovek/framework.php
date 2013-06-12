<?php


class Controller_Test extends Controller
{
    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function action_index()
    {
        echo '<pre>';
        var_dump($this);
        echo '</pre>';
    }
}