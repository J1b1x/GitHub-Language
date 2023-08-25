<?php
namespace Jibix\GitHubLanguage\task;
use GlobalLogger;
use Jibix\GitHubLanguage\event\LanguageReloadEvent;
use Jibix\GitHubLanguage\language\exception\LanguageException;
use Jibix\GitHubLanguage\language\exception\MissingLanguageValueException;
use Jibix\GitHubLanguage\language\Language;
use Jibix\GitHubLanguage\language\LanguageManager;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;


/**
 * Class LanguageReloadAsyncTask
 * @package Jibix\GitHubLanguage\task
 * @author Jibix
 * @date 22.08.2023 - 23:45
 * @project GitHub-Language
 */
class LanguageReloadAsyncTask extends AsyncTask{

    private const NOT_FOUND_ERROR = "404: Not Found";

    public function __construct(private string $url, private ?string $token = null){}

    public function onRun(): void{
        $headers = $this->token === null ? [] : ["Authorization: token " . $this->token];
        $languages = [];
        if (($body = Internet::getURL($this->url . "locales.txt", 10, $headers)?->getBody()) === self::NOT_FOUND_ERROR) throw new LanguageException("locales.txt file could not be found");
        foreach (explode("\n", $body) as $locale) {
            if (empty($locale)) continue;
            $data = Internet::getURL($this->url . $locale . ".json", 10, $headers)?->getBody();
            if ($data === null || $data === self::NOT_FOUND_ERROR) {
                GlobalLogger::get()->warning(LanguageManager::PREFIX . "Language file '$locale.json' could not be found");
                return;
            }
            try {
                $languages[] = Language::fromData(json_decode($data, true));
            } catch (MissingLanguageValueException $exception) {
                GlobalLogger::get()->warning(LanguageManager::PREFIX . "Error while loading '$locale.json': " . $exception->getMessage());
            }
        }
        $this->setResult($languages);
    }

    public function onCompletion(): void{
        if (empty($result = $this->getResult())) return;
        ($event = new LanguageReloadEvent($result))->call();
        if ($event->isCancelled()) return;

        $languages = $event->getLanguages();
        GlobalLogger::get()->info(LanguageManager::PREFIX . "§aSuccessfully loaded " . count($languages) . " languages");
        LanguageManager::getInstance()->addLanguage(...$languages);
    }
}