<?php
namespace Jibix\GitHubLanguage\github;


/**
 * Class RepositoryType
 * @package Jibix\GitHubLanguage\github
 * @author Jibix
 * @date 24.08.2023 - 17:44
 * @project GitHub-Language
 */
final class RepositoryType{

    private const REPOSITORY_URL = "https://raw.githubusercontent.com/";

    public static function PRIVATE(string $repository, string $token): self{
        return new self(self::REPOSITORY_URL . $repository, $token);
    }

    public static function PUBLIC(string $repository): self{
        return new self(self::REPOSITORY_URL . $repository);
    }


    public function __construct(private string $url, private ?string $token = null){
    }

    public function getUrl(): string{
        return $this->url;
    }

    public function getToken(): ?string{
        return $this->token;
    }
}