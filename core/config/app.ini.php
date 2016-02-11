;<?php die(); __halt_compiler(); ?>

[app]

base.uri		= "/area51/"
base.staticUri	= "//127.0.0.1/area51/"

title			= "Thunderhawk"
titleSeparator	= " - "

theme.main		= "main"
theme.partials	= "common"
theme.layouts	= "layouts"
theme.name		= "default"

;--------------+
; VOLT OPTIONS |
;--------------+
;An additional extension appended to the compiled PHP file
;----------------------------------------------------------------
volt.compiledExtension = ".compiled"
;----------------------------------------------------------------
;Volt replaces the directory separators / and \ by this separator 
;in order to create a single file in the compiled directory
;----------------------------------------------------------------
volt.compiledSeparator = "%%"
;----------------------------------------------------------------
;Whether Phalcon must check if exists differences between 
;the template file and its compiled path
;----------------------------------------------------------------
volt.stat = true
;----------------------------------------------------------------
;Tell Volt if the templates must be compiled in each request 
;or only when they change
;----------------------------------------------------------------
volt.compileAlways = false
;----------------------------------------------------------------
:Allows to prepend a prefix to the templates in the 
;compilation path
;----------------------------------------------------------------
volt.prefix = 
;----------------------------------------------------------------
;Enables globally autoescape of HTML
;----------------------------------------------------------------
volt.autoescape = false
;----------------------------------------------------------------