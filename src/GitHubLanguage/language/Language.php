<?php
namespace GitHubLanguage\language;
use GitHubLanguage\language\exception\MissingLanguageValueException;
use JsonSerializable;
use pocketmine\player\Player;


/**
 * Class Language
 * @package GitHubLanguage\language
 * @author Jibix
 * @date 22.08.2023 - 23:44
 * @project GitHub-Language
 */
class Language implements JsonSerializable{

    public function __construct(
        protected string $locale,
        protected string $name,
        protected string $shortcut = "en",
        protected ?string $prefix = null,
        protected array $contributors = [],
        protected array $values = []
    ) {}

    public function getLocale(): string{
        return $this->locale;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getShortcut(): string{
        return $this->shortcut;
    }

    public function getPrefix(): ?string{
        return $this->prefix;
    }

    public function getContributors(): array{
        return $this->contributors;
    }

    public function getValues(): array{
        return $this->values;
    }

    public function getValue(int|string $key, string|array|null $default = null): string|array|null{
        return $this->values[$key] ?? $default;
    }

    public function isKey(string $key): bool{
        return isset($this->values[$key]);
    }

    public function translate(string $key, array $params = []): string{
        $key = str_replace("%", "", $key);
        if (!$this->isKey($key)) return $key;
        $value = $this->values[$key];
        if (count($params) > 0) {
            preg_match_all("/{(\d+)}/", $value, $matches, PREG_OFFSET_CAPTURE);
            foreach ($matches[1] as $match) {
                $i = intval($match[0]);
                $value = str_replace("{" . $i . "}", strval($values[$i] ?? "") ?? "\${$i}", $value);
            }
        }
        return str_replace("{PREFIX}", $this->prefix, $value);
    }

    public function jsonSerialize(): array{
        return [
            "locale" => $this->locale,
            "name" => $this->name,
            "shortcut" => $this->shortcut,
            "prefix" => $this->prefix,
            "contributors" => $this->contributors,
            "values" => $this->values,
        ];
    }

    /**
     * Function fromData
     * @param array $data
     * @return static
     * @throws MissingLanguageValueException
     */
    public static function fromData(array $data): static{
        foreach (["locale", "name", "shortcut"] as $required) {
            if (!isset($data[$required])) throw new MissingLanguageValueException("The language value $required could not be found in the language data");
        }
        return new static(
            $data['locale'],
            $data['name'],
            $data['shortcut'],
            $data['prefix'] ?? null,
            $data["contributors"] ?? [],
            $data["values"] ?? []
        );
    }


    public static function translateStatic(Player $player, string $text, array $params = []): string{
        $closure = LanguageManager::getInstance()->getPlayerLanguage();
        if ($closure === null) return $text;
        return ($closure)($player)->translate($text, $params);
    }
}