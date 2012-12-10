<?php

namespace etobi\extensionUtils;

function register_autoload() {
	spl_autoload_register( function($class) {
				// Only attempt to load classes in our namespace
				if( substr( $class, 0, 21 ) !== 'etobi\\extensionUtils\\' ) {
					return;
				}

				$base = dirname( __DIR__ ) . DIRECTORY_SEPARATOR;
				$path = $base . 'lib' . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $class ) . '.php';
				if( is_file( $path ) ) {
					require_once $path;
				}
			} );
}