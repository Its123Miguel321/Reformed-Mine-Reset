<?php

namespace Its123Miguel321\MineReset;

use pocketmine\utils\TextFormat as TF;

class ResetSettings
{
    const PREFIX = TF::BOLD . TF::DARK_RED . 'MineReset' . TF::DARK_GRAY . ' » ' . TF::RESET;
    const WAND_NAME = TF::BOLD . TF::DARK_RED . 'MineReset ' . TF::WHITE . 'Wand' . TF::RESET;
    const FIRST_SELECTION = 0;
    const SECOND_SELECTION = 1;
    const RESET_INTERVAL = 200; // this is by seconds
}
