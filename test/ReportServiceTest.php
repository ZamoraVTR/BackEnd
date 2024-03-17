<?php

use PHPUnit\Framework\TestCase;
use Contatoseguro\TesteBackend\Service\ReportService;

class ReportServiceTest extends TestCase
{
    public function testGenerateReport()
    {
        $reportService = new ReportService();
    
        $adminUserId = 1;
    
        $report = $reportService->generateReport($adminUserId);
    
        //valida se o relatório não está vazio
        $this->assertNotEmpty($report);
    }

    public function testGetLastPriceChangeUserForProduct()
    {
        $reportService = new ReportService();

        //produto para verificação da última alteração de preço
        $productName = "iphone 8";

        $lastPriceChange = $reportService->getLastPriceChangeUserForProduct($productName);

        // valida se não está vazio
        $this->assertNotEmpty($lastPriceChange);

        //valida o formato da resposta
        $this->assertArrayHasKey('Nome do usuário', $lastPriceChange);
        $this->assertArrayHasKey('Data', $lastPriceChange);
    }
}