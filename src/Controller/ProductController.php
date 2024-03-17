<?php

namespace Contatoseguro\TesteBackend\Controller;


use Contatoseguro\TesteBackend\Service\ProductService;
use Contatoseguro\TesteBackend\Service\CategoryService;
use Contatoseguro\TesteBackend\Service\ReportService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class ProductController
{
    private ProductService $service;
    private CategoryService $categoryService; 
    private ReportService $reportService;

    public function __construct()
    {
        $this->service = new ProductService();
        $this->categoryService = new CategoryService(); 
        $this->reportService = new ReportService();
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeaderLine('admin_user_id');

        // pega o ID da categoria, se fornecido na solicitação
        // ex: ?category_id=1- clothing , 2-phone, 3-computer, 4-furniture,5-food,6-house
        $categoryId = $request->getQueryParams()['category_id'] ?? null;

        // pega o parâmetro de filtro ativo/inativo, se fornecido na solicitação
        //ex:?active=1- ativo, 0 - inativo
        $activeFilter = $request->getQueryParams()['active'] ?? null;

        // pega o parâmetro de ordenação, se fornecido na solicitação
        //ex:?orderBy=desc , asc
        $orderBy = $request->getQueryParams()['orderBy'] ?? null;

        $active = null;

        if ($activeFilter !== null) {
            $active = (int)$activeFilter;
        }

        // obtém todos os produtos com base nos parâmetros fornecidos
        $products = $this->service->getAll($adminUserId, $active, $categoryId, $orderBy);

        $responseBody = [
            'products' => $products
        ];

        $response->getBody()->write(json_encode($responseBody));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];
        $product = $this->service->getOne($args['id'], $adminUserId);
    
        //valida se o produto foi encontrado
        if ($product) {
            $response->getBody()->write(json_encode($product));
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }

    public function insertOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if ($this->service->insertOne($body, $adminUserId)) {
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }

    public function updateOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $adminUserId = $request->getHeader('admin_user_id')[0];

        //pega o nome do produto atual
        $currentProduct = $this->service->getOne($args['id'], $adminUserId);
        $productName = $currentProduct['title'];

        if ($this->service->updateOne($args['id'], $body, $adminUserId)) {
            //chama getLastPriceChange com o nome do produto
            $this->reportService->getLastPriceChangeUserForProduct($productName);
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }

    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if ($this->service->deleteOne($args['id'], $adminUserId)) {
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }
}