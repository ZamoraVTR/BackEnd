<?php
use PHPUnit\Framework\TestCase;
use Contatoseguro\TesteBackend\Service\ProductService;

class ProductServiceTest extends TestCase
{
    public function testGetAll()
    {
        // Arrange
        $adminUserId = 1; 
        $productService = new ProductService();

        // Act
        $products = $productService->getAll($adminUserId);

        // Assert
        $this->assertIsArray($products); // valida se retornou um array
        $this->assertNotEmpty($products); // valida se tem dados retornados
    }

    public function testGetOne()
    {
        // Arrange
        $productId = 1; 
        $adminUserId = 1;
        $productService = new ProductService();

        // Act
        $product = $productService->getOne($productId, $adminUserId);

        // Assert
        $this->assertIsArray($product); // valida se retornou um array
        $this->assertNotEmpty($product); // valida se tem dados retornados
    }

    public function testInsertOne()
    {
        // Arrange
        $productService = new ProductService();
        $productData = [
            'company_id' => 1,
            'title' => 'Novo Produto',
            'price' => 10.99,
            'active' => 1,
            'category_id' => [1]
        ];
        $adminUserId = 1;

        // Act
        $productId = $productService->insertOne($productData, $adminUserId);

        // converte o ID retornado para um inteiro
        $productId = (int) $productId;

        // Assert
        $this->assertIsInt($productId);
    }
}