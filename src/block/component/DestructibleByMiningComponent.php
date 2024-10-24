<?php

declare(strict_types=1);

namespace customiesdevs\customies\block\component;

use customiesdevs\customies\block\component\BlockComponent;
use pocketmine\nbt\tag\CompoundTag;

final class DestructibleByMiningComponent implements BlockComponent
{
    private float $seconds;

    public function __construct(float $seconds = 0.0) {
        $this->seconds = $seconds;
    }

    public function getName(): string {
        return "minecraft:destructible_by_mining";
    }

    public function getValue(): CompoundTag {
        return CompoundTag::create()
            ->setFloat("value", $this->seconds);
    }
}