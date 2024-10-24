<?php

declare(strict_types=1);

namespace customiesdevs\customies\block\component;

use customiesdevs\customies\block\component\BlockComponent;
use pocketmine\nbt\tag\CompoundTag;

final class FlammableComponent implements BlockComponent
{
    private int $catchChance;
    private int $destroyChance;

    public function __construct(int $catchChance = 1, int $destroyChance = 5) {
        $this->catchChance = $catchChance;
        $this->destroyChance = $destroyChance;
    }

    public function getName(): string {
        return "minecraft:flammable";
    }

    public function getValue(): CompoundTag {
        return CompoundTag::create()
            ->setInt("catch_chance_modifier", $this->catchChance)
            ->setInt("destroy_chance_modifier", $this->destroyChance);
    }
}