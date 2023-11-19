<?php
namespace Jibix\GitHubLanguage\task;
use Closure;
use Jibix\GitHubLanguage\util\GoogleUtils;
use pocketmine\scheduler\AsyncTask;


/**
 * Class GoogleTranslateAsyncTask
 * @package Jibix\GitHubLanguage\task
 * @author Jibix
 * @date 19.11.2023 - 01:43
 * @project GitHub-Language
 */
class GoogleTranslateAsyncTask extends AsyncTask{

    public function __construct(
        private string $languageFrom,
        private string $languageTo,
        private string $text,
        private Closure $onComplete
    ){}

    public function onRun(): void{
        $this->setResult(GoogleUtils::jsonToString(GoogleUtils::translateRequest($this->languageFrom, $this->languageTo, $this->text)));
    }

    public function onCompletion(): void{
        ($this->onComplete)($this->getResult());
    }
}