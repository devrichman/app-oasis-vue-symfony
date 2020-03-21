<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Route;

use App\Domain\Exception\NotFound;
use App\Domain\Model\FileDescriptor;
use App\Infrastructure\Config\EnvVarHelper;
use App\Infrastructure\Dao\FileDescriptorDao;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;
use function file_exists;
use function pathinfo;
use function Safe\file_get_contents;
use function Safe\parse_url;
use function strtolower;

final class GetFileController extends AbstractController
{
    protected const EXTENSION_MIMES = [
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
    ];

    private FileDescriptorDao $fileDescriptorDao;
    private EnvVarHelper $envVarHelper;

    public function __construct(FileDescriptorDao $fileDescriptorDao, EnvVarHelper $envVarHelper)
    {
        $this->fileDescriptorDao = $fileDescriptorDao;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @throws NotFound
     *
     * @Route("/file/serve/{id}")
     */
    public function serveFile(string $id): Response
    {
        $file = $this->fileDescriptorDao->mustFindOneById($id);

        $upstream = parse_url($file->getUpstream());
        if ($upstream['scheme'] === 'file' && empty($upstream['host'])) {
            $filepath = $this->envVarHelper->fetch('ROOT_PATH') . $upstream['path'];
            if (! file_exists($filepath)) {
                throw new NotFound(FileDescriptor::class, ['filename' => $file->getName()]);
            }

            $pathinfo = pathinfo($file->getName());
            $headers = [];
            if (isset($pathinfo['extension']) && isset(self::EXTENSION_MIMES[strtolower($pathinfo['extension'])])) {
                $headers = [
                    'Content-Type' => self::EXTENSION_MIMES[strtolower($pathinfo['extension'])],
                    'Content-Disposition' => 'inline; filename="' . $file->getName() . '"',
                ];
            } else {
                $headers = [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . $file->getName() . '"',
                ];
            }

            $content = file_get_contents($filepath);

            $response = new Response($content ?? '', 200, $headers);
            $response->setSharedMaxAge(3600 * 24 * 365);
            $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

            return $response;
        }

        throw new AccessDeniedException('Unsupported upstream', 500);
    }
}
