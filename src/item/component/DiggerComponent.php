<?php
declare(strict_types=1);

namespace customiesdevs\customies\item\component;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use function array_map;
use function implode;

final class DiggerComponent implements ItemComponent {

	private array $destroySpeeds;

	public function getName(): string {
		return "minecraft:digger";
	}

	public function getValue(): array {
		return [
			"destroy_speeds" => $this->destroySpeeds
		];
	}

	public function isProperty(): bool {
		return false;
	}

	public function withBlocks(int $speed, Block ...$blocks): self {
		foreach($blocks as $block){
			$this->destroySpeeds[] = [
				"block" => [
					"name" => GlobalBlockStateHandlers::getSerializer()->serialize($block->getStateId())->getName()
				],
				"speed" => $speed
			];
		}
		return $this;
	}

	public function withTags(int $speed, string ...$tags): self {
		$query = implode(",", array_map(fn($tag) => "'" . $tag . "'", $tags));
		$this->destroySpeeds[] = [
			"block" => [
				"tags" => "query.any_tag(" . $query . ")"
			],
			"speed" => $speed
		];
		return $this;
	}
}