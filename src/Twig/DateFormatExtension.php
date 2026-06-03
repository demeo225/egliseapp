<?php


namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DateFormatExtension extends AbstractExtension
{

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Appel de la methode $this->dateFormat.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('date_format', [$this, 'dateFormat']),
        ];
    }

    /**
     * Retourne le format de la date traduite.
     *
     * @param object $date   date
     * @param string $format format de la date
     */
    public function dateFormat(object $date, string $format = 'l d F Y'): string
    {
        return str_replace(
            [date_format($date, 'l'), date_format($date, 'F')],
            [$this->translator->trans(strtoupper(date_format($date, 'l'))), $this->translator->trans(strtoupper(date_format($date, 'F')))],
            date_format($date, $format)
        );
    }
}