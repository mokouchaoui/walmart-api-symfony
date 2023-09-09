<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client; // Add this line to import the GuzzleHttp\Client class
use GuzzleHttp\Exception\RequestException;
use Exception;
use App\Service\WalmartAuthService;

class WalmartController extends AbstractController
{
    /**
     * @var WalmartAuthService
     */
    private $walmartAuthService;

    public function __construct(WalmartAuthService $walmartAuthService)
    {
        $this->walmartAuthService = $walmartAuthService;
    }
    
    /**
     * @Route("/", name="walmart_items")
     */
    public function fetchItems(): Response
    {
        // Get the access token from the WalmartAuthService
        $result = $this->walmartAuthService->getAccessToken();
        
        if ($result['status'] !== 'success') {
            return $this->render('error_page.html.twig', [
                'message' => $result['message'],
            ]);
        }

        $accessToken = $result['access_token'];
        $client = new Client();

        try {
            $itemsEndpoint = 'https://marketplace.walmartapis.com/v3/items';
            $wmSvcName = 'Your-Walmart-Service-Name';
            $wmQosCorrelationId = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            $itemsResponse = $client->get($itemsEndpoint, [
                'headers' => [
                    'WM_SEC.ACCESS_TOKEN' => $accessToken,
                    'WM_CONSUMER.CHANNEL.TYPE' => 'Your-Channel-Type',
                    'WM_QOS.CORRELATION_ID' => $wmQosCorrelationId,
                    'WM_SVC.NAME' => $wmSvcName,
                    'Accept' => 'application/json',
                ]
            ]);

            $itemsBody = (string) $itemsResponse->getBody();
            $itemsData = json_decode($itemsBody, true);

        } catch (RequestException $e) {
            return $this->render('error_page.html.twig', [
                'message' => 'HTTP Error: ' . $e->getMessage(),
            ]);
        } catch (Exception $e) {
            return $this->render('error_page.html.twig', [
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }

        return $this->render('index.html.twig', [
            'items' => $itemsData['ItemResponse'] ?? [],
        ]);
    }
}
