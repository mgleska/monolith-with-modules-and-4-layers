<?php

declare(strict_types=1);

namespace App\Api;

use App\Api\Export\Exception\ApiProblemException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ApiProblemExceptionListener
{
    private LoggerInterface $logger;
    private ParameterBagInterface $parameterBag;

    public function __construct(LoggerInterface $logger, ParameterBagInterface $params)
    {
        $this->logger = $logger;
        $this->parameterBag = $params;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (! $exception instanceof ApiProblemException) {
            return;
        }

        $response = [
            'type' => $exception->getType(),
            'title' => $exception->getTitle(),
            'status' => $exception->getStatusCode(),
        ];
        if ($this->parameterBag->get('kernel.debug')) {
            $response['trace'] = $exception->getTrace();
        }

        $event->setResponse(
            new JsonResponse(
                $response,
                $exception->getStatusCode(),
                ['Content-Type' => 'application/problem+json']
            )
        );

        $this->logger->error('ApiProblemException: type: {type}, title: {title}, status: {status}, uri: {uri}, file: {file}, line: {line}, trace: {trace}',
            [
                'status' => $exception->getStatusCode(),
                'type' => $exception->getType(),
                'title' => $exception->getTitle(),
                'uri'  => $event->getRequest()->getUri(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => json_encode($exception->getTrace()),
            ]
        );
    }
}