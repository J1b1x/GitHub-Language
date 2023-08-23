<?php
namespace GitHubLanguage\language\util;
use Exception;
use GitHubLanguage\language\Language;


/**
 * Trait TranslatableTrait
 * @package GitHubLanguage\language\util
 * @author Jibix
 * @date 22.08.2023 - 23:50
 * @project GitHub-Language
 */
trait TranslatableTrait{

    protected ?Language $language = null;

    public function getLanguage(): Language{
        return $this->language ?? throw new Exception("Language has not been initialized yet");
    }

    public function setLanguage(Language $language): void{
        $this->language = $language;
    }

    public function translate(string $text, array $params = []): string{
        return $this->language->translate($text, $params);
    }
}