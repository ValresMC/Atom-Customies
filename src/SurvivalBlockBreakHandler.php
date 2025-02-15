<?php

declare(strict_types=1);

namespace customiesdevs\customies;

use pocketmine\block\Block;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockPunchParticle;
use pocketmine\world\sound\BlockPunchSound;

final class SurvivalBlockBreakHandler {

    public const DEFAULT_FX_INTERVAL_TICKS = 5;

    private int $fxTicker = 0;
    private float $breakSpeed;
    private float $breakProgress = 0;

    public function __construct(
        private readonly Player $player,
        private readonly Vector3 $blockPos,
        private readonly Block $block,
        private int $targetedFace,
        private readonly int $maxPlayerDistance,
        private readonly int $fxTickInterval = self::DEFAULT_FX_INTERVAL_TICKS
    )
    {
        $this->breakSpeed = $this->calculateBreakProgressPerTick();
        if($this->breakSpeed > 0){
            $this->player->getWorld()->broadcastPacketToViewers(
                $this->blockPos,
                LevelEventPacket::create(LevelEvent::BLOCK_START_BREAK, (int)(65535 * $this->breakSpeed), $this->blockPos)
            );
        }
    }

    private function calculateBreakProgressPerTick(): float {
        if(!$this->block->getBreakInfo()->isBreakable()){
            return 0.0;
        }

        $breakTimePerTick = $this->block->getBreakInfo()->getBreakTime($this->player->getInventory()->getItemInHand()) * 20;

        if($breakTimePerTick > 0){
            return 1 / $breakTimePerTick;
        }
        return 1;
    }

    public function update(): bool {
        if($this->player->getPosition()->distanceSquared($this->blockPos->add(0.5, 0.5, 0.5)) > $this->maxPlayerDistance ** 2){
            return false;
        }

        $newBreakSpeed = $this->calculateBreakProgressPerTick();
        if(abs($newBreakSpeed - $this->breakSpeed) > 0.0001){
            $this->breakSpeed = $newBreakSpeed;
        }

        $this->breakProgress += $this->breakSpeed;

        if(($this->fxTicker++ % $this->fxTickInterval) === 0 && $this->breakProgress < 1){
            $this->player->getWorld()->addParticle($this->blockPos, new BlockPunchParticle($this->block, $this->targetedFace));
            $this->player->getWorld()->addSound($this->blockPos, new BlockPunchSound($this->block));
            $this->player->broadcastAnimation(new ArmSwingAnimation($this->player), $this->player->getViewers());
        }

        return $this->breakProgress < 1;
    }

    public function getBlockPos(): Vector3 {
        return $this->blockPos;
    }

    public function getTargetedFace(): int {
        return $this->targetedFace;
    }

    public function setTargetedFace(int $face): void {
        Facing::validate($face);
        $this->targetedFace = $face;
    }

    public function getBreakSpeed(): float {
        return $this->breakSpeed;
    }

    public function getBreakProgress(): float {
        return $this->breakProgress;
    }

    public function __destruct() {
        if($this->player->getWorld()->isInLoadedTerrain($this->blockPos)){
            $this->player->getWorld()->broadcastPacketToViewers(
                $this->blockPos,
                LevelEventPacket::create(LevelEvent::BLOCK_STOP_BREAK, 0, $this->blockPos)
            );
        }
    }
}