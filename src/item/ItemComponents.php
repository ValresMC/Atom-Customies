<?php
declare(strict_types=1);

namespace customiesdevs\customies\item;

use customiesdevs\customies\item\component\ItemComponent;
use pocketmine\nbt\tag\CompoundTag;

interface ItemComponents
{
	public function addComponent(ItemComponent $component): void;
	public function hasComponent(string $name): bool;
	public function getComponents(): CompoundTag;
}
