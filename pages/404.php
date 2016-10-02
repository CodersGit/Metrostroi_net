<?php
header("HTTP/1.0 404 Not Found");
$show_login = true;
$page_fucking_title = "Страница не найдена";
include Mitrastroi::PathTPL("header");

include Mitrastroi::PathTPL("404");

include Mitrastroi::PathTPL("footer");