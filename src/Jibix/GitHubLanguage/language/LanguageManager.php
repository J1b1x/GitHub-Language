<?php
namespace Jibix\GitHubLanguage\language;
use Closure;
use Jibix\GitHubLanguage\github\RepositoryType;
use Jibix\GitHubLanguage\listener\EventListener;
use Jibix\GitHubLanguage\task\LanguageReloadAsyncTask;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Utils;
use function GitHubLanguage\language\mb_strtolower;


/**
 * Class LanguageManager
 * @package Jibix\GitHubLanguage\language
 * @author Jibix
 * @date 22.08.2023 - 23:43
 * @project GitHub-Language
 */
final class LanguageManager{
    use SingletonTrait{
        setInstance as private;
        reset as private;
    }

    public const PREFIX = "§bLanguage§8 »§7 ";

    /** @var Language[] */
    private array $languages = [];

    public function __construct(
        Plugin $plugin,
        private RepositoryType $repository,
        private ?Closure $getPlayerLanguage = null,
        //Considering adding a fallback language?
    ){
        if ($this->getPlayerLanguage !== null) Utils::validateCallableSignature(function (Player $player): ?Language{}, $this->getPlayerLanguage);

        self::setInstance($this);
        Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $plugin);
        $this->reloadLanguages();
    }

    public function reloadLanguages(): void{
        Server::getInstance()->getAsyncPool()->submitTask(new LanguageReloadAsyncTask($this->repository->getUrl(), $this->repository->getToken()));
    }

    public function getLanguages(): array{
        return $this->languages;
    }

    public function getLanguage(string $locale): ?Language{
        return $this->languages[$locale] ?? null;
    }

    public function addLanguage(Language ...$languages): void{
        foreach ($languages as $language) {
            $this->languages[$language->getLocale()] = $language;
        }
    }

    public function getPlayerLanguage(): ?Closure{
        return $this->getPlayerLanguage;
    }

    /**
     * Use this if you want to get a language per string, usually you need to use the locale, this way you can use the shortcut or the name as well
     * Example: "/translate <from> <to>" command. If you use this function you can do "/translate <de> <en>" instead of "/translate <DE_DE> <EN_US>"
     */
    public function searchLanguage(string $string): ?Language{
        $string = mb_strtolower($string);
        foreach ($this->getLanguages() as $language){
            if (
                strtolower($language->getLocale()) === $string ||
                mb_strtolower($language->getShortcut()) === $string ||
                mb_strtolower($language->getName()) === $string
            ) return $language;
        }
        return null;
    }
}