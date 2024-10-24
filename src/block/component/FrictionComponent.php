<?php

declare(strict_types=1);

namespace customiesdevs\customies\block\component;

use customiesdevs\customies\block\component\BlockComponent;
use pocketmine\nbt\tag\CompoundTag;

final class FrictionComponent implements BlockComponent
{
	private float $friction;

	public function __construct(float $friction = 0.4) {
		$this->friction = $friction;
	}

	public function getName(): string {
		return "minecraft:friction";
	}

	public function getValue(): CompoundTag {
		return CompoundTag::create()
			->setFloat("value", $this->friction);
	}
}