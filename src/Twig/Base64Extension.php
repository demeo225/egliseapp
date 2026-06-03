<?php



namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Base64Extension extends AbstractExtension
{
    
     public function getFunctions(): array
    {
        return [
            new TwigFunction('base64', [$this, 'base64']),
            new TwigFunction('file_get_contents', [$this, 'fileGetContents']),
        ];
    }

    private function options(): array
    {
        return [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
            
            "http" => array(
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
        )
            
        ];
    }

    public function base64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path, false, stream_context_create($this->options()));

        return 'data:image/'.$type.';base64,'.base64_encode($data);
    }

    public function fileGetContents(string $file)
    {
        return file_get_contents($file, false, stream_context_create($this->options()));
    }
}

