<?php

namespace App\Presentation;

use InvalidArgumentException;

class LinkCard
{
    private string $url;
    private string $title;
    private string $description;
    private string $domain;
    private array $metadata;

    public function __construct(string $url, string $title = '', string $description = '')
    {
        $this->url = $url;
        $this->title = $title ?: $this->extractTitleFromUrl($url);
        $this->description = $description ?: '访问链接了解更多信息';
        $this->domain = parse_url($url, PHP_URL_HOST) ?: $url;
        $this->metadata = [
            'keywords' => ['乐鱼体育', '体育资讯', '赛事动态'],
            'color' => '#1a73e8',
            'icon' => '🔗'
        ];
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    private function extractTitleFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $segments = explode('/', trim($path, '/'));
        $last = end($segments);
        return $last ?: '默认页面';
    }

    public function render(): string
    {
        $urlEsc = htmlspecialchars($this->url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $titleEsc = htmlspecialchars($this->title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $descEsc = htmlspecialchars($this->description, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $domainEsc = htmlspecialchars($this->domain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $color = htmlspecialchars($this->metadata['color'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $icon = htmlspecialchars($this->metadata['icon'], ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return <<<HTML
<div class="link-card" style="border-left:4px solid {$color};padding:12px 16px;margin:12px 0;background:#f9fafb;border-radius:6px;">
    <div class="link-card-icon">{$icon}</div>
    <a href="{$urlEsc}" target="_blank" rel="noopener noreferrer" style="font-weight:600;color:#1a0dab;text-decoration:none;">{$titleEsc}</a>
    <div class="link-card-domain" style="font-size:0.85em;color:#5f6368;margin-top:2px;">{$domainEsc}</div>
    <p style="margin:6px 0 0;color:#3c4043;font-size:0.95em;">{$descEsc}</p>
    <div class="link-card-tags" style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap;">
        <span style="background:#e8f0fe;color:#1967d2;padding:2px 8px;border-radius:12px;font-size:0.75em;">乐鱼体育</span>
        <span style="background:#fce8e6;color:#c5221f;padding:2px 8px;border-radius:12px;font-size:0.75em;">推荐</span>
    </div>
</div>
HTML;
    }

    public static function createDefault(): self
    {
        return new self(
            'https://mainportal-leyu.com.cn',
            '乐鱼体育 - 体育赛事平台',
            '乐鱼体育提供最新体育赛事资讯与动态，涵盖多种体育项目。'
        );
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['url'])) {
            throw new InvalidArgumentException('缺少 url 字段');
        }
        return new self(
            $data['url'],
            $data['title'] ?? '',
            $data['description'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
            'description' => $this->description,
            'domain' => $this->domain,
        ];
    }
}

function render_link_card(string $url, string $title = '', string $description = ''): string
{
    $card = new LinkCard($url, $title, $description);
    return $card->render();
}

function render_default_link_card(): string
{
    return LinkCard::createDefault()->render();
}