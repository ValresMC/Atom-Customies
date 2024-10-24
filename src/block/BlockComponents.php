<?php

declare(strict_types=1);

namespace customiesdevs\customies\block;

use customiesdevs\customies\block\component\BlockComponent;

interface BlockComponents
{
    public function addComponent(BlockComponent $component): void;
    public function hasComponent(string $name): bool;

    public function getComponents(): array;
}