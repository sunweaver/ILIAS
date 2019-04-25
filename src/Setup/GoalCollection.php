<?php

/* Copyright (c) 2019 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\Setup;

use ILIAS\UI;

/**
 * A goal collection is a goal that is achieved once all subgoals are achieved.
 */
class GoalCollection implements Goal {
	/**
	 * @var string 
	 */
	protected $label;

	/**
	 * @var bool
	 */
	protected $is_notable;

	/**
	 * @var	Goal[]
	 */
	protected $goals;

	public function __construct(string $label, bool $is_notable, Goal ...$goals) {
		$this->label = $label;
		$this->is_notable = $is_notable;
		$this->goals = $goals;
	}

	/**
	 * @inheritdocs
	 */
	public function getHash() : string {
		return hash(
			"sha256",
			implode(
				array_map(
					function($g) { return $g->getHash(); },
					$this->goals
				)
			)
		); 
	}

	/**
	 * @inheritdocs
	 */
	public function getLabel() : string {
		return $this->label;
	}

	/**
	 * @inheritdocs
	 */
	public function isNotable() : bool {
		return $this->is_notable;
	}

	/**
	 * @inheritdocs
	 */
	public function withResourcesFrom(Environment $environment) : Goal {
		return new GoalCollection(
			$this->label,
			$this->is_notable,
			...array_map(
				function($g) use ($environment) { return $g->withResourcesFrom($environment); },
				$this->goals
			)
		);
	}

	/**
	 * @inheritdocs
	 */
	public function getPreconditions() : array {
		$pre = [];

		return array_unique(
			array_merge(
				...array_map(
					function ($g) { return $g->getPreconditions(); },
					$this->goals
				)
			),
			SORT_REGULAR
		);
	}

	/**
	 * @inheritdocs
	 */
	public function achieve(Environment $environment) {
		foreach ($this->goals as $g) {
			$g->achieve($environment);
		}	
	}
}