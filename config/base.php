<?php

// BASE_URL already comes from db.php
// base.php should NEVER redefine it

if (!defined('BASE_URL')) {
    die("BASE_URL not defined. Make sure db.php is loaded first.");
}
