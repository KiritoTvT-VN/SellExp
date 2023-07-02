<?php
namespace SellExp;

use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\command\{Command, CommandSender};
use jojoe77777\FormAPI\{Form, FormAPI, SimpleForm, ModalForm, CustomForm};
use onebone\economy\EconomyAPI;

class Main extends PluginBase implements Listener {
	
	public function onEnable(): void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML , ["price" => 10000]);
		$this->getLogger()->info("Plugin SellExp Đã Hoạt Động");
	}
    
    public function onCommand(CommandSender $player, Command $cmd, string $label, array $args): bool {
		switch($cmd->getName()){
			case "sellexpui":
			$this->menu($player);
			return true;
		}
		return true;
	}
	
	public function menu($player){
		$price = $this->cfg->get("price");
		$form = new CustomForm(function(Player $player, $data){
			$result = $data;
			if($result==null){
				return;
			}
			if(is_numeric($data[0])){
				if($data[0] > 0){
					$exp = $player->getXpManager()->getXpLevel();
					if($exp >= $data[0]){
						$price = $this->cfg->get("price");
						$player->getXpManager()->setXpLevel($exp - $data[0]);
						$total = $price * $data[0];
						$this->eco->addMoney($player, $total);
						$player->sendMessage("§l§c• §eYou Sold ".$data[0]." Exp With Price ".$total." Money");
					}else{
						$player->sendMessage("§l§c• §eYou are not enough §a ".$data[0]." exp to sell");
						return true;
					}
				}else{
					$player->sendMessage("§l§c• §eYour Exp must be greater than 0");
					return true;
				}
			}else{
				$player->sendMessage("§l§c• §eExp Must Is A Number");
				return true;
			}
		});
		$price = $this->cfg->get("price");
		$exp = $player->getXpManager()->getXpLevel();
		$form->setTitle("§l§6♦ §dSell EXP §6♦");
		$form->addInput("§l§c⊳ §7Your Exp: ".$exp."\n§l§c⊳ §7Price:§e ".$price. " Money/1 Exp\n\n§l§c⊳ §7Enter Exp You Want To Sell:");
		$form->sendToPlayer($player);
	}
}