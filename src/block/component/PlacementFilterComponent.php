<?php

declare(strict_types=1);

namespace customiesdevs\customies\block\component;

use customiesdevs\customies\block\component\BlockComponent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

final class PlacementFilterComponent implements BlockComponent
{
    private array $block_filter;
	private ?array $allowed_faces;

	public function __construct(array $block_filter, ?array $allowed_faces = null) {
        $this->block_filter = $block_filter;
		$this->allowed_faces = $allowed_faces ?? ["all"];
	}

	public function getName(): string {
		return "minecraft:placement_filter";
	}

	public function getValue(): CompoundTag {
		return CompoundTag::create()
			->setTag("conditions", CompoundTag::create())
				->setTag("allowed_faces", new ListTag($this->allowed_faces))
				->setTag("block_filter", new ListTag($this->block_filter));
	}
}