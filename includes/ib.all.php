<?php
foreach (glob(ICE_API_DIR . "/ib.*.php") as $filename)
    require_once $filename;