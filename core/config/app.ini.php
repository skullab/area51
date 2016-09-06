;<?php die(); __halt_compiler(); ?>

[app]

base.uri		= "/area51/"
;base.staticUri	= "//areariservata.iprovenzali.it/area51/"
base.staticUri	= "http://127.0.0.1/area51/"

title			= "IProvenzali"
titleSeparator	= " - "

theme.main		= "main"
theme.partials	= "common"
theme.layouts	= "layouts"
theme.name		= "default"

;-------------------------------------------------------------+
; SETTINGS DATABASE TABLES					  |
;-------------------------------------------------------------+ 
; Table name of controller's settings
;--------------------------------------------------------------
settings.controller = "controller_settings"
;--------------------------------------------------------------
; Table name of general's settings
;--------------------------------------------------------------
settings.general = "app_settings"
;--------------------------------------------------------------

;-------------------------------------------------------------+
; CRYPT SALT - DON'T CHANGE THIS VALUE ! 					  |
;-------------------------------------------------------------+
; Change this only the first time that Thunderhawk
; is installed, otherwise every encrypted data will be lost !
;--------------------------------------------------------------
crypt.salt		= "#8da9/7b064X.$lP"
;--------------------------------------------------------------

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
volt.compileAlways = true
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