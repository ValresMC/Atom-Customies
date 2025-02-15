<?php

declare(strict_types=1);

namespace customiesdevs\customies\block;

use customiesdevs\customies\block\component\BlockComponent;
use customiesdevs\customies\block\component\DestructibleByMiningComponent;
use customiesdevs\customies\block\component\DisplayNameComponent;
use customiesdevs\customies\block\component\FlammableComponent;
use customiesdevs\customies\block\component\FrictionComponent;
use customiesdevs\customies\block\component\GeometryComponent;
use customiesdevs\customies\block\component\LightDampeningComponent;
use customiesdevs\customies\block\component\LightEmissionComponent;
use customiesdevs\customies\block\component\MaterialComponent;
use customiesdevs\customies\block\component\SelectionBoxComponent;
use customiesdevs\customies\block\component\CollisionBoxComponent;
use pocketmine\block\Opaque;
use pocketmine\block\Transparent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;

trait BlockComponentsTrait
{
    /** @var BlockComponent[] */
    private array $components;

    public function addComponent(BlockComponent $component): void {
        $this->components[$component->getName()] = $component;
    }

    public function hasComponent(string $name): bool {
        return isset($this->components[$name]);
    }

    public function getPropertiesTag() : CompoundTag {
        return CompoundTag::create()->merge(CompoundTag::create())
            ->setTag("blockTags", new ListTag(array_map(fn(string $tag) => new StringTag($tag), $this->getTypeTags())));
    }

    public function getComponents(): CompoundTag {
        $componentsTags = CompoundTag::create();
        foreach($this->components as $component){
            $componentsTags = $componentsTags->setTag($component->getName(), $component->getValue());
        }
        return $componentsTags;
    }

    public function toPacket(): CompoundTag {
        return $this->getPropertiesTag()->setTag('components', $this->getComponents())
            ->setInt("molangVersion", 12)
            ->setTag("vanilla_block_data", CompoundTag::create()
                ->setInt("block_id", $this->getTypeId()));
    }

    protected function initComponent(string $texture): void {
        if($this->getName() !== "Unknown") $this->addComponent(new DisplayNameComponent($this->getName()));

        $this->addComponent(new GeometryComponent());
        $this->addComponent(match($this::class){
            Opaque::class => new MaterialComponent(MaterialComponent::TARGET_ALL, $texture, MaterialComponent::RENDER_METHOD_OPAQUE),
            Transparent::class => new MaterialComponent(MaterialComponent::TARGET_ALL, $texture, MaterialComponent::RENDER_METHOD_ALPHA_TEST, false)
        });
        if($this->hasEntityCollision()){
            $this->addComponent(new SelectionBoxComponent());
            $this->addComponent(new CollisionBoxComponent(collision: $this->isTransparent()));
        }
        $this->addComponent(new FrictionComponent($this->getFrictionFactor()));

        $this->addComponent(new LightEmissionComponent($this->getLightLevel()));
        $this->addComponent(new LightDampeningComponent($this->getLightFilter()));
        $this->addComponent(new DestructibleByMiningComponent($this->getBreakInfo()->getHardness()));

        if($this->getFlammability() > 0){
            $this->addComponent(new FlammableComponent($this->getFlameEncouragement()));
        }
    }

    public function setGeometry(string $geometry): void {
        $this->addComponent(new GeometryComponent($geometry));
    }
}