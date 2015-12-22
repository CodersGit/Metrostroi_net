<?php
header("HTTP/1.0 403 Forbidden");
$page_fucking_title = "Доступ запрещен";
include Mitrastroi::PathTPL("header");

include Mitrastroi::PathTPL("403");

include Mitrastroi::PathTPL("footer");