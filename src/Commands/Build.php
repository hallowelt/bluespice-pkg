<?php

namespace BlueSpice\Pkg\Commands;

use BlueSpice\Pkg\PackageLists\LocalSMWJSON;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output\OutputInterface;

class Build extends Command {
	const ARG_TYPE = 'type';
	const ARG_SOURCE = 'source';

	const TYPE_DEV = 'dev';

	protected function configure() {
		$this
			->setName('build')
			->setDescription('Created a builds based on a given package list')
			->setDefinition( new Input\InputDefinition( [
				new Input\InputOption(
					self::ARG_TYPE,
					null,
					Input\InputOption::VALUE_REQUIRED,
					'Type of build',
					self::TYPE_DEV
				),
				new Input\InputOption(
					self::ARG_SOURCE,
					null,
					Input\InputOption::VALUE_REQUIRED,
					'Path or URL to the package list'
				)
			] ) )
			->set;
	}

	/**
	 *
	 * @var \Symfony\Component\Console\Output\Output
	 */
	protected $output = null;

	protected function execute( Input\InputInterface $input, OutputInterface $output ) {
		$this->output = $output;
		$this->type = $input->getOption( self::ARG_TYPE );
		$this->source = $input->getOption( self::ARG_SOURCE );

		$this->makePackageList();
		$this->buildByType();

		$output->writeln( '<info>Build complete.</info>' );
	}

	/**
	 *
	 * @var \BlueSpice\Pkg\PackageLists\PackageList
	 */
	protected $packageList = null;

	/**
	 *
	 * @var string
	 */
	protected $source = '';

	/**
	 *
	 * @var string
	 */
	protected $type = '';

	protected function makePackageList() {
		$this->packageList = new LocalSMWJSON( $this->source );
	}

	protected function buildByType() {
		$builder = null;
		switch( $this->type ) {
			case self::TYPE_DEV:
				$builder = new \BlueSpice\Pkg\Builder\Dev( $this->packageList );
				break;
			default:
				throw new Exception( 'Unknown type' );
		}

		$builder->getEventDispatcher()->addListener(
				\BlueSpice\Pkg\Builder\Builder::EVENT_START_BUILD_PACKAGE,
				function ( \Symfony\Component\EventDispatcher\GenericEvent $event ) {
					$this->output->writeln( "Building '{$event->getArgument('path')}'" );
				}
		);
		$builder->build();
	}
}