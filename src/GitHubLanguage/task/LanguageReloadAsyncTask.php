<?php
namespace GitHubLanguage\task;
use GitHubLanguage\event\LanguageReloadEvent;
use GitHubLanguage\language\exception\LanguageException;
use GitHubLanguage\language\exception\MissingLanguageValueException;
use GitHubLanguage\language\Language;
use GitHubLanguage\language\LanguageManager;
use GlobalLogger;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;


/**
 * Class LanguageReloadAsyncTask
 * @package GitHubLanguage\task
 * @author Jibix
 * @date 22.08.2023 - 23:45
 * @project GitHub-Language
 */
class LanguageReloadAsyncTask extends AsyncTask{

    public function __construct(private string $url){}

    public function onRun(): void{
        $languages = [];
        if (($body = Internet::getURL($this->url . "locales.txt")?->getBody()) === "404: Not Found") throw new LanguageException("locales.txt file could not be found");
        foreach (explode("\n", $body) as $locale) {
            if (empty($locale)) continue;
            $data = Internet::getURL($this->url . $locale . ".json")?->getBody();
            if ($data === null || $data === "404: Not Found") {
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