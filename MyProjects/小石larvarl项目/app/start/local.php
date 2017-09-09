<?php

 Event::listen('illuminate.query', function() {
 	//Log::info('SQL', func_get_args());
	//var_dump(func_get_args());
});