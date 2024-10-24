<?php

declare(strict_types=1);

namespace customiesdevs\customies\block\component;

use customiesdevs\customies\block\component\BlockComponent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

final class CraftingTableComponent implements BlockComponent
{
    private string $name;
    private int $size;
    private array $tags;

    public function __construct(string $name, int $size = 3, array $tags = ["table"]) {
        $this->name = $name;
        $this->size = $size;
        $this->tags = $tags;
    }

    public function getName(): string {
        return "minecraft:crafting_table";
    }

    public function getValue(): CompoundTag {
        return CompoundTag::create()
            ->setString("table_name", $this->name)
            ->setInt("grid_size", $this->size)
            ->setTag("crafting_tags", new ListTag($this->tags));
    }
}
