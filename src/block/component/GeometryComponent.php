<?php

declare(strict_types=1);

namespace customiesdevs\customies\block\component;

use customiesdevs\customies\block\component\BlockComponent;
use pocketmine\nbt\tag\CompoundTag;

final class GeometryComponent implements BlockComponent
{
	private string $geometry;

	public function __construct(string $geometry = "geometry.block") {
		$this->geometry = $geometry;
	}

	public function getName(): string {
		return "minecraft:geometry";
	}

	public function getValue(): CompoundTag {
		return CompoundTag::create()
			->setTag("bone_visibility", CompoundTag::create())
			->setString("culling", "")
			->setString("identifier", $this->geometry);
	}
}