<?php

class shopHeaderWidget extends Widget {
    public function render($data) {
        
        
        return $this->renderFile("shopHeader", array("thisMonthAccount" => 1, "thisMonthOrderCount" => 2,
            "lastMonthAccount" => 2, "lastMonthOrderCount" => 2));
    }
}

