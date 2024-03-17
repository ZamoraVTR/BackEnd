<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;

class ReportService
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function generateReport($adminUserId)
    {
        $query = "
            SELECT 
                au.name AS 'Nome do usuário', 
                pl.action AS 'Tipo de alteração', 
                strftime('%d/%m/%Y %H:%M:%S', pl.created_at) AS 'Data'
            FROM 
                product_log pl
                INNER JOIN admin_user au ON pl.admin_user_id = au.id
            WHERE 
                au.id = :adminUserId
            ORDER BY 
                pl.created_at DESC
        ";
    
        $stm = $this->pdo->prepare($query);
        $stm->bindValue(':adminUserId', $adminUserId, \PDO::PARAM_INT);
        $stm->execute();
    
        $results = $stm->fetchAll(\PDO::FETCH_ASSOC);
    
        $report = '';
    
        //base do relatório
        $report .= "| Logs de Alterações |\n";
        $report .= "|---------------------|\n";
    
        foreach ($results as $row) {
            //registro de log
            $report .= "| ({$row['Nome do usuário']}, {$row['Tipo de alteração']}, {$row['Data']}) |\n";
        }
        return $report;
    }

    // getLastPriceChangeUserForProduct()
    // busca ultima alteração de preço
    public function getLastPriceChangeUserForProduct($productName)
    {
        $query = "
            SELECT 
                au.name AS 'Nome do usuário', 
                pl.created_at AS 'Data'
            FROM 
                product_log pl
                INNER JOIN admin_user au ON pl.admin_user_id = au.id
                INNER JOIN product p ON pl.product_id = p.id
            WHERE 
                pl.action = 'update'
                AND p.title = :productName
            ORDER BY 
                pl.created_at DESC
            LIMIT 
                1
        ";

        $stm = $this->pdo->prepare($query);
        $stm->bindValue(':productName', $productName, \PDO::PARAM_STR);
        $stm->execute();

        $result = $stm->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            //mensagem padrão para incluir o nome do produto
            $productName = ucfirst($productName);
            $result['message'] = "Último usuário que alterou o preço do produto $productName:\n";
            $result['message'] .= "Nome do usuário: {$result['Nome do usuário']}\n";
            $result['message'] .= "Data da alteração: {$result['Data']}\n";
        }

        return $result;
    }
}