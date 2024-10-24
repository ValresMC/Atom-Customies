<?php

declare(strict_types=1);

namespace customiesdevs\customies\block;

use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\BlockStateDictionaryEntry;
use pocketmine\network\mcpe\convert\BlockTranslator;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\SingletonTrait;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;

final class BlockPalette
{
    use SingletonTrait;

    /** @var BlockStateDictionaryEntry[] */
    private array $states;
    /** @var BlockStateDictionaryEntry[] */
    private array $customStates = [];

    private BlockTranslator $translator;
    private ReflectionProperty $bedrockKnownStates;
    private ReflectionProperty $stateDataToStateIdLookup;
    private ReflectionProperty $idMetaToStateIdLookupCache;
    private ReflectionProperty $fallbackStateId;

    /** @throws ReflectionException */
    public function __construct() {
        $this->translator = $instance = TypeConverter::getInstance()->getBlockTranslator();
        $dictionary = $instance->getBlockStateDictionary();
        $this->states = $dictionary->getStates();

        $this->bedrockKnownStates = new ReflectionProperty($dictionary, "states");
        $this->stateDataToStateIdLookup = new ReflectionProperty($dictionary, "stateDataToStateIdLookup");
        $this->idMetaToStateIdLookupCache = new ReflectionProperty($dictionary, "idMetaToStateIdLookupCache");
        $this->fallbackStateId = new ReflectionProperty($instance, "fallbackStateId");
    }

    public function getStates(): array {
        return $this->states;
    }

    public function getCustomStates(): array {
        return $this->customStates;
    }

    public function insertState(CompoundTag $state, int $meta = 0): void {
        if(($name = $state->getString(BlockStateData::TAG_NAME, "")) === "") {
            throw new RuntimeException("Block state must contain a StringTag called 'name'");
        }
        if(($properties = $state->getCompoundTag(BlockStateData::TAG_STATES)) === null) {
            throw new RuntimeException("Block state must contain a CompoundTag called 'states'");
        }
        $this->sortWith($entry = new BlockStateDictionaryEntry($name, $properties->getValue(), $meta));
        $this->customStates[] = $entry;
    }

    private function sortWith(BlockStateDictionaryEntry $newState): void {
        $states = [];
        foreach($this->getStates() as $state){
            $states[$state->getStateName()][] = $state;
        }
        $states[$newState->getStateName()][] = $newState;

        $names = array_keys($states);
        usort($names, static fn(string $a, string $b) => strcmp(hash("fnv164", $a), hash("fnv164", $b)));
        $sortedStates = [];
        $stateId = 0;
        $stateDataToStateIdLookup = [];
        foreach($names as $name){
            foreach($states[$name] as $state){
                $sortedStates[$stateId] = $state;
                if(count($states[$name]) === 1) {
                    $stateDataToStateIdLookup[$name] = $stateId;
                }else $stateDataToStateIdLookup[$name][$state->getRawStateProperties()] = $stateId;
                $stateId++;
            }
        }
        $this->states = $sortedStates;
        $dictionary = $this->translator->getBlockStateDictionary();
        $this->bedrockKnownStates->setValue($dictionary, $sortedStates);
        $this->stateDataToStateIdLookup->setValue($dictionary, $stateDataToStateIdLookup);
        $this->idMetaToStateIdLookupCache->setValue($dictionary, null);
        $this->fallbackStateId->setValue($this->translator, $stateDataToStateIdLookup[BlockTypeNames::INFO_UPDATE] ??
            throw new AssumptionFailedError(BlockTypeNames::INFO_UPDATE . " should always exist")
        );
    }
}
