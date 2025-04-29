<?php

namespace FBF\DPD;

use FBF\DPD\Label\LabelPdfProcessor;
use FBF\DPD\Request\PrintLabelRequest;
use FBF\DPD\Request\ShipmentRequest;
use FBF\DPD\Response\CancelResponse;
use FBF\DPD\Response\LabelResponse;
use FBF\DPD\Response\ShipmentResponse;
use FBF\DPD\Response\TrackResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class ShipmentClient
{
    private Client $httpClient;

    public function __construct(
        private readonly string $clientKey,
        private readonly string $email,
        private readonly string $endpoint,
        private readonly ?LoggerInterface $logger = null
    ) {
        $this->httpClient = new Client([
            'base_uri' => $this->endpoint,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 120,
            'connect_timeout' => 90,
        ]);
    }

    public function createShipment(ShipmentRequest $shipment): ShipmentResponse
    {
        return new ShipmentResponse($this->sendRequest('create', ['shipment' => [$shipment->toArray()]]));
    }

    private function sendRequest(string $method, array $params): array
    {
        $payload = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => array_merge([
                'DPDSecurity' => [
                    'SecurityToken' => [
                        'ClientKey' => $this->clientKey,
                        'Email' => $this->email,
                    ],
                ],
            ], $params),
            'id' => uniqid(),
        ];

        try {
            $response = $this->httpClient->post('', ['json' => $payload]);

            if (current($response->getHeader('content-type')) == 'application/pdf') {
                $data = $response->getBody()->getContents();
            } else {
                $data = json_decode($response->getBody()->getContents(), true);

                if (isset($data['error'])) {
                    $this->logger?->error("DPD API Error [{$method}]", $data['error']);

                    return ['success' => false, 'error' => $data['error']];
                }

                if (isset($data['data']['result']['result'][0]['success']) && $data['data']['result']['result'][0]['success'] === false) {
                    return [
                        'success' => false,
                        'ackCode' => $data['data']['result']['result'][0]['ackCode'] ?? null,
                        'messages' => $data['data']['result']['result'][0]['messages'] ?? [],
                        'reference' => $data['data']['result']['result'][0]['reference'] ?? null,
                    ];
                }
            }

            return ['success' => true, 'data' => $data];
        } catch (GuzzleException $e) {
            $this->logger?->error("DPD JSON transport error [{$method}]", ['exception' => $e]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function cancelShipment(string $shipmentReference): CancelResponse
    {
        return new CancelResponse($this->sendRequest('cancel', ['reference' => $shipmentReference]));
    }

    public function printLabel(PrintLabelRequest $printLabelRequest): LabelResponse
    {
        $response = $this->sendRequest('printLabels', ['label' => $printLabelRequest->toArray()]);

        return new LabelResponse($response);
    }

    public function trackShipment(string $shipmentReference): TrackResponse
    {
        return new TrackResponse($this->sendRequest('trackByReference', ['reference' => $shipmentReference]));
    }

    public function downloadAndProcessLabel(string $labelUrl, string $savePath = '/tmp/labels'): array
    {
        try {
            $response = $this->httpClient->get($labelUrl);
            $pdfContent = $response->getBody()->getContents();

            $processor = new LabelPdfProcessor($savePath);

            return $processor->splitAndExtractParcelNumbers($pdfContent);
        } catch (GuzzleException $e) {
            $this->logger?->error('Failed to download or process label PDF', [
                'labelUrl' => $labelUrl,
                'exception' => $e,
            ]);

            return [];
        }
    }
}
