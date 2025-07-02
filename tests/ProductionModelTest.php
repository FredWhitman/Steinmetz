<?php

namespace Fred\Steinmetz\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use PDO;
use PDOStatement;

use Production\Models\ProductionModel;
use Production\utils\Util;

class ProductionModelTest extends TestCase
{
    private PDO $pdo;
    private LoggerInterface $logger;
    private ProductionModel $model;

    protected function setUp(): void
    {
        // 1) PDO & statement
        $this->pdo = $this->createMock(PDO::class);
        $stmt = $this->createMock(PDOStatement::class);
        $this->pdo->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);
        $this->pdo->method('lastInsertId')->willReturn(555);

        // 2) Logger
        $this->logger = $this->createMock(LoggerInterface::class);

        // 3) Util
        $this->util = $this->createMock(Util::class);

        // 4) ProductionModel with 3 dependencies
        $this->model = $this->getMockBuilder(ProductionModel::class)
            ->setConstructorArgs([
                $this->pdo,
                $this->logger,
                $this->util,       // ← added here
            ])
            ->onlyMethods([
                'getProdRunID',
                'getPrevProdLog',
                'insertProductionRun',
                'insertMatLog',
                'insertTempLog',
                'updateMatLogProdLogID',
                'updateTempLogProdLogID',
                'getMaterialLbs',
                'updateMaterialInventory',
                'insertTrans',
                'getInvQty',
                'updateProductInventory',
                'updateProductionRun',
                'updatePFMInventory'
            ])
            ->getMock();
    }


    public function testInsertProdLog_RunStatus1_StartsTransactionAndReturnsSuccess()
    {
        // Arrange: stub return values of helper methods
        $this->model->method('insertProductionRun')->willReturn(777);
        $this->model->method('insertMatLog')->willReturn(888);
        $this->model->method('insertTempLog')->willReturn(999);
        $this->model->method('getInvQty')->willReturn(1000);

        $prodData = [
            'productID'      => 'P123',
            'runStatus'      => '1',
            'prodDate'       => '2025-07-02',
            'pressCounter'   => 50,
            'startUpRejects' => 5,
            'qaRejects'      => 2,
            'purgeLbs'       => '0.5',
            'comments'       => 'All good'
        ];
        $materialData = ['mat1' => 'M1', 'matUsed1' => 3.2];
        $tempData     = ['temp1' => 'T1', 'tempVal1' => 180];

        // Expect transaction calls
        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->once())->method('commit');

        // Act
        $result = $this->model->insertProdLog(
            $prodData,
            $materialData,
            $tempData
        );

        // Assert
        $this->assertTrue($result['success']);
        $this->assertSame(555, $result['prodLogID']);
    }
}
