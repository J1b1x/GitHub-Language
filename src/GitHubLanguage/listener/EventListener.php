<?php
namespace GitHubLanguage\listener;
use GitHubLanguage\language\LanguageManager;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ServerSettingsResponsePacket;
use pocketmine\Server;


/**
 * Class EventListener
 * @package GitHubLanguage\listener
 * @author Jibix
 * @date 22.08.2023 - 23:42
 * @project GitHub-Language
 */
class EventListener implements Listener{

    public function onPacketSend(DataPacketSendEvent $event): void{
        $closure = LanguageManager::getInstance()->getPlayerLanguage();
        if ($closure === null) return;
        foreach ($event->getPackets() as $packet) {
            foreach ($event->getTargets() as $target) {
                if (($player = $target->getPlayer()) === null) continue;
                $language = ($closure)($player);
                if ($packet instanceof ModalFormRequestPacket || $packet instanceof ServerSettingsResponsePacket) {
                    $packet->formData = preg_replace_callback(
                        "/%([a-zA-Z0-9_.]+)/",
                        fn ($match): string => $language->translate($match[1]),
                        $packet->formData
                    );
                } elseif ($packet instanceof AvailableCommandsPacket && $player->spawned) {
                    foreach ($packet->commandData as $name => $data) {
                        $command = Server::getInstance()->getCommandMap()->getCommand($name);
                        if ($command !== null) $packet->commandData[$name]->description = $language->translate($command->getDescription());
                    }
                }
            }
        }
    }
}