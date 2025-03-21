<?php
declare(strict_types=1);

namespace customiesdevs\customies\item;

use customiesdevs\customies\block\BlockComponentsTrait;
use InvalidArgumentException;
use pocketmine\block\Block;
use pocketmine\data\bedrock\item\BlockItemIdMap;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Utils;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use ReflectionClass;
use ReflectionException;
use function array_values;

final class CustomiesItemFactory
{
	use SingletonTrait;

	/** @var ItemTypeEntry[] */
	private array $itemTableEntries = [];

	/**
	 * Get a custom item from its identifier. An exception will be thrown if the item is not registered.
	 */
	public function get(string $identifier, int $amount = 1): Item {
		$item = StringToItemParser::getInstance()->parse($identifier);
		if($item === null) {
			throw new InvalidArgumentException("Custom item " . $identifier . " is not registered");
		}
		return $item->setCount($amount);
	}

	public function getItemTableEntries(): array {
		return array_values($this->itemTableEntries);
	}

	public function registerItem(string $className, string $identifier, string $name): void {
        if($className !== Item::class){
            Utils::testValidInstance($className, Item::class);
        }

		$itemId = ItemTypeIds::newId();
        /** @var Item & ItemComponents $item */
        $item = new $className(new ItemIdentifier($itemId), $name);
		$this->registerCustomItemMapping($identifier, $itemId);

		GlobalItemDataHandlers::getDeserializer()->map($identifier, fn() => clone $item);
		GlobalItemDataHandlers::getSerializer()->map($item, fn() => new SavedItemData($identifier));
        StringToItemParser::getInstance()->register($identifier, fn() => clone $item);

        $this->itemTableEntries[$identifier] = new ItemTypeEntry($identifier, $itemId, true, 1,
            new CacheableNbt($item->getComponents()
                ->setInt("id", $itemId)
                ->setString("name", $identifier))
        );

		CreativeInventory::getInstance()->add($item);
	}

	private function registerCustomItemMapping(string $identifier, int $itemId): void {
		$dictionary = TypeConverter::getInstance()->getItemTypeDictionary();
		$reflection = new ReflectionClass($dictionary);

		$intToString = $reflection->getProperty("intToStringIdMap");
		/** @var int[] $value */
		$value = $intToString->getValue($dictionary);
		$intToString->setValue($dictionary, $value + [$itemId => $identifier]);

		$stringToInt = $reflection->getProperty("stringToIntMap");
		/** @var int[] $value */
		$value = $stringToInt->getValue($dictionary);
		$stringToInt->setValue($dictionary, $value + [$identifier => $itemId]);
	}

    /**
     * @param string $identifier
     * @param Block & BlockComponentsTrait $block
     * @return void
     * @throws ReflectionException
     */
	public function registerBlockItem(string $identifier, Block $block): void {
		$itemId = $block->getIdInfo()->getBlockTypeId();
		$this->registerCustomItemMapping($identifier, $itemId);
		StringToItemParser::getInstance()->registerBlock($identifier, fn() => clone $block);

        $this->itemTableEntries[] = new ItemTypeEntry($identifier, $itemId, false, 2, new CacheableNbt(CompoundTag::create()));

		$blockItemIdMap = BlockItemIdMap::getInstance();
		$reflection = new ReflectionClass($blockItemIdMap);

		$itemToBlockId = $reflection->getProperty("itemToBlockId");
		/** @var string[] $value */
		$value = $itemToBlockId->getValue($blockItemIdMap);
		$itemToBlockId->setValue($blockItemIdMap, $value + [$identifier => $identifier]);

        CreativeInventory::getInstance()->add($block->asItem());
	}
}
