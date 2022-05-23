<?php

namespace Asmodine\FrontBundle\Service;

use Asmodine\CommonBundle\Api\Client;
use Asmodine\CommonBundle\Exception\ApiException;
use Asmodine\FrontBundle\Entity\PhysicalProfile;
use Buzz\Message\MessageInterface;
use Buzz\Message\Response;
use Psr\Log\LoggerInterface;

/**
 * Class BackApiService.
 */
class BackApiService
{
    /** @var Client */
    private $api;

    /** @var LoggerInterface */
    private $logger;

    /**
     * FrontUpdateService constructor.
     *
     * @param Client          $api
     * @param LoggerInterface $logger
     */
    public function __construct(Client $api, LoggerInterface $logger)
    {
        $this->api = $api;
        $this->logger = $logger;
    }

    /**
     * @param PhysicalProfile $physicalProfile
     *
     * @return string
     */
    public function updatePhysicalProfile(PhysicalProfile $physicalProfile)
    {
        if (!$physicalProfile->checkRequiredValue()) {
            return 'Valeur requise incomplète';
        }

        $url = $this->api->prepareUrl('front_profile');
        $datas = $physicalProfile->getDTO();
        $this->logger->debug('Update Physical Profile ', ['url' => $url, 'datas' => json_encode($datas)]);
        try {
            $response = $this->update($url, ['physical_profile' => $datas]);

            return 'RESPONSE : '.$response->getContent();
        } catch (\Exception $e) {
            return 'EXCEPTION : '.$e->getMessage();
        }
    }

    /**
     * Submit Datas to Back.
     *
     * @param string $url
     * @param array  $datas
     *
     * @return MessageInterface
     */
    private function update(string $url, array $datas): MessageInterface
    {
        /** @var Response $response */
        $response = $this->api->submit($url, $datas);
        if ($response->getStatusCode() >= 300) {
            $this->logger->error(
                $response->getContent(),
                ['url' => $url, 'status_code' => $response->getStatusCode(), 'datas' => json_encode($datas)]
            );
            // Exception apparaît si les données sont soumises trop vite les unes derrières les autres
            // throw new ApiException($response->getContent(), $response->getStatusCode());
            return $response;
        }

        return $response;
    }
}
