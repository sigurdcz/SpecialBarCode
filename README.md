# SpecialBarCode
Magento Module

## API Documentation: Update Product Attribute

### Description
This API endpoint allows you to update **SpecialBarCode** a product attribute using an HTTP POST request.

### Endpoint
**POST/** `sigurd_specialbarcode/product/attribute`

### Request Parameters

The following required parameters must be included in the request body in JSON format:

- `productId` (int): The ID of the product to update the attribute for.
- `attributeValue` (string): The value of the attribute to be updated.

### Responses

#### Successful Response

If the request is processed successfully, a response with HTTP status code `200` will be returned, along with the following data in JSON format:

```json
{
  "success": true,
  "message": "Product attribute updated successfully.",
  "productId": [Product ID]
}
```


#### Error Responses
If there is an error processing the request, the following error responses with the corresponding HTTP status codes will be returned:

- `400: Bad Request`. This error may occur if the required parameters (productId and attributeValue) were not provided, or if the parameters were provided in the wrong format.
- `404: Product Not Found`. This error occurs if the specified productId does not match any existing product.
- `500: Server Error`. This error occurs in case of an unexpected error while processing the request.

The error response will contain the following data in JSON format:
```json
{
  "success": false,
  "message": "[Error description]",
  "productId": [Product ID or empty]
}
```
POST http://localhost/sigurd_specialbarcode/product/attribute
`Content-Type: application/json`
```json
{
  "productId": 123,
  "attributeValue": "new_value"
}
```


#### Error Responses

If there is an error processing the request, the following error responses with the corresponding HTTP status codes will be returned:

- 400: Bad Request. This error may occur if the required parameters (productId and attributeValue) were not provided, or if the parameters were provided in the wrong format.
- 404: Product Not Found. This error occurs if the specified productId does
