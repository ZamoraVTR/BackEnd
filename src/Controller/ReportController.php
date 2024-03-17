<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Service\CompanyService;
use Contatoseguro\TesteBackend\Service\ProductService;
use Contatoseguro\TesteBackend\Service\ReportService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReportController
{
    private ProductService $productService;
    private CompanyService $companyService;
    private ReportService $reportService;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->companyService = new CompanyService();
        $this->reportService = new ReportService();
    }

    public function generate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        try {
            //gera relatório
            $reportData = $this->reportService->generateReport($adminUserId);
        } catch (\Exception $e) {
            error_log("Erro ao gerar relatório: " . $e->getMessage());
            $response->getBody()->write("Erro ao gerar relatório. Por favor, tente novamente mais tarde.");
            return $response->withStatus(500);
        }

        //relatório do corpo da resposta
        $response->getBody()->write($reportData);
        return $response->withStatus(200)->withHeader('Content-Type', 'text/plain');
    }

    public function getLastPriceChange(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $productId = $args['id'];
    
            $adminUserId = $request->getHeaderLine('admin_user_id');
    
            //pega o nome do produto com base no ID
            $product = $this->productService->getOne($productId, $adminUserId);
            
            //valida se o produto foi encontrado
            if ($product === null) {
                $response->getBody()->write("Produto não encontrado.");
                return $response->withStatus(404); 
            }
            
            $productName = $product['title'];
            
            //último usuário que alterou o preço do produto
            //ex: last-price-change/4
            $lastPriceChangeUser = $this->reportService->getLastPriceChangeUserForProduct($productName);
    
            $responseData = "Último usuário que alterou o preço do produto $productName:\n";
            if ($lastPriceChangeUser) {
                $responseData .= "Nome do usuário: {$lastPriceChangeUser['Nome do usuário']}\n";
                $responseData .= "Data da alteração: {$lastPriceChangeUser['Data']}\n";
            } else {
                $responseData .= "Nenhuma alteração de preço encontrada para o produto $productName.\n";
            }
    
            //corpo da resposta
            $response->getBody()->write($responseData);
            return $response->withStatus(200)->withHeader('Content-Type', 'text/plain');
            } catch (\Exception $e) {
                error_log("Erro ao obter a última alteração de preço: " . $e->getMessage());
                $response->getBody()->write("Erro ao obter a última alteração de preço. Por favor, tente novamente mais tarde.");
                return $response->withStatus(500);
            }
    }
}