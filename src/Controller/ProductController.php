<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\WalmartAuthService;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class ProductController extends AbstractController
{
    private $walmartAuthService;
    private $httpClient;

    public function __construct(WalmartAuthService $walmartAuthService, HttpClientInterface $httpClient)
    {
        $this->walmartAuthService = $walmartAuthService;
        $this->httpClient = $httpClient;
    }

    public function addItem(Request $request): Response
    {
        try {
            // Check if the request method is POST
            if ($request->isMethod('POST')) {
                $mpItem = $request->request->get('mpItem');

                if (!empty($mpItem)) {
                    // Write the posted item to feed.json
                    file_put_contents('feed.json', json_encode($mpItem, JSON_PRETTY_PRINT));
                }

                // Get the access token using the service
                $accessTokenResult = $this->walmartAuthService->getAccessToken();

                if ($accessTokenResult['status'] === 'success') {
                    // Access token retrieved successfully
                    $access_token = $accessTokenResult['access_token'];

                    // Set API URL
                    $url = 'https://marketplace.walmartapis.com/v3/feeds?feedType=MP_ITEM';

                    // Set HTTP Headers
                    $headers = [
                        'WM_SEC.ACCESS_TOKEN' => $access_token,
                        'WM_CONSUMER.CHANNEL.TYPE' => 'your-channel-type',
                        'WM_QOS.CORRELATION_ID' => uniqid(),
                        'WM_SVC.NAME' => 'Walmart Service Name',
                    ];

                    // Read the file
                    $feedFileName = 'feed.json';
                    $payload = file_get_contents($feedFileName);

                    // Make an HTTP POST request using Symfony's HttpClient
                    $response = $this->httpClient->request('POST', $url, [
                        'headers' => $headers,
                        'body' => $payload,
                    ]);

                    $responseCode = $response->getStatusCode();
                    $responseContent = $response->getContent();

                    if ($responseCode !== 200) {
                        if ($responseCode === 429) {
                            // Handle rate limit exceeded, e.g., add retry logic
                            $this->addFlash('error', 'Rate limit exceeded. Please try again later.');
                        } else {
                            $error_msg = 'Received HTTP status code ' . $responseCode;
                            $this->addFlash('error', $error_msg);
                        }
                    } else {

                        

                            // Add the feed ID to the success message
                            $this->addFlash('success', 'Item added successfully. Feed ID: ' . $responseContent);
                            $this->addFlash('success', 'Access token: ' . $access_token);
                        
                    }
                } else {
                    // Handle the error case
                    $error_message = $accessTokenResult['message'];
                    $this->addFlash('error', $error_message);
                }

                // Redirect to the same page
                return $this->redirectToRoute('product_add');
            }
        } catch (ClientExceptionInterface $e) {
            // Handle ClientExceptions (HTTP errors) here
            $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions here
            $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
        }

        return $this->render('product/add_item.html.twig');
    }
}
