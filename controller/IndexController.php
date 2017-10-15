<?php

namespace controller;

class IndexController extends BaseController {

    public function __construct() {

    }

    public function indexAction() {
        $this->render('index/index');
    }

}
