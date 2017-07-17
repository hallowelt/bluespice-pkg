<?php

namespace BlueSpice\Pkg\Packages;

abstract class Package {

	const DESCRIPTOR_SOURCE_TYPE = 'source-type';
	const DESCRIPTOR_SOURCE_URL = 'source-url';
	const DESCRIPTOR_GIT_BRANCH = 'git-branch';
	const DESCRIPTOR_TARBALL_BASEPATH = 'tarball-basepath';
	const DESCRIPTOR_COMPOSER_COMMAND = 'composer-command';
	const DESCRIPTOR_CONFIG_FILES = 'config-files';

	/**
	 *
	 * @var string
	 */
	protected $canonicalName = '';

	/**
	 *
	 * @var array
	 */
	protected $descriptor = [];

	/**
	 *
	 * @param string $canonicalName
	 * @param array $descriptor
	 */
	public function __construct( $canonicalName, $descriptor ) {
		$this->canonicalName = $canonicalName;
		$this->descriptor = $descriptor;;
	}

	/**
	 *
	 * @return string
	 */
	public function getTargetRelPath() {
		return $this->getTargetBasePath() . '/' . $this->canonicalName;
	}

	/**
	 *
	 * @return string
	 */
	public function getName() {
		return $this->canonicalName;
	}

	abstract protected function getTargetBasePath();
}