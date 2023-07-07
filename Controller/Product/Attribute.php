<?php

namespace Sigurd\SpecialBarCode\Controller\Product;

use BadMethodCallException;
use Exception;
use Laminas\Http\Request;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Sigurd\SpecialBarCode\Logger\Logger;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class Attribute extends Action implements CsrfAwareActionInterface
{
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
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param Logger $logger
     */
    public function __construct(
        Context                    $context,
        ProductRepositoryInterface $productRepository,
        Logger                     $logger
    )
    {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * Execute action
     */
    public function execute()
    {
        try {
            // check method
            if ($this->getRequest()->getMethod() != Request::METHOD_POST) {
                throw new BadMethodCallException("Method Not Allowed", 1);
            }

            $requestData = json_decode($this->getRequest()->getContent(), true);

            //check validate
            $errorMessages = $this->validate($requestData);
            if ($errorMessages) {
                throw new BadRequestException(json_encode($errorMessages), 1);
            }

            $attributeValue = isset($requestData['attributeValue']) ? $requestData['attributeValue'] : '';
            $productId = isset($requestData['productId']) ? $requestData['productId'] : '';

            $product = $this->productRepository->getById($productId);
            $product->setData('special_bar_code', $attributeValue);
            $this->productRepository->save($product);

            $response = ['success' => true, 'message' => 'Product attribute updated successfully.', 'productId' => $productId];
            $httpCode = 200;

            $this->logger->info('Product attribute updated.', ['productId' => $productId]);
        } catch (NoSuchEntityException $e) {
            $response = ['success' => false, 'message' => 'Product not found.'];
            $httpCode = 404;

            $this->logger->error('Product not found.');
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
            $httpCode = 500;

            $this->logger->error($e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setHttpResponseCode($httpCode);
        $this->getResponse()->setBody(json_encode($response));
    }

    /**
     * @param array $requestData
     * @return array
     */
    private function validate(array $requestData): array
    {
        $messages = [];
        $attributeValue = $requestData['attributeValue'] ?? '';
        $productId = (int)($requestData['productId'] ?? '');

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

    /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @param RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
