<?php

declare(strict_types=1);

namespace customiesdevs\customies\block\components;

use customiesdevs\customies\block\component\BlockComponent;
use pocketmine\nbt\tag\CompoundTag;

final class BreathabilityComponent implements BlockComponent
{
    private string $breathability;

    const SOLID = "solid";
    const AIR = "air";

    public function __construct(string $breathability = self::SOLID) {
        $this->breathability = $breathability;
    }

    public function getName(): string {
        return "minecraft:breathability";
    }

    public function getValue(): CompoundTag {
        return CompoundTag::create()
            ->setString("value", $this->breathability);
    }
}
