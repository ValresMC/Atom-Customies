<?php

declare(strict_types=1);

namespace customiesdevs\customies\block\component;

use customiesdevs\customies\block\component\BlockComponent;
use pocketmine\nbt\tag\CompoundTag;

final class LootComponent implements BlockComponent
{
	private string $pathString;

	public function __construct(string $pathString) {
		$this->pathString = $pathString;
	}

	public function getName(): string {
		return "minecraft:loot";
	}

	public function getValue(): CompoundTag {
		return CompoundTag::create()
			->setString("value", $this->pathString);
	}
}