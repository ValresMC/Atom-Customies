<?php

declare(strict_types=1);

namespace customiesdevs\customies\block\component;

use customiesdevs\customies\block\component\BlockComponent;
use pocketmine\nbt\tag\CompoundTag;

final class LightEmissionComponent implements BlockComponent
{
	private int $emission;

	public function __construct(int $emission = 0) {
		$this->emission = $emission;
	}

	public function getName(): string {
		return "minecraft:light_emission";
	}

	public function getValue(): CompoundTag {
		return CompoundTag::create()
			->setByte("emission", $this->emission);
	}
}