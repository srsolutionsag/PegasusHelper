<?php
/**
 * File ChainRequestHandler.php
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */

namespace SRAG\PegasusHelper\handler;

/**
 * Class BaseHandler
 *
 * The base handler provides the implementation of a chain element which
 * is implemented by the concrete handlers.
 *
 * The motivation behind this approach is to eliminate the growing
 * switch statements in the plugin.
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 *
 * @see     chain of responsability
 */
interface ChainRequestHandler {

	/**
	 * Add a new chain link to the end of the chain.
	 *
	 * @param ChainRequestHandler $handler The chain element which should be added to the chain end.
	 *
	 * @return  void
	 */
	public function add(ChainRequestHandler $handler);


	/**
	 * The request handling logic of the chain link.
	 * This logic must call the next() unless it is responsible to handle
	 * the request.
	 *
	 * @return void
	 */
	public function handle();
}