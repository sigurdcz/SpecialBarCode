<?php

namespace Sigurd\SpecialBarCode\Controller\Product;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Sigurd\SpecialBarCode\Logger\Logger;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class Attribute
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Logging instance
     * @var Logger
     */
    private $logger;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Logger $logger
     * @param RequestInterface $request
     * @param ResponseInterface $response
    */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Logger $logger,
        RequestInterface $request,
        ResponseInterface $response,
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * @param string $attributeValue
     * @return void
     */
    public function update($attributeValue)
    {
        // todo refactor to magento process
        $parts = explode('/', $_SERVER['REQUEST_URI']);
        $productId = end($parts);

        try {
            //check validate
            $errorMessages = $this->validate($attributeValue, $productId);
            if ($errorMessages) {
                throw new BadRequestException(json_encode($errorMessages), 1);
            }

            $product = $this->productRepository->getById($productId);
            $product->setData('special_bar_code', $attributeValue);
            $this->productRepository->save($product);

            $responseBody = [
                'success' => true,
                'message' => 'Product attribute updated successfully.',
                'productId' => $productId,
                'attributeValue' => $attributeValue
            ];
            $httpCode = 200;

            $this->logger->info('Product attribute updated.', ['productId' => $productId]);
        } catch (NoSuchEntityException $e) {
            $responseBody = ['success' => false, 'message' => 'Product not found.'];
            $httpCode = 404;

            $this->logger->error('Product not found.');
        } catch (Exception $e) {
            $responseBody = ['success' => false, 'message' => $e->getMessage()];
            $httpCode = 500;

            $this->logger->error($e->getMessage());
        }

        $response = $this->response;
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setStatusCode($httpCode);
        $response->setContent(json_encode($responseBody));

        $response->send();
        exit();
    }

    /**
     * @param string $attributeValue
     * @param string $productId
     * @return array
     */
    private function validate(string $attributeValue, string $productId): array
    {
        $messages = [];

        // Validate productId
        if (!is_numeric($productId) || $productId <= 0) {
            $messages[] = 'Invalid "productId"';
        }

        // Validate attributeValue
        if (empty($attributeValue)) {
            $messages[] = 'The "attributeValue" is required';
        }

        return $messages;
    }
}
