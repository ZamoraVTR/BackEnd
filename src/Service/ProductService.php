<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;
use \PDO;


class ProductService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function getAll($adminUserId, $active = null, $categoryId = null, $orderBy = null)
    {
        //filtra por ativo ou inativo
        $whereClause = '';
        if ($active !== null) {
            $whereClause = "AND p.active = {$active}";
        }
        
        //filtra por categoria
        $categoryClause = '';
        if ($categoryId !== null) {
            $categoryClause = "AND c.id = {$categoryId}";
        }

        //ORDER BY pelo parâmetro passado
        $orderByClause = '';
        if ($orderBy !== null) {
            if ($orderBy === 'asc') {
                $orderByClause = "ORDER BY p.created_at ASC";
            } else {
                $orderByClause = "ORDER BY p.created_at DESC";
            }
        }
        
        $query = "
            SELECT p.*, GROUP_CONCAT(c.title) as categories
            FROM product p
            LEFT JOIN product_category pc ON pc.product_id = p.id
            LEFT JOIN category c ON c.id = pc.cat_id
            LEFT JOIN admin_user au ON au.id = {$adminUserId}
            WHERE p.company_id = au.company_id
            {$whereClause}
            {$categoryClause}
            GROUP BY p.id
            {$orderByClause}
        ";
        
        $stm = $this->pdo->prepare($query);
        $stm->execute();
        
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($id, $adminUserId)
    {
        $query = "
            SELECT company_id
            FROM admin_user
            WHERE id = :adminUserId
        ";

        $stm = $this->pdo->prepare($query);
        $stm->bindParam(':adminUserId', $adminUserId, PDO::PARAM_INT);
        $stm->execute();

        $adminUser = $stm->fetch(PDO::FETCH_ASSOC);

        if (!$adminUser) {
            return null; 
        }

        $query = "
            SELECT p.*, GROUP_CONCAT(c.title) as categories
            FROM product p
            LEFT JOIN product_category pc ON pc.product_id = p.id
            LEFT JOIN category c ON c.id = pc.cat_id
            WHERE p.id = :id
            AND p.company_id = :companyId
            GROUP BY p.id
        ";
        $stm = $this->pdo->prepare($query);
        $stm->bindParam(':id', $id, PDO::PARAM_INT);
        $stm->bindParam(':companyId', $adminUser['company_id'], PDO::PARAM_INT);
        $stm->execute();

        $product = $stm->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $product['categories'] = explode(',', $product['categories']);
            return $product;
        } else {
            return null; 
        }
    }

    public function insertOne($body, $adminUserId)
    {
        $stm = $this->pdo->prepare("
            INSERT INTO product (
                company_id,
                title,
                price,
                active
            ) VALUES (
                :company_id,
                :title,
                :price,
                :active
            )
        ");
        $stm->bindParam(':company_id', $body['company_id'], PDO::PARAM_INT);
        $stm->bindParam(':title', $body['title'], PDO::PARAM_STR);
        $stm->bindParam(':price', $body['price'], PDO::PARAM_STR);
        $stm->bindParam(':active', $body['active'], PDO::PARAM_INT);
    
        if (!$stm->execute())
            return false;
    
        $productId = $this->pdo->lastInsertId();
    
        //adiciona uma ou mais categorias na tabela product_category
        //o insert deve ser feito dentro de um array ex:[1] ou para mais de um [1,2]
        foreach ($body['category_id'] as $categoryId) {
            $stm = $this->pdo->prepare("
                INSERT INTO product_category (
                    product_id,
                    cat_id
                ) VALUES (
                    :product_id,
                    :category_id
                );
            ");
            $stm->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stm->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    
            if (!$stm->execute())
                return false;
        }
    
        $stm = $this->pdo->prepare("
            INSERT INTO product_log (
                product_id,
                admin_user_id,
                `action`
            ) VALUES (
                :product_id,
                :admin_user_id,
                'create'
            )
        ");
        $stm->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stm->bindParam(':admin_user_id', $adminUserId, PDO::PARAM_INT);
    
        if ($stm->execute()) {
            return $productId;
        } else {
             false;
        }
    }

    public function updateOne($id, $body, $adminUserId)
    {
        $stm = $this->pdo->prepare("
            UPDATE product
            SET company_id = {$body['company_id']},
                title = '{$body['title']}',
                price = {$body['price']},
                active = {$body['active']}
            WHERE id = {$id}
        ");
        if (!$stm->execute())
            return false;

        //exclui todas as categorias existentes do produto
        $stm = $this->pdo->prepare("
            DELETE FROM product_category WHERE product_id = {$id}
        ");
        if (!$stm->execute())
            return false;

        //insere as novas categorias selecionadas
        foreach ($body['category_id'] as $categoryId) {
            $stm = $this->pdo->prepare("
                INSERT INTO product_category (
                    product_id,
                    cat_id
                ) VALUES (
                    {$id},
                    {$categoryId}
                )
            ");
            if (!$stm->execute())
                return false;
        }

        //insere o registro de log
        $stm = $this->pdo->prepare("
            INSERT INTO product_log (
                product_id,
                admin_user_id,
                `action`
            ) VALUES (
                {$id},
                {$adminUserId},
                'update'
            )
        ");

        return $stm->execute();
    }

    public function deleteOne($id, $adminUserId)
    {
        $stm = $this->pdo->prepare("
            DELETE FROM product_category WHERE product_id = {$id}
        ");
        if (!$stm->execute())
            return false;
        
        $stm = $this->pdo->prepare("DELETE FROM product WHERE id = {$id}");
        if (!$stm->execute())
            return false;

        $stm = $this->pdo->prepare("
            INSERT INTO product_log (
                product_id,
                admin_user_id,
                `action`
            ) VALUES (
                {$id},
                {$adminUserId},
                'delete'
            )
        ");

        return $stm->execute();
    }
}
