<?php
interface RequestInterface {
    public function getMethod(): string;
    public function getPath(): string;
    public function getBody(): array;
    public function getAuthorizationHeader(): ?string;
    public function getBearerToken(): ?string;
    public function getUser();
    public function setUser($user);
    public function getHeader($name);
}
