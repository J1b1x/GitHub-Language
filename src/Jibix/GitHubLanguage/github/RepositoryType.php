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

    public static function PRIVATE(string $repository, string $token): self{
        return new self($repository, $token);
    }

    public static function PUBLIC(string $repository): self{
        return new self($repository);
    }

    public static function DETECT(string $repository, ?string $token = null): self{
        return empty($token) ? self::PUBLIC($repository) : self::PRIVATE($repository, $token);
    }


    private const REPOSITORY_URL = "https://raw.githubusercontent.com/";

    private string $url;

    private function __construct(string $repository, private ?string $token = null){
        $this->url = self::REPOSITORY_URL . $repository;
    }

    public function getUrl(): string{
        return $this->url;
    }

    public function getToken(): ?string{
        return $this->token;
    }
}