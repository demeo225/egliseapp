<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('sum', [$this, 'calculateSum']),
        ];
    }

    public function calculateSum($array, $property)
    {
        if (!is_array($array) && !$array instanceof \Traversable) {
            return 0;
        }

        $sum = 0;
        foreach ($array as $item) {
            if (is_object($item)) {
                // Chercher la méthode getter
                $getter = 'get' . ucfirst($property);
                if (method_exists($item, $getter)) {
                    $sum += $item->$getter() ?? 0;
                } elseif (property_exists($item, $property)) {
                    $sum += $item->$property ?? 0;
                }
            } elseif (is_array($item) && isset($item[$property])) {
                $sum += $item[$property] ?? 0;
            }
        }
        
        return $sum;
    }
}