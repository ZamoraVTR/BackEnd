<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;

class CompanyService
{
    private \PDO $pdo;
    
    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function getCompanyName($id)
    {
        $stm = $this->pdo->prepare("SELECT name FROM company WHERE id = ?");
        $stm->execute([$id]);
        $result = $stm->fetch();
        
        if ($result) {
            return $result->name;
        } else {
            return null;
        }
    }
}
