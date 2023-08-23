<?php
namespace GitHubLanguage\event;
use GitHubLanguage\language\Language;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;


/**
 * Class LanguageReloadEvent
 * @package GitHubLanguage\event
 * @author Jibix
 * @date 22.08.2023 - 23:49
 * @project GitHub-Language
 */
class LanguageReloadEvent extends Event implements Cancellable{
    use CancellableTrait;

    /**
     * LanguageReloadEvent constructor.
     * @param Language[] $languages
     */
    public function __construct(private array $languages){}

    public function getLanguages(): array{
        return $this->languages;
    }

    public function setLanguages(array $languages): void{
        $this->languages = $languages;
    }
}