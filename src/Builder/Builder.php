<?php

namespace BlueSpice\Pkg\Builder;

use Symfony\Component\EventDispatcher\EventDispatcher as EventDispatcher;

class Builder {

	const EVENT_START_BUILD_PACKAGE = 'startBuildPackage';

	/**
	 *
	 * @var \BlueSpice\Pkg\PackageLists\PackageList
	 */
	protected $packageList = null;

	/**
	 *
	 * @var Symfony\Component\EventDispatcher
	 */
	protected $eventDispatcher = null;

	/**
	 *
	 * @param \BlueSpice\Pkg\PackageLists\PackageList $packageList
	 */
	public function __construct( $packageList ) {
		$this->packageList = $packageList;
		$this->eventDispatcher = new EventDispatcher();
	}

	/**
	 *
	 * @return Symfony\Component\EventDispatcher
	 */
	public function getEventDispatcher() {
		return $this->eventDispatcher;
	}

	public function build() {
		foreach( $this->packageList->getPackages() as $package ) {
			$this->getEventDispatcher()->dispatch(
				self::EVENT_START_BUILD_PACKAGE,
				new \Symfony\Component\EventDispatcher\GenericEvent( $this, [
					'name' => $package->getName(),
					'path' => $package->getTargetRelPath()
				] )
			);
			//TODO: The magic
		}
	}
}