<?php
$voltFunctions = array(
		'_' => function(){
				if(count(func_get_args()) == 3){
					return ngettext(func_get_arg(0), func_get_arg(1),func_get_arg(2));
				}else{
					return gettext(func_get_arg(0));
				}
			},
		'gettext' => 'gettext'
);