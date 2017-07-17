<?php

namespace BlueSpice\Pkg\Packages;

class Extension extends Package {
	protected function getTargetBasePath() {
		return 'extensions';
	}
}