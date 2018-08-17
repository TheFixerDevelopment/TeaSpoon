<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

declare(strict_types = 1);

namespace CortexPE\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\level\{
	ChunkManager, generator\populator\Populator
};
use pocketmine\utils\Random;

class GroundFire extends Populator {
	/** @var ChunkManager */
	private $level;
	private $randomAmount;
	private $baseAmount;

	/**
	 * @param $amount
	 */
	public function setRandomAmount(int $amount){
		$this->randomAmount = $amount;
	}

	/**
	 * @param $amount
	 */
	public function setBaseAmount(int $amount){
		$this->baseAmount = $amount;
	}

	/**
	 * @param ChunkManager $level
	 * @param              $chunkX
	 * @param              $chunkZ
	 * @param Random $random
	 *
	 * @return mixed|void
	 */
	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random): void{
		$this->level = $level;
		$amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
		for($i = 0; $i < $amount; ++$i){
			$x = $random->nextRange(0, 15);
			$z = $random->nextRange(0, 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if($y !== -1 and $this->canGroundFireStay($x, $y, $z)){
				$this->level->setBlockIdAt($x, $y, $z, Block::FIRE);
				$this->level->setBlockLightAt($x, $y, $z, Block::get(Block::FIRE)->getLightLevel());
			}
		}
	}

	/**
	 * @param $x
	 * @param $z
	 *
	 * @return int
	 */
	private function getHighestWorkableBlock(int $x, int $z): bool{
		for($y = 0; $y <= 127; ++$y){
			$b = $this->level->getBlockIdAt($x, $y, $z);
			if($b == Block::AIR){
				break;
			}
		}

		return $y === 0 ? -1 : $y;
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 *
	 * @return bool
	 */
	private function canGroundFireStay(int $x, int $y, int $z): bool{
		$b = $this->level->getBlockIdAt($x, $y, $z);
		return ($b == Block::AIR) and $this->level->getBlockIdAt($x, $y - 1, $z) == Block::NETHERRACK;
	}
}