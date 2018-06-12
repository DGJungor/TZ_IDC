<?php
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
class FooterController extends HomeBaseController
{
    public function about() {
        return $this->fetch("/".__FUNCTION__);
    }
    public function service() {
        return $this->fetch("/".__FUNCTION__);
    }
    public function disclaimer() {
        return $this->fetch("/".__FUNCTION__);
    }
    public function map() {
        return $this->fetch("/".__FUNCTION__);
    }
}