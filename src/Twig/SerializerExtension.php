<?php

// src/Twig/SerializerExtension.php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class SerializerExtension extends AbstractExtension
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer) 
    {
        $this->serializer = $serializer;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('serialize', [$this, 'serialize']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('serialize', [$this, 'serialize']),
        ];
    }

 public function serialize($data, string $format = 'json', array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }
}