<?php
$engine->translator->setLocale(Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']));