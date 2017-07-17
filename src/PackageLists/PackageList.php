<?php

namespace BlueSpice\Pkg\PackageLists;

abstract class PackageList {

	/**
	 * @return \BlueSpice\Pkg\Packages\Package[]
	 */
	abstract public function getPackages();
}
