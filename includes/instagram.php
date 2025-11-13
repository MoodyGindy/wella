<?php

declare(strict_types=1);

const INSTAGRAM_USERNAME = 'wellaresin_33';
const INSTAGRAM_CACHE_TTL = 1800; // 30 minutes

function fetch_instagram_posts(int $limit = 6): array
{
    $cacheFile = __DIR__ . '/../storage/cache/instagram.json';

    if (is_readable($cacheFile) && (time() - filemtime($cacheFile)) < INSTAGRAM_CACHE_TTL) {
        $cached = json_decode((string) file_get_contents($cacheFile), true);
        if (is_array($cached)) {
            return array_slice($cached, 0, $limit);
        }
    }

    $posts = request_instagram_posts($limit);

    if (!empty($posts)) {
        file_put_contents($cacheFile, json_encode($posts, JSON_PRETTY_PRINT));
    }

    return $posts;
}

function request_instagram_posts(int $limit = 6): array
{
    $endpoint = sprintf('https://www.instagram.com/%s/?__a=1&__d=dis', INSTAGRAM_USERNAME);

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0 Safari/537.36',
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Referer: https://www.instagram.com/',
        ],
    ]);

    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false || $statusCode !== 200) {
        curl_close($ch);
        return [];
    }

    curl_close($ch);

    $json = json_decode($response, true);

    $edges = $json['graphql']['user']['edge_owner_to_timeline_media']['edges'] ?? [];

    $posts = [];

    foreach ($edges as $edge) {
        if (!isset($edge['node'])) {
            continue;
        }

        $node = $edge['node'];
        $captionEdges = $node['edge_media_to_caption']['edges'] ?? [];
        $caption = $captionEdges[0]['node']['text'] ?? '';

        $posts[] = [
            'id' => $node['id'] ?? null,
            'permalink' => isset($node['shortcode']) ? sprintf('https://www.instagram.com/p/%s/', $node['shortcode']) : null,
            'image_url' => $node['display_url'] ?? null,
            'caption' => $caption,
        ];

        if (count($posts) >= $limit) {
            break;
        }
    }

    return $posts;
}


