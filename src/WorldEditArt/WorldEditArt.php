<?php

/*
 * WorldEditArt
 *
 * Copyright (C) 2016 LegendsOfMCPE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author LegendsOfMCPE Team
 */

namespace WorldEditArt;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use WorldEditArt\Command\WorldEditArtCommand;
use WorldEditArt\DataProvider\DataProvider;
use WorldEditArt\DataProvider\SerializedDataProvider;
use WorldEditArt\Lang\LanguageManager;
use WorldEditArt\User\WorldEditArtUser;
use WorldEditArt\Utils\Fridge;

class WorldEditArt extends PluginBase{
	private static $PLUGIN_NAME = "WorldEditArt";

	/** @type LanguageManager */
	private $langMgr;
	/** @type Fridge */
	private $fridge;
	/** @type DataProvider */
	private $dataProvider;

	/** @type WorldEditArtUser[] */
	private $users = [];

	public function onLoad(){
		self::$PLUGIN_NAME = $this->getName();
	}

	public function onEnable(){
		$this->saveDefaultConfig();
		$this->langMgr = new LanguageManager($this);
		$this->fridge = new Fridge($this);
		$this->dataProvider = new SerializedDataProvider();
		new WorldEditArtCommand($this);
	}

	public function getResourceFolder(string $file = "") : string{
		return $this->getFile() . "resources/" . $file;
	}

	public function getResourceContents(string $path){
		$res = $this->getResource($path);
		if(is_resource($res)){
			$contents = stream_get_contents($res);
			fclose($res);
			return $contents;
		}
		return null;
	}

	public function getLanguageManager() : LanguageManager{
		return $this->langMgr;
	}

	public function getFridge() : Fridge{
		return $this->fridge;
	}

	public function getDataProvider() : DataProvider{
		return $this->dataProvider;
	}

	public static function getInstance(Server $server) : WorldEditArt{
		return ($instance = $server->getPluginManager()->getPlugin(self::$PLUGIN_NAME)) !== null and $instance->isEnabled() ?
			$instance : null;
	}

	public function translate(string $id, ...$langs) : string{
		return $this->getLanguageManager()->getTerm($id, ...$langs);
	}

	/**
	 * @param Player $player
	 *
	 * @return WorldEditArtUser|null
	 */
	public function getUser(Player $player){
		return $this->users[$player->getId()] ?? null;
	}
}