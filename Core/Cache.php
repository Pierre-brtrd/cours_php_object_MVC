<?php

namespace App\Core;

class Cache
{
    public function __construct(
        private string $cacheDir = ROOT . '/cache/'
    ) {
        // Créer le dossier de cache s'il n'existe pas
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function get(string $key): mixed
    {
        $cacheFile = $this->getCacheFile($key);

        // Vérifier si le fichier de cache existe
        if (file_exists($cacheFile)) {
            // Lire les données depuis le fichier de cache
            $content = file_get_contents($cacheFile);

            // Désérialiser les données
            $data = unserialize($content);

            // Vérifier si le cache a expiré
            if ($data['expiration'] > time()) {
                return $data['value'];
            }

            // Supprimer le fichier de cache expiré
            unlink($cacheFile);
        }

        return null;
    }

    public function set(string $key, mixed $value, int $expiration = 3600): void
    {
        $cacheFile = $this->getCacheFile($key);

        // Sérialiser les données à mettre en cache
        $data = [
            'value' => $value,
            'expiration' => time() + $expiration,
        ];

        // Écrire les données sérialisées dans le fichier de cache
        file_put_contents($cacheFile, serialize($data), LOCK_EX);
    }

    public function clear(string $key): void
    {
        $cacheFile = $this->getCacheFile($key);

        // Supprimer le fichier de cache
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    private function getCacheFile(string $key): string
    {
        // Utiliser une clé unique pour chaque fichier de cache
        return $this->cacheDir . md5($key) . '.cache';
    }
}
