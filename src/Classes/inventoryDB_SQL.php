<?php
require_once 'database.php';
require __DIR__ . '/../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;


/**
 * 
 */
/**
 * inventoryDB_SQL
 */
class inventoryDB_SQL extends database
{
    private $log;
    // Constructor
    public function __construct()
    {
        $database = new Database();
        $db = $database->dbConnection();
        $this->con = $db;

        $this->log = new Logger('inventoryDB_SqlError');
        //send log errors to this fill
        $this->log->pushHandler(new StreamHandler(__DIR__ . '/logs/inventory_errors.log'), Logger::DEBUG);
        $this->log->pushHandler(new FirePHPHandler());

        //register the errorHandler
        ErrorHandler::register($this->log);
    }

    /**
     * getInventory
     * 
     * is called from inventoryActions and fills the datatable with product,material & PFMs
     *
     * @return void
     */
    public function getInventory()
    {
        $this->log->info('getIventory called!');

        try {
            $sqlProduct = 'SELECT 
                                products.productID AS productID,
                                productinventory.partQty AS partQty,
                                products.minQty AS minQty,
                                products.displayOrder AS displayOrder
                            FROM
                                productinventory
                            INNER JOIN
                                products ON (productinventory.productID = products.productID)
                            ORDER BY displayOrder';

            $stmtProduct = $this->con->prepare($sqlProduct);

            $stmtProduct->execute();

            $products = $stmtProduct->fetchALL(PDO::FETCH_ASSOC);
            $iCount = count($products);

            if (!$products) {
                $this->log->error('Nothing was returned $inventoryProducts.');
            } else {
                //$this->log->info('$inventoryProducts row count :' . $iCount);
            }

            $sqlMaterial = 'SELECT 
                            `materialinventory`.`matPartNumber`,
                            `materialinventory`.`matLbs`,
                            `material`.`matPartNumber`,
                            `material`.`matName`,
                            `material`.`productID`,
                            `material`.`minLbs`,
                            `material`.`displayOrder`
                            FROM
                            `materialinventory`
                            INNER JOIN `material` ON (`materialinventory`.`matPartNumber` = `material`.`matPartNumber`)
                            ORDER BY
                            `material`.`displayOrder`';
            $stmtMaterial = $this->con->prepare($sqlMaterial);
            $stmtMaterial->execute();
            $materials = $stmtMaterial->fetchALL(PDO::FETCH_ASSOC);
            $mCount = count($materials);

            //throw exception if $material is empty
            if (!$materials) {
                $this->log->error('Nothing was returned $materials.');
                throw new ErrorException(
                    'Failed to get Material!',
                    0,
                    E_ERROR,
                    '',
                );
            } else {
                //$this->log->info('$materials row count :' . $mCount);
            }

            $sqlPFM = 'SELECT 
                `pfm`.`pFMID`,
                `pfm`.`partNumber`,
                `pfm`.`partName`,
                `pfm`.`productID`,
                `pfm`.`minQty`,
                `pfm`.`customer`,
                `pfm`.`displayOrder`,
                `pfminventory`.`partNumber`,
                `pfminventory`.`Qty`
                FROM
                `pfminventory`
                INNER JOIN `pfm` ON (`pfminventory`.`partNumber` = `pfm`.`partNumber`)
                ORDER BY
                `pfm`.`displayOrder`';

            $stmtPFM = $this->con->prepare($sqlPFM);
            $stmtPFM->execute();
            $pfm = $stmtPFM->fetchAll(PDO::FETCH_ASSOC);
            $pCount = count($pfm);

            if (!$pfm) {
                $this->log->error('Nothing was returned $inventoryPFMs.');
                throw new ErrorException(
                    'Failed to get PFM',
                    0,
                    E_ERROR,
                    '',
                );
            } else {
                //$this->log->info('$inventoryPFM row count :' . $pCount);
            }
            $this->log->info('Returnin table data to controller!');
            return ['products'  => $products, 'materials' => $materials, 'pfms' => $pfm];

        } catch (PDOException $e) {
            $this->log->error("Error getting products: " . $e->getMessage());
            //Convert this to an uncaught exception to let ErrorHandler process it
            throw new ErrorException(
                $e->getMessage(),
                0,
                E_ERROR,
                $e->getFile(),
                $e->getLine()
            );
        }
    }

