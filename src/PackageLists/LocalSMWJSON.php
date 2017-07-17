<?php

namespace BlueSpice\Pkg\PackageLists;

use \BlueSpice\Pkg\Packages\Package;

class LocalSMWJSON extends PackageList {

	const FIELD_TYPE_SCALAR = 'scalar';
	const FIELD_TYPE_LIST = 'list';

	protected $source = '';
	public function __construct( $source ) {
		$this->source = $source;
	}

	/**
	 * @return \BlueSpice\Pkg\Packages\Package[]
	 */
	public function getPackages() {
		$data = json_decode( file_get_contents( $this->source ), true );
		$packages = [];
		foreach( $data['results'] as $subject => $info ) {
			$package = $this->makePackage( $info['printouts'] );
			if( $package instanceof Package ) {
				$packages[] = $package;
			}
		}

		return $packages;
	}

	/**
	 * array (
		'Destination' =>
			array (
			  0 => 'Widgets',
			),
		'Module type' =>
			array (
			  0 => 'Extension',
			),
		'Source type' =>
			array (
			  0 => 'git',
			),
		'Source' =>
			array (
			  0 => 'https://github.com/wikimedia/mediawiki-extensions-Widgets.git',
			),
		'Archive path' =>
			array (
			),
		'Version' =>
			array (
			  0 => 'REL1_27',
			),
		'Config file' =>
			array (
			),
		'Composer' =>
			array (
			),
	  )
	 */
	protected function makePackage( $info ) {
		$desc = [];
		$this->setConditionally(
			$desc,
			$info,
			Package::DESCRIPTOR_SOURCE_TYPE,
			'Source type'
		);
		$this->setConditionally(
			$desc,
			$info,
			Package::DESCRIPTOR_SOURCE_URL,
			'Source'
		);
		$this->setConditionally(
			$desc,
			$info,
			Package::DESCRIPTOR_COMPOSER_COMMAND,
			'Composer'
		);
		$this->setConditionally(
			$desc,
			$info,
			Package::DESCRIPTOR_GIT_BRANCH,
			'Version'
		);
		$this->setConditionally(
			$desc,
			$info,
			Package::DESCRIPTOR_TARBALL_BASEPATH,
			'Archive path'
		);
		$this->setConditionally(
			$desc,
			$info,
			Package::DESCRIPTOR_CONFIG_FILES,
			'Config file',
			self::FIELD_TYPE_LIST
		);

		$package = null;
		switch( $info['Module type'][0] ) {
			case 'Extension':
				$package = new \BlueSpice\Pkg\Packages\Extension(
					$info['Destination'][0], $desc
				);
				break;
			case 'Skin':
				$package = new \BlueSpice\Pkg\Packages\Skin(
					$info['Destination'][0], $desc
				);
				break;
			case 'Content':
				$package = new \BlueSpice\Pkg\Packages\Content(
					$info['Destination'][0], $desc
				);
				break;
		}

		return $package;
	}

	protected function setConditionally( &$desc, $info, $targetField, $sourceField, $sourceFieldType = self::FIELD_TYPE_SCALAR ) {
		if( isset( $info[$sourceField] ) && !empty(  $info[$sourceField] ) ) {
			if( $sourceFieldType === self::FIELD_TYPE_SCALAR ) {
				$desc[$targetField] = $info[$sourceField][0];
			} else {
				$desc[$targetField] = $info[$sourceField];
			}
		}
	}
}
