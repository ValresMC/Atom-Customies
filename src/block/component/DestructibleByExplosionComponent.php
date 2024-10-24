<?php

declare(strict_types=1);

namespace customiesdevs\customies\block\component;

use customiesdevs\customies\block\component\BlockComponent;
use pocketmine\nbt\tag\CompoundTag;

final class DestructibleByExplosionComponent implements BlockComponent
{
    private float $resistance;

    public function __construct(float $resistance = 0.0) {
        $this->resistance = $resistance;
    }

    public function getName(): string {
        return "minecraft:destructible_by_explosion";
    }

    public function getValue(): CompoundTag {
        return CompoundTag::create()
            ->setFloat("explosion_resistance", $this->resistance);
    }
}