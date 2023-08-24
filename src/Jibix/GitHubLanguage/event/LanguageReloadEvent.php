<?php
namespace Jibix\GitHubLanguage\event;
use Jibix\GitHubLanguage\language\Language;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;


/**
 * Class LanguageReloadEvent
 * @package Jibix\GitHubLanguage\event
 * @author Jibix
 * @date 22.08.2023 - 23:49
 * @project GitHub-Language
 */
class LanguageReloadEvent extends Event implements Cancellable{
    use CancellableTrait;

    /**
     * LanguageReloadEvent constructor.
     * @param \Jibix\GitHubLanguage\language\Language[] $languages
     */
    public function __construct(private array $languages){}

    public function getLanguages(): array{
        return $this->languages;
    }

    public function setLanguages(array $languages): void{
        $this->languages = $languages;
    }
}