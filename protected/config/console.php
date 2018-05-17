<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.

//Yii::setPathOfAlias('webroot.images.mail', '/path/to/your/images/mail/dir');
/**Todo
 * 
 * Need to update the above line with the correct path 
 * and check the functionality.
 * 
 */

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Happy Sale',

	// preloading 'log' component
	'preload'=>array('log'),

	// application components
	'components'=>array(

		// database settings are configured in database.php
		'db'=>require(dirname(__FILE__).'/database.php'),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),

	),
);