    /**
     * getRecord
     *  This function returns either a product, material or pfm record for editting.
     *
     * @param [type] $table //This is the table that will be queried
     * @param [type] $id  //record id used for the query
     * @return void
     */
    public function getRecord($id, $table)
    {
        $this->log->info('getRecord called with these parameters: ' . $id . ' ' . $table);
        $sql = '';

        if ($table === 'products') {
            $this->log->info('product record requested');
            $sql = 'SELECT * FROM products WHERE productID = :productID';
            $stmt = $this->con->prepare($sql);
            $stmt->execute([':productID' => $id]);
            $result = $stmt->fetch();
            if (!$result) {
                $this->log->warning("NO record found for the $id in table $table. ");
            }

            $this->log->info('getRecord returning : ' . $result['productID']);
            return $result;
        } else if ($table === 'materials') {
            $sql = 'SELECT * FROM material WHERE matPartNumber = :matPartNumber';
            try {
                $stmt = $this->con->prepare($sql);
                $stmt->execute([':matPartNumber' => $id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$result) {
                    $this->log->warning("NO record found for the $id in table $table. ");
                }
                $this->log->info('getRecord returning : ' . $result['matPartNumber']);
                return $result;
            } catch (PDOException $e) {
                $this->log->error("Error getting material record for $id in table $table.");
            }
        } else { //pfms
            $sql = 'SELECT * FROM pfm WHERE pFMID = :pFMID';
            $stmt = $this->con->prepare($sql);
            $stmt->execute([':pFMID' => $id]);
            $result = $stmt->fetch();
            if (!$result) {
                $this->log->warning("NO record found for the $id in table $table. ");
            }
            $this->log->info('getRecord returning : ' . $result['productID']);
            return $result;
        }
    }

    public function editInventory($data)
    {
        $this->log->info("editInventory : " .print_r($data,true));
        if($data['action'] === 'editProduct'){
            $this->log->info("edit product has begun!");

            //$this->log->info("data sent to editInventory function: ", $data);
            //$this->log->info('editInventory called and transaction begun.');
            //$this->log->info("Received partName value: " . $data['products']['partName']);
            $sql = "UPDATE products 
                        SET partName = :partName, 
                            minQty = :minQty, 
                            boxesPerSkid = :boxesPerSkid, 
                            partsPerBox = :partsPerBox, 
                            partWeight = :partWeight, 
                            displayOrder = :displayOrder, 
                            customer = :customer, 
                            productionType = :productionType 
                        WHERE productID = :productID";

            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':productID', $data['products']['productID'], PDO::PARAM_STR);
            $stmt->bindParam(':partName', $data['products']['partName'], PDO::PARAM_STR);
            $stmt->bindParam(':minQty', $data['products']['minQty'], PDO::PARAM_INT);
            $stmt->bindParam(':boxesPerSkid', $data['products']['boxesPerSkid'], PDO::PARAM_INT);
            $stmt->bindParam(':partsPerBox', $data['products']['partsPerBox'], PDO::PARAM_INT);
            $stmt->bindParam(':partWeight', $data['products']['partWeight'], PDO::PARAM_STR);
            $stmt->bindParam(':displayOrder', $data['products']['displayOrder'], PDO::PARAM_INT);
            $stmt->bindParam(':customer', $data['products']['customer'], PDO::PARAM_STR);
            $stmt->bindParam(':productionType', $data['products']['productionType'], PDO::PARAM_STR);

            $this->log->info("Executing update query with values: " . json_encode($data));

            try {
                $result = $stmt->execute();

                //check to make sure rows were actually affected
                $affectedRows = $stmt->rowCount();
                $this->log->info("Rows affected: " . $affectedRows);

                if (!$result) {
                    $errorInfo = $stmt->errorInfo();
                    $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
                    return ["success" => false, "message" => "Database update failed.", "error" => $errorInfo];
                }
                $this->log->info('Product Updated!');
                return ["success" => true, "message" => "Transaction completed successfully.", "product" => $data['products']['productID']];
            } catch (PDOException $e) {
                $this->log->error("ERROR updateInventory: " . $e->getMessage());
                return ["success" => false, "message" => "An error occurred", "error" => $e->getMessage()];
            }
        }else if($data["action"] === 'editMaterial'){
            $this->log->info('edit material has begun.');
            $sql = 'UPDATE material 
                        SET 
                            matName = :matName, 
                            productID = :productID, 
                            minLbs = :minLbs, 
                            matCustomer = :matCustomer, 
                            displayOrder = :displayOrder
                        WHERE matPartNumber = :matPartNumber';
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':matPartNumber', $data['materials']['matPartNumber'], PDO::PARAM_STR);
            $stmt->bindParam(':matName', $data['materials']['matName'], PDO::PARAM_STR);
            $stmt->bindParam(':productID', $data['materials']['productID'], PDO::PARAM_STR);
            $stmt->bindParam(':minLbs', $data['materials']['minLbs'], PDO::PARAM_STR);
            $stmt->bindParam(':matCustomer', $data['materials']['matCustomer'], PDO::PARAM_STR);
            $stmt->bindParam(':displayOrder', $data['materials']['displayOrder'], PDO::PARAM_INT);

            $this->log->info("Executing update query with values: " . json_encode($data));
            try {
                $result = $stmt->execute();
                //check to make sure rows were actually affected
                $affectedRows = $stmt->rowCount();
                $this->log->info("Rows affected: " . $affectedRows);
                 if (!$result) {
                    $errorInfo = $stmt->errorInfo();
                    $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
                    return ["success" => false, "message" => "Database update failed.", "error" => $errorInfo];
                }
                $this->log->info('Product Updated!');
                return ["success" => true, "message" => "Transaction completed successfully.", "material" => $data['materials']['matName']];
            } catch (PDOException $e) {
                $this->log->error("ERROR updateInventory: " . $e->getMessage());
                return ["success" => false, "message" => "An error occurred", "error" => $e->getMessage()];
            }
            
        }else{

        }
            
    }

    public function updateInvQty($data)
    {
        
        try {
            $sql = 'UPDATE product SET qty = qty - :qty WHERE productID = :productID';

            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':productID' , $data['productID'] , PDO::PARAM_STR);
            $result = $stmt->execute();;
            if(!$result){
                $errorInfo = $stmt->errorInfo();
                return["success" => false, "message" => "Database failed to update.", "error" => $errorInfo];

            }else{
                return ["success" => true, "message" => "Update successful!", "productID: " . $data['productID'] ];
            }

        } catch (PDOException $e){
            $this->log->error("ERROR updating inventory: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred", "error" => $e->getMessage()];
        }
    }
}
