<?php
namespace Jibix\GitHubLanguage\util;
use Closure;
use Jibix\GitHubLanguage\language\Language;
use Jibix\GitHubLanguage\task\GoogleTranslateAsyncTask;
use pocketmine\Server;


/**
 * Class GoogleUtils
 * @package Jibix\GitHubLanguage\util
 * @author Jibix
 * @date 19.11.2023 - 01:41
 * @project GitHub-Language
 */
final class GoogleUtils{

    private function __construct(){}

    private const TEXT_LIMIT = 5000;
    public const URL = "https://translate.googleapis.com/translate_a/single?client=gtx&dt=t";

    public static function translate(Language $languageFrom, Language $languageTo, string $text, Closure $onComplete): void{
        Server::getInstance()->getAsyncPool()->submitTask(new GoogleTranslateAsyncTask(
            $languageFrom->getShortcut(),
            $languageTo->getShortcut(),
            $text,
            $onComplete
        ));
    }

    public static function translateRequest(Language|string $languageFrom, Language|string $languageTo, string $text): string{
        if (strlen($text) >= self::TEXT_LIMIT) return "§cText is too long";

        $fields = [
            'sl' => urlencode(is_string($languageFrom) ? $languageFrom : $languageFrom->getShortcut()),
            'tl' => urlencode(is_string($languageTo) ? $languageTo : $languageTo->getShortcut()),
            'q' => urlencode($text)
        ];

        $string = "";
        foreach ($fields as $key => $value) {
            $string .= '&' . $key . '=' . $value;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::URL);
        curl_setopt($curl, CURLOPT_POST, count($fields));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    public static function jsonToString(string $json): ?string{
        $arrays = json_decode($json, true);
        if (!$arrays) return null;
        $text = "";
        foreach ($arrays as $array) {
            if (!is_array($array)) continue;
            foreach ($array as $word) {
                $text .= $word[array_key_first($word)] ?? '';
            }
        }
        return $text;
    }
